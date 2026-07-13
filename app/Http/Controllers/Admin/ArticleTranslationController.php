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
        $request->validate([
            'article_id' => 'required|numeric|exists:articles,id',
            'language_code' => [
                'required',
                'in:en,ar,de,es,fr,ru,pt,it,nl,sv,da,nb',
                Rule::unique('article_translations', 'language_code')->where(function ($query) use ($request) {
                    return $query->where('article_id', (int)$request->article_id);
                })
            ],
            'slug' => [
                'required',
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

        /*
        // The language for this Article should NOT be exist
        $language_exists = ArticleTranslation::where('article_id', $request->article_id)->where('language_code', $request->language_code)->first();
        if($language_exists){
            return back()->withErrors(['language_exists' => 'this language is already exist for this article'] )->withInput();
        }
        */

        // Adding a new Article
        $article_trans = ArticleTranslation::create([
            'article_id' => $request->article_id,
            'language_code' => $request->language_code,
            'slug' => trim($request->slug),
            'title' => trim($request->title),
            'description' => trim($request->description),
            'meta_title' => trim($request->meta_title),
            'meta_description' => trim($request->meta_description),
        ]);

        // Sending the Notification of a new article of the favorite category
        $article = $article_trans->article;
        $article->load(['category.followers', 'translations']);
        if ($article->status === 'published') {
            $usersToNotify = $article->category->followers;

            // Sending the notification to all users
            Notification::send($usersToNotify, new NewArticleInFavoriteCategory($article));
        }

        return to_route('admin.articles.index');
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
            return to_route('home');
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
                return to_route('home')->with('info', __('This article is not available in your language.'));
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

        return view('articles.show', compact('article_trans'));
    }


    public function edit(Request $request, int $article_trans_id)
    {
        //$cats = CategoryTranslation::all()->where('language_code', 'ar');
        $article_trans = ArticleTranslation::find($article_trans_id);
        $article = $article_trans->article;
        $article_content_imgs = $article->getMedia('content_imgs');
        return view('admin.articles.edit-trans', compact(/* 'cats',*/'article_trans', 'article_content_imgs'));
    }

    public function update(Request $request, int $article_trans_id)
    {
        $request->validate([
            'article_id' => 'required|numeric|exists:articles,id',
            'language_code' => [
                'required',
                'in:en,ar,de,es,fr,ru,pt,it,nl,sv,da,nb',
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

        /*
        // The language for this Article should NOT be exist (except the current article_trans)
        $language_exists = ArticleTranslation::where('article_id', $request->article_id)->where('language_code', $request->language_code)->whereNot('id', $article_trans_id)->first();
        if($language_exists){
            return back()->withErrors(['language_exists' => 'this language is already exist for this article'] )->withInput();
        }
        */

        $article_trans = ArticleTranslation::findOrFail($article_trans_id);
        $article_trans->update([
            'language_code' => $request->language_code,
            'slug' => trim($request->slug),
            'title' => trim($request->title),
            'description' => trim($request->description),
            'meta_title' => trim($request->meta_title),
            'meta_description' => trim($request->meta_description),
        ]);

        //return to_route('admin.articles.index');
        return redirect()->back();
    }

    public function destroy(int $article_trans_id)
    {
        $article_trans = ArticleTranslation::findOrFail($article_trans_id);
        $article_trans->delete();

        return to_route('admin.articles.index');
    }
}
