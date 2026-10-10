<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Article;
use App\Models\ArticleTranslation;
use App\Notifications\NewArticleInFavoriteCategory;
use DOMDocument;
use Illuminate\Http\Request;
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
        $articles_trans = ArticleTranslation::all();//->where('language_code', 'ar');
        return view('admin.articles.index-trans', compact('articles_trans'));
    }
    */

    public function create(int $article_id)
    {
        //$cats = CategoryTranslation::all()->where('language_code', 'ar');
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
            'language_code' => [
                'required',
                'in:' . $supportedLocales, // 👈 التعديل الديناميكي
                Rule::unique('article_translations', 'language_code')->where(function ($query) use ($request) {
                    return $query->where('article_id', (int)$request->article_id);
                })
            ],
            'slug' => [
                'required', // تم تغييرها إلى required لأن الـ slug تم دمجه مسبقاً في الخطوة 2
                'string',
                Rule::unique('article_translations', 'slug')->where(function ($query) use ($request) {
                    return $query->where('language_code', $request->language_code);
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
                'language_code'    => $request->language_code,
                'slug'             => trim($request->slug),
                'title'            => trim($request->title),
                'description'      => trim($request->description),
                'meta_title'       => trim($request->meta_title),
                'meta_description' => trim($request->meta_description),
            ]);

            // 5. إرسال الإشعارات
            $article = $article_trans->article;

            if ($article && $article->status === 'published' && $article->category) {
                // Sending Notification one time for users
                if ($article->translations()->count() === 1) {
                    $article->load(['category.followers', 'translations']);
                    $usersToNotify = $article->category->followers;

                    if ($usersToNotify->isNotEmpty()) {
                        Notification::send($usersToNotify, new NewArticleInFavoriteCategory($article));
                    }
                }
            }

            return to_route('admin.articles.index')->with('success', 'Article Translation Added Successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to add article translation. Please try again.');
        }
    }


    public function show(int $article_trans_id, ?string $article_trans_slug = null)
    {
        $lang_code = LaravelLocalization::getCurrentLocale();

        // Getting the translated article basen on id only
        $article_trans = ArticleTranslation::with([
            'article.comments' => function ($query) {
                $query->whereNull('parent_id')->with('replies');
            },

            'article.reactions' => function ($query) {
                $query->whereIn('type', ['like', 'dislike']);
            },

            'article.tags.translations' => function ($query) use ($lang_code) {
                $query->where('language_code', $lang_code);
            },
            'article.category.translations' => function ($query) use ($lang_code) {
                $query->where('language_code', $lang_code);
            }

        ])
            ->where('id', $article_trans_id)
            //->whereSlug($article_trans_slug)
            ->first();

        //dd($article_trans->article->tags);

        if (!$article_trans || $article_trans->article->status != "published") {
            return to_route('home.index');
        }


        // Redirect if there is no translation for the specifed $lang_code
        if ($article_trans->language_code !== $lang_code) {

            // Get article_translation based on specifed $lang_code
            $target_translation = $article_trans->article->translations
                ->where('language_code', $lang_code)
                ->first();

            if ($target_translation) {
                // Redirect if the translation exists
                return redirect()->route('articles.show', [
                    'id'   => $target_translation->id,
                    'slug' => $target_translation->slug
                ]);
            } else {
                // Redirect to home page if the translation not exists
                return to_route('home.index')->with('info', __('This article is not available in your language.'));
            }
        }


        $doc = new DOMDocument();
        @$doc->loadHTML(
            mb_convert_encoding($article_trans->description, 'HTML-ENTITIES', 'UTF-8'),
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

        $article_trans->description = $doc->saveHTML();

        // correct the slug if it's wrong (because only id is required for getting the record)
        if (empty($article_trans_slug) || $article_trans->slug !== $article_trans_slug) {
            return redirect()->route('articles.show', [
                'id'   => $article_trans->id,
                'slug' => $article_trans->slug
            ], 301);
        }

        $article_trans->load(
            'article',
            'article.translations',
            'article.reactions',
            'article.category',
            'article.category.translations',
            'article.tags',
            'article.tags.translations',
            'article.comments',
            'article.media'
        );
        return view('articles.show', compact('article_trans'));
    }


    public function edit(Request $request, int $article_trans_id)
    {
        //$cats = CategoryTranslation::all()->where('language_code', 'ar');
        $article_trans = ArticleTranslation::with('article', 'media')->find($article_trans_id);
        $article = $article_trans->article;
        $article_content_imgs = $article->getMedia('content_imgs');
        return view('admin.articles.edit-trans', compact(/* 'cats',*/'article_trans', 'article_content_imgs'));
    }


    public function update(Request $request, int $article_trans_id)
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

        $request->validate([
            'article_id' => 'required|numeric|exists:articles,id',
            'language_code' => [
                'required',
                'in:' . $supportedLocales, // 👈 أصبح ديناميكياً هنا
                Rule::unique('article_translations')->where(function ($query) use ($request) {
                    return $query->where('article_id', (int)$request->article_id);
                })->ignore($article_trans_id),
            ],
            'slug' => [
                'required',
                'string',
                Rule::unique('article_translations')->where(function ($query) use ($request) {
                    return $query->where('language_code', $request->language_code);
                })->ignore($article_trans_id)
            ],
            'title' => "required|string|min:3",
            'description' => "required|string|min:20",
            'meta_title' => "required|string|min:3",
            'meta_description' => "required|string|min:20",
        ]);

        try {
            $article_trans = ArticleTranslation::findOrFail($article_trans_id);
            $article_trans->update([
                'language_code' => $request->language_code,
                'slug' => trim($request->slug),
                'title' => trim($request->title),
                'description' => trim($request->description),
                'meta_title' => trim($request->meta_title),
                'meta_description' => trim($request->meta_description),
            ]);

            return redirect()->back()->with('success', 'Article Updated Successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update article. Please try again.');
        }
    }

    public function destroy(int $article_trans_id)
    {
        $article_trans = ArticleTranslation::findOrFail($article_trans_id);
        $article_trans->delete();

        return to_route('admin.articles.index')->with('success', 'Article Translation Deleted Successfully.');
    }
}
