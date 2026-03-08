<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use App\Models\Article;
use App\Models\ArticleTranslation;
use App\Models\CategoryTranslation;
use DOMDocument;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ArticleTranslationController extends Controller
{
    // no need to dispaly all articles' translations
    /*
    public function index() {
        $articles_trans = ArticleTranslation::all();//->where('language_code', 'ar');
        return view('admin.articles.index-trans', compact('articles_trans'));
    }
    */

    public function create(int $article_id ) {
        //$cats = CategoryTranslation::all()->where('language_code', 'ar');
        $article = Article::find($article_id);
        $article_content_imgs = $article->getMedia('content_imgs');
        return view('admin.articles.create-trans', compact( 'article_id', 'article_content_imgs'));
    }


    public function store(Request $request) {
        $validated = $request->validate([
            'article_id' => 'required|numeric|exists:articles,id',
            'language_code' => [
                'required',
                'in:en,ar,de,es,fr,ru,pt',
                Rule::unique('article_translations')->where(function($query) use ($request){
                    return $query->where('article_id', (int)$request->article_id);
                })
            ],
            

            //'slug' => "required|string|unique:article_translations,slug",
            'slug' => [
                'required',
                'string',
                Rule::unique('article_translations')->where(function($query) use ($request){
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
        ArticleTranslation::create([
            'article_id' => $request->article_id,
            'language_code' => $request->language_code,
            'slug' => trim($request->slug),
            'title' => trim($request->title),
            'description' => trim($request->description),
            'meta_title' => trim($request->meta_title),
            'meta_description' => trim($request->meta_description),
        ]);
        
        return to_route('admin.articles.index');

    }

    public function show(string $article_trans_slug) {
        $article_trans = ArticleTranslation::where('slug', $article_trans_slug)->first();
        /*
        $article_trans = ArticleTranslation::where('slug', $article_trans_slug)->first();
        $article_trans_lang_code = $article_trans->lang_code;
        $article_trans_title = $article_trans->title;
        $article_trans_description = $article_trans->description;
        $article_trans_meta_title = $article_trans->meta_title;
        $article_trans_meta_description = $article_trans->meta_description;
        */

        $doc = new DOMDocument();
        @$doc->loadHTML(
            mb_convert_encoding($article_trans->description, 'HTML-ENTITIES', 'UTF-8'),
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        $imgs = $doc->getElementsByTagName('img');
        foreach($imgs as $img) {
            $content_img_id = $img->getAttribute('data-image-id');
            $img->setAttribute('srcset', $this->create_content_img_srcset($content_img_id));
            $img->setAttribute('sizes', '(max-width: 768px) 100vw, 800px');
        }

        /*
        $article_trans_description = $doc->saveHTML();
        */
        $article_trans->description = $doc->saveHTML();


        return view('articles.show', compact('article_trans'));
    }

    public function create_content_img_srcset($content_img_id) {
        /*
        $content_img = Media::find($content_img_id);
        $content_img_names = $content_img['responsive_images']['responsive']['urls'];
        $content_img_year = $content_img->created_at->format('Y');
        $content_img_month = $content_img->created_at->format('m');
        $widths = [1200, 1003, 839, 702, 587, 491, 411, 344, 288, 241];
        $srcset = '';
        $i = 0;
        foreach($content_img_names as $content_img_name) {
            $url = asset("storage/article/content/$content_img_year/$content_img_month/responsive/$content_img_name");
            $srcset .= "$url $widths[$i]w, ";
            $i += 1;
        }
        return $srcset;
        */
        $content_img = Media::findOrFail($content_img_id);
        $srcset = '';

        $srcset .= $content_img->getUrl("content_375w") ? "{$content_img->getUrl('content_375w')} 375w," : '';
        $srcset .= $content_img->getUrl("content_480w") ? "{$content_img->getUrl('content_480w')} 480w," : '';
        $srcset .= $content_img->getUrl('content_768w') ? "{$content_img->getUrl('content_768w')} 768w," : '';
        $srcset .= $content_img->getUrl('content_800w') ? "{$content_img->getUrl('content_800w')} 800w," : '';
        $srcset .= $content_img->getUrl('content_1024w') ? "{$content_img->getUrl('content_1024w')} 1024w," : '';
        $srcset .= $content_img->getUrl('content_1200w') ? "{$content_img->getUrl('content_1200w')} 1200w," : '';
        $srcset .= $content_img->getUrl('content_1600w') ? "{$content_img->getUrl('content_1600w')} 1600w," : '';

        return $srcset;
    }

    public function edit(Request $request, int $article_trans_id) {
        $cats = CategoryTranslation::all()->where('language_code', 'ar');
        $article_trans = ArticleTranslation::find($article_trans_id);
        $article = $article_trans->article;
        $article_content_imgs = $article->getMedia('content_imgs');
        return view('admin.articles.edit-trans', compact('cats', 'article_trans', 'article_content_imgs'));
    }

    public function update(Request $request, int $article_trans_id) {
        $validated = $request->validate([
            'article_id' => 'required|numeric|exists:articles,id',
            'language_code' => [
                'required',
                'in:en,ar,de,es,fr,ru,pt',
                Rule::unique('article_translations')->where(function($query) use ($request){
                    return $query->where('article_id', (int)$request->article_id);
                })->ignore($article_trans_id),
            ],
            /*
            'slug' => [
                'required',
                'string',
                //'unique:article_translations,slug,$article_trans_id'
                Rule::unique('article_translations', 'slug')->ignore($article_trans_id)
            ],
            */
            'slug' => [
                'required',
                'string',
                Rule::unique('article_translations')->where(function($query) use ($request){
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
            'slug' => $request->slug,
            'title' => $request->title,
            'description' => $request->description,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
        ]);

        //return to_route('admin.articles.index');
        return redirect()->back();
    }

    public function destroy($article_trans_id) {
        $article_trans = ArticleTranslation::findOrFail($article_trans_id);
        $article_trans->delete();

        return to_route('admin.articles.index');
    }

}
