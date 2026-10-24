<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleTranslation;
use DOMDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Support\Str;

class ArticleTranslationController extends Controller
{
    public function create(Request $request, int $article_id)
    {
        // 🔒 التحقق الأمني: مسموح للكاتب، المحرر، والمدير بإنشاء التراجم والمحتوى
        if (!$request->user() || !$request->user()->hasRole(['super-admin', 'editor', 'writer'])) {
            abort(403, 'غير مسموح لك بالوصول إلى هذه الصفحة.');
        }

        $article = Article::findOrFail($article_id);
        $article_content_imgs = $article->getMedia('content_imgs');
        return view('admin.articles.create-trans', compact('article_id', 'article_content_imgs'));
    }

    public function store(Request $request)
    {
        // 🔒 التحقق الأمني
        if (!$request->user() || !$request->user()->hasRole(['super-admin', 'editor', 'writer'])) {
            abort(403, 'غير مسموح لك بإنشاء ترجمة جديدة.');
        }

        // تحديد الحالة (status) بناءً على صلاحيات المستخدم
        // الكاتب (writer) ترسل ترجمته تلقائياً كـ pending للمراجعة، بينما المححر/الأدمن يمكنهم نشرها أو حفظها كمسودة
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
                'locale'           => $request->locale,
                'slug'             => trim($request->slug),
                'title'            => trim($request->title),
                'description'      => trim($request->description),
                'meta_title'       => trim($request->meta_title),
                'meta_description' => trim($request->meta_description),
                'views'            => 0,
                'user_id'          => $request->user()->id, // 👈 ربط الترجمة بالمستخدم الحالي (الكاتب أو المحرر)
                'status'           => $status,              // 👈 حالة الترجمة المستقلة
            ]);

            // 5. إرسال الإشعارات إذا كانت الترجمة والمقال منشرين بالفعل
            $article = $article_trans->article;
            if ($article && $article->status === 'published' && $article_trans->status === 'published' && $article->category) {
                \App\Jobs\SendNewArticleNotificationJob::dispatch($article_trans)->onQueue('default');
            }

            //Cache::forget("article_show_{$article_trans->id}_lang_{$article_trans->locale}");
            return to_route('admin.articles.index')->with('success', 'Article Translation Added Successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to add article translation. Please try again.');
        }
    }

    public function show(int $article_trans_id, ?string $article_trans_slug = null)
    {
        // هذه الدالة عامة (Frontend) لعرض المقال للزوار
        $currentLocale = LaravelLocalization::getCurrentLocale();
        //$cacheKey = "article_show_{$article_trans_id}_lang_{$currentLocale}";

        //$article_trans = Cache::remember($cacheKey, 7200, function () use ($article_trans_id, $currentLocale) {
        $trans = ArticleTranslation::with([
            'article.comments' => function ($query) {
                $query->whereNull('parent_id')->with('replies');
            },
            'article.reactions' => function ($query) {
                $query->whereIn('type', ['like', 'dislike']);
            },
            'article.tags.translations' => function ($query) use ($currentLocale) {
                $query->where('locale', $currentLocale);
            },
            'article.category.translations' => function ($query) use ($currentLocale) {
                $query->where('locale', $currentLocale);
            },
            'article',
            'article.translations',
            'article.category',
            'article.tags',
            'article.media'
        ])->find($article_trans_id);


        // 🔍 شرط العرض المزدوج: يجب أن يكون المقال الأساسي منشوراً، والترجمة الخاصة بهذه اللفة منشورة أيضاً
        if (!$trans || $trans->article->status != "published" || $trans->status != "published") {
            return null;
        }


        $doc = new DOMDocument();
        @$doc->loadHTML(
            mb_convert_encoding($trans->description, 'HTML-ENTITIES', 'UTF-8'),
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );

        $imgs = $doc->getElementsByTagName('img');
        foreach ($imgs as $img) {
            $content_img_id = $img->getAttribute('data-image-id');
            $media = Media::find($content_img_id);
            if ($media) {
                $img->setAttribute('srcset', $media->getSrcset());
                $img->setAttribute('sizes', '(max-width: 768px) 100vw, 1200px');
            }
        }

        $trans->description = $doc->saveHTML();
        $article_trans =  $trans;
        //});

        if (!$article_trans) {
            return to_route('home.index');
        }

        if ($article_trans->locale !== $currentLocale) {
            $target_translation = $article_trans->article->translations
                ->where('locale', $currentLocale)
                ->where('status', 'published') // التأكد من أن اللغة البديلة منشورة أيضاً
                ->first();

            if ($target_translation) {
                return redirect()->route('articles.show', [
                    'id'   => $target_translation->id,
                    'slug' => $target_translation->slug
                ]);
            } else {
                return to_route('home.index')->with('info', __('This article is not available in your language.'));
            }
        }

        if (empty($article_trans_slug) || $article_trans->slug !== $article_trans_slug) {
            return redirect()->route('articles.show', [
                'id'   => $article_trans->id,
                'slug' => $article_trans->slug
            ], 301);
        }

        return view('articles.show', compact('article_trans'));
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
        $status = $article_trans->status;
        if ($request->user()->hasRole(['super-admin', 'editor']) || $request->user()->can('publish-articles')) {
            $status = $request->input('status', $article_trans->status);
        } else {
            // إذا عدل الكاتب ترجمته، تعود تلقائياً لـ pending لتدقيقها مجدداً
            $status = 'pending';
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
                // لا يجب تعديل كاتب المقال وتغييره عند تحديث المقال
                //'user_id'          => $request->user()->id,
                'status'           => $status,
            ]);
            // التحقق مما إذا كانت الترجمة أصبحت منشورة حديثاً بعد التحديث
            if ($article_trans->article && $article_trans->article->status === 'published' && $article_trans->status === 'published' && $article_trans->article->category) {
                \App\Jobs\SendNewArticleNotificationJob::dispatch($article_trans)->onQueue('default');
            }

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

        // 🔒 التحقق من صلاحية الحذف (الأدمن والمحرر فقط، أو صاحب الترجمة إذا أردت)
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
