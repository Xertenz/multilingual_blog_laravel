<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendNewArticleNotificationJob;
use App\Models\Article;
use App\Models\ArticleTranslation;
use App\Notifications\NewArticleInFavoriteCategory;
use DOMDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class ArticleTranslationController extends Controller
{
    // no need to dispaly all articles' translations
    /*
    public function index() {
        $articles_trans = ArticleTranslation::all();//->where('locale', 'ar');
        return view('admin.articles.index-trans', compact('articles_trans'));
    }
    */

    public function create(int $article_id)
    {
        //$cats = CategoryTranslation::all()->where('locale', 'ar');
        $article = Article::find($article_id);
        $article_content_imgs = $article->getMedia('content_imgs');
        return view('admin.articles.create-trans', compact('article_id', 'article_content_imgs'));
    }

    public function store(Request $request)
    {
        // 1. استخراج الـ slug أو الاعتماد على الـ title كخيار بديل
        $slugSource = trim($request->input('slug')) !== ''
            ? $request->input('slug')
            : $request->input('title');

        // 2. توليد الـ slug النهائي (يدعم الإنجليزية والعربية)
        $finalSlug = (string) Str::of($slugSource)->slug('-');
        // دمج الـ slug الناتج داخل الـ Request قبل التمرير لـ Validation
        $request->merge(['slug' => $finalSlug]);

        // جلب اللغات المدعومة ديناميكياً من مكتبة mcamara
        $supportedLocales = implode(',', LaravelLocalization::getSupportedLanguagesKeys());

        // 3. التحقق من البيانات
        $validated = $request->validate([
            'article_id' => 'required|numeric|exists:articles,id',
            'locale' => [
                'required',
                'in:' . $supportedLocales, // 👈 التعديل الديناميكي
                Rule::unique('article_translations', 'locale')->where(function ($query) use ($request) {
                    return $query->where('article_id', (int)$request->article_id);
                })
            ],
            'slug' => [
                'required', // تم تغييرها إلى required لأن الـ slug تم دمجه مسبقاً في الخطوة 2
                'string',
                Rule::unique('article_translations', 'slug')->where(function ($query) use ($request) {
                    return $query->where('locale', $request->locale);
                })
            ],
            'title' => "required|string|min:3",
            'description' => "required|string|min:20",
            'meta_title' => "required|string|min:3",
            'meta_description' => "required|string|min:20",
        ]);


        try {
            // 4. إضافة الترجمة
            $article_trans = ArticleTranslation::create([
                'article_id'       => $request->article_id,
                'locale'           => $request->locale,
                'slug'             => trim($request->slug),
                'title'            => trim($request->title),
                'description'      => trim($request->description),
                'meta_title'       => trim($request->meta_title),
                'meta_description' => trim($request->meta_description),
                'views'            => 0,
            ]);

            // 5. إرسال الإشعارات
            $article = $article_trans->article;

            if ($article && $article->status === 'published' && $article->category) {
                \App\Jobs\SendNewArticleNotificationJob::dispatch($article_trans)->onQueue('default');
                //}
            }

            Cache::forget("article_show_{$article_trans->id}_lang_{$article_trans->locale}");
            return to_route('admin.articles.index')->with('success', 'Article Translation Added Successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to add article translation. Please try again.');
        }
    }

    public function show(int $article_trans_id, ?string $article_trans_slug = null)
    {
        $currentLocale = LaravelLocalization::getCurrentLocale();
        $cacheKey = "article_show_{$article_trans_id}_lang_{$currentLocale}";

        // تخزين المقال ومعالجته في Redis لمدة ساعتين لضمان سرعة فائقة
        $article_trans = Cache::remember($cacheKey, 7200, function () use ($article_trans_id, $currentLocale) {
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

            if (!$trans || $trans->article->status != "published") {
                return null;
            }

            // معالجة صور الـ DOMDocument وحقن الـ srcset داخل الكاش
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
            return $trans;
        });

        if (!$article_trans) {
            return to_route('home.index');
        }

        // Redirect if there is no translation for the specified $lang_code
        if ($article_trans->locale !== $currentLocale) {
            $target_translation = $article_trans->article->translations
                ->where('locale', $currentLocale)
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

        // correct the slug if it's wrong
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
        $article_trans = ArticleTranslation::with('article', 'media')->find($article_trans_id);
        $article = $article_trans->article;

        if (!$request->user()->hasRole(['super-admin', 'editor']) && $article->user_id !== $request->user()->id) {
            abort(403, 'غير مسموح لك بتعديل ترجمة مقال لا تملكه.');
        }

        $article_content_imgs = $article->getMedia('content_imgs');
        return view('admin.articles.edit-trans', compact('article_trans', 'article_content_imgs'));
    }


    public function update(Request $request, int $article_trans_id)
    {
        // 1. جلب الترجمة أولاً لنتمكن من الوصول للمقال المرتبط بها والتحقق من مالكه
        $article_trans = ArticleTranslation::findOrFail($article_trans_id);
        $article = $article_trans->article;

        // 👈 التحقق من الملكية قبل إجراء أي تعديل
        if (!$request->user()->hasRole(['super-admin', 'editor']) && $article->user_id !== $request->user()->id) {
            abort(403, 'غير مسموح لك بتحديث ترجمة مقال لا تملكه.');
        }

        // 2. استخراج الـ slug أو الاعتماد على الـ title كخيار بديل
        $slugSource = trim($request->input('slug')) !== ''
            ? $request->input('slug')
            : $request->input('title');

        // 3. توليد الـ slug النهائي (يدعم الإنجليزية والعربية)
        $finalSlug = (string) Str::of($slugSource)->slug('-');

        // دمج الـ slug الناتج داخل الـ Request قبل التمرير لـ Validation
        $request->merge(['slug' => $finalSlug]);

        // جلب اللغات المدعومة ديناميكياً من مكتبة mcamara
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
        ]);

        try {
            $article_trans->update([
                'locale' => $request->locale,
                'slug' => trim($request->slug),
                'title' => trim($request->title),
                'description' => trim($request->description),
                'meta_title' => trim($request->meta_title),
                'meta_description' => trim($request->meta_description),
            ]);

            Cache::forget("article_show_{$article_trans->id}_lang_{$article_trans->locale}");
            return to_route('admin.articles.index')->with('success', 'Article Updated Successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update article. Please try again.');
        }
    }

    public function destroy(int $article_trans_id)
    {
        $article_trans = ArticleTranslation::findOrFail($article_trans_id);

        Cache::forget("article_show_{$article_trans->id}_lang_{$article_trans->locale}");

        $article_trans->delete();
        return to_route('admin.articles.index')->with('success', 'Article Translation Deleted Successfully.');
    }
}
