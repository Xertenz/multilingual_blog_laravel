<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ContentStatus;
use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleTranslation;
use App\Services\ContentNotificationDispatcher;
use DOMDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ArticleTranslationController extends Controller
{
    public function create(Request $request, int $article_id)
    {
        if (!$request->user() || !$request->user()->hasRole(['super-admin', 'editor', 'writer'])) {
            abort(403, 'غير مسموح لك بالوصول إلى هذه الصفحة.');
        }

        $article = Article::with('translations')->findOrFail($article_id);
        $article_content_imgs = $article->getMedia('content_imgs');

        // 🌐 اللغة المختارة مسبقاً من رابط ?locale=xx
        $selectedLocale = $request->query('locale');

        return view('admin.articles.create-trans', compact('article', 'article_content_imgs', 'selectedLocale'));
    }

    public function store(Request $request)
    {
        // 🔒 التحقق الأمني
        if (!$request->user() || !$request->user()->hasRole(['super-admin', 'editor', 'writer'])) {
            abort(403, 'غير مسموح لك بإنشاء ترجمة جديدة.');
        }

        // تحديد الحالة (status) بناءً على صلاحيات المستخدم
        // الكاتب (writer) ترسل ترجمته تلقائياً كـ pending للمراجعة، بينما المحرر/الأدمن يمكنهم نشرها أو حفظها كمسودة
        $status = 'pending';
        if ($request->user()->hasRole(['super-admin', 'editor']) || $request->user()->can('publish-articles')) {
            $status = $request->input('status', 'draft');
        }

        // 1. استخراج الـ slug أو الاعتماد على الـ title كخيار بديل
        $slugSource = trim($request->input('slug')) !== ''
            ? $request->input('slug')
            : $request->input('title');

        // 2. توليد الـ slug النهائي (يدعم الإنجليزية والعربية)
        $finalSlug = (string) Str::of($slugSource)->slug('-');
        $request->merge(['slug' => $finalSlug]);

        // جلب اللغات المدعومة ديناميكياً من مكتبة mcamara
        $supportedLocales = implode(',', LaravelLocalization::getSupportedLanguagesKeys());

        // 3. التحقق من البيانات (مع دعم حقل status الجديد)
        $validated = $request->validate([
            'article_id' => 'required|numeric|exists:articles,id',
            'locale' => [
                'required',
                'in:' . $supportedLocales,
                Rule::unique('article_translations', 'locale')->where(function ($query) use ($request) {
                    return $query->where('article_id', (int)$request->article_id);
                })
            ],
            'slug' => [
                'required',
                'string',
                Rule::unique('article_translations', 'slug')->where(function ($query) use ($request) {
                    return $query->where('locale', $request->locale);
                })
            ],
            'title' => "required|string|min:3",
            'description' => "required|string|min:20",
            'meta_title' => "required|string|min:3",
            'meta_description' => "required|string|min:20",
            'status' => 'sometimes|in:draft,pending,published',
        ]);

        try {
            // 4. إضافة الترجمة مع تسجيل user_id وحالة الـ status المستقلة لكل لغة
            $article_trans = ArticleTranslation::create([
                'article_id'       => $request->article_id,
                'user_id'          => $request->user()->id,
                'locale'           => $request->locale,
                'slug'             => trim($request->slug),
                'title'            => trim($request->title),
                'description'      => trim($request->description),
                'meta_title'       => trim($request->meta_title),
                'meta_description' => trim($request->meta_description),
                'views'            => 0,
                'status'           => $status,
            ]);

            // 5. التحقق وحجز notification_sent_at وإرسال الإشعار إذا اكتملت شروط النشر
            app(ContentNotificationDispatcher::class)->dispatchIfReady(
                translation: $article_trans,
                contentType: 'article',
                translationTable: 'article_translations',
                parentRelation: 'article',
                parentTable: 'articles',
                parentForeignKey: 'article_id',
            );

            //Cache::forget("article_show_{$article_trans->id}_lang_{$article_trans->locale}");
            return to_route('admin.articles.index')->with('success', 'Article Translation Added Successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to add article translation. Please try again.');
        }
    }


    public function edit(Request $request, int $article_trans_id)
    {
        $article_trans = ArticleTranslation::with('article', 'media')->findOrFail($article_trans_id);
        $article = $article_trans->article;

        // 🔒 التحقق من الصلاحية: مسموح للكاتب (ترجمته الخاصة)، والمحرر والمدير (الكل)
        if (!$request->user() || !$request->user()->hasRole(['super-admin', 'editor', 'writer'])) {
            abort(403, 'غير مسموح لك بتعديل الترجمات.');
        }

        if (!$request->user()->hasRole(['super-admin', 'editor']) && $article_trans->user_id !== $request->user()->id) {
            abort(403, 'غير مسموح لك بتعديل ترجمة لم تقم بإنشائها.');
        }

        $article_content_imgs = $article->getMedia('content_imgs');
        return view('admin.articles.edit-trans', compact('article_trans', 'article_content_imgs'));
    }

    public function update(Request $request, int $article_trans_id)
    {
        $article_trans = ArticleTranslation::findOrFail($article_trans_id);

        // 🔒 التحقق الأمني والملكية
        if (!$request->user() || !$request->user()->hasRole(['super-admin', 'editor', 'writer'])) {
            abort(403, 'غير مسموح لك بتحديث الترجمات.');
        }

        if (!$request->user()->hasRole(['super-admin', 'editor']) && $article_trans->user_id !== $request->user()->id) {
            abort(403, 'غير مسموح لك بتحديث ترجمة لم تقم بإنشائها.');
        }

        // تحديد الحالة عند التحديث
        $status = $article_trans->status instanceof ContentStatus
            ? $article_trans->status->value
            : (string) $article_trans->status;

        if ($request->user()->hasRole(['super-admin', 'editor']) || $request->user()->can('publish-articles')) {
            $status = $request->input('status', $status);
        } else {
            // إذا عدل الكاتب ترجمته، تعود تلقائياً لـ pending لتدقيقها مجدداً
            $status = ContentStatus::PENDING->value;
        }

        // 1. استخراج الـ slug
        $slugSource = trim($request->input('slug')) !== ''
            ? $request->input('slug')
            : $request->input('title');

        $finalSlug = (string) Str::of($slugSource)->slug('-');
        $request->merge(['slug' => $finalSlug]);

        $supportedLocales = implode(',', LaravelLocalization::getSupportedLanguagesKeys());

        $request->validate([
            'article_id' => 'required|numeric|exists:articles,id',
            'locale' => [
                'required',
                'in:' . $supportedLocales,
                Rule::unique('article_translations')->where(function ($query) use ($request) {
                    return $query->where('article_id', (int)$request->article_id);
                })->ignore($article_trans_id),
            ],
            'slug' => [
                'required',
                'string',
                Rule::unique('article_translations')->where(function ($query) use ($request) {
                    return $query->where('locale', $request->locale);
                })->ignore($article_trans_id)
            ],
            'title' => "required|string|min:3",
            'description' => "required|string|min:20",
            'meta_title' => "required|string|min:3",
            'meta_description' => "required|string|min:20",
            'status' => 'sometimes|in:draft,pending,published',
        ]);

        try {
            $article_trans->update([
                'locale'           => $request->locale,
                'slug'             => trim($request->slug),
                'title'            => trim($request->title),
                'description'      => trim($request->description),
                'meta_title'       => trim($request->meta_title),
                'meta_description' => trim($request->meta_description),
                'status'           => $status,
            ]);

            // التحقق وحجز notification_sent_at وإرسال الإشعار إذا أصبحت الترجمة منشورة لأول مرة
            app(ContentNotificationDispatcher::class)->dispatchIfReady(
                translation: $article_trans,
                contentType: 'article',
                translationTable: 'article_translations',
                parentRelation: 'article',
                parentTable: 'articles',
                parentForeignKey: 'article_id',
            );

            //Cache::forget("article_show_{$article_trans->id}_lang_{$article_trans->locale}");
            return to_route('admin.articles.index')->with('success', 'Article Translation Updated Successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update article translation. Please try again.');
        }
    }

    public function destroy(Request $request, int $article_trans_id)
    {
        $article_trans = ArticleTranslation::findOrFail($article_trans_id);

        // 🔒 التحقق من صلاحية الحذف
        if (!$request->user() || !$request->user()->hasRole(['super-admin', 'editor', 'writer'])) {
            abort(403, 'غير مسموح لك بحذف الترجمات.');
        }

        if (!$request->user()->hasRole(['super-admin', 'editor']) && $article_trans->user_id !== $request->user()->id) {
            abort(403, 'غير مسموح لك بحذف ترجمة لم تقم بإنشائها.');
        }

        //Cache::forget("article_show_{$article_trans->id}_lang_{$article_trans->locale}");

        $article_trans->delete();
        return to_route('admin.articles.index')->with('success', 'Article Translation Deleted Successfully.');
    }
}
