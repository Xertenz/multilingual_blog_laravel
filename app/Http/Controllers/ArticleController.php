<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleTranslation;
use App\Models\CategoryTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ArticleController extends Controller
{
    //
    public function index() {
        $articles = Article::with('translations')->get();

        return view('admin.articles', compact('articles'));
    }

    public function create(int $article) {
        $cats = CategoryTranslation::all()->where('language_code', 'ar');
        return view('admin.add-article', compact('cats'));
    }


    public function store(Request $request) {
        $validated = $request->validate([
            'category_id' => 'required|numeric|exists:categories,id',
            'featured_img' => "required|image",
        ]);

        $article = Article::create([
            'category_id' => (int)$request->category_id,
            'featured_img' => '',
            'views' => 0,
            'status' => 'published',
        ]);

        return to_route('articles.index');
    }

    public function edit(int $article_id) {
        $article = Article::findOrFail($article_id);
        $cats = CategoryTranslation::all()->where('language_code', 'ar');
        return view('admin.edit-article', compact('article', 'cats'));
    }

    public function update(Request $request, int $article_id) {
        $validated = $request->validate([
            'category_id' => 'required|numeric|exists:categories,id',
            'featured_img' => "required|image",
        ]);
        $article = Article::findOrFail($article_id);
        $article->update([
            'category_id' => (int)($request->category_id),
            'featured_img' => '',
            //'views' => 0,
            //'status' => 'published',
        ]);

        return to_route('articles.index');
    }


    public function destroy($article_id) {
        $article = Article::findOrFail($article_id);
        $article->delete();

        return to_route('articles.index');
    }

    public function uploadContentImg(Request $request) {

        $request->validate([
            'article_id' => 'required|numeric|exists:articles,id',
            'content_img' => "required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048"
        ]);

        $file = $request->file('content_img');
        $file_ext = $file->getClientOriginalExtension();

        $article = Article::findOrFail($request->article_id);

        $article
            ->addMedia($file) // $article->addMediaFromRequest('content_img)
            ->usingFileName(Str::random(32).'.'.$file_ext)
            //->withResponsiveImages()
            ->toMediaCollection("content_imgs");
        
        $article_media = Article::findOrFail($request->article_id)
            ->getMedia('content_imgs');

        return response()->json(
            [
                'content_imgs' => $article_media
            ]
        );
    }

    public function getArticleContentImgs(Request $request) {
        $article_content_imgs = Article::findOrFail($request->article_id)->getMedia('content_imgs')->all();
        return response()->json($article_content_imgs);
    }


    public function deleteContentImg(Request $request) {
        // getting the media
        $content_img_id = $request->content_img_id;
        $content_img = Media::find($content_img_id);

        // deleting the media
        $content_img->delete();
        return response()->json(['message' => 'media deleted successfully']);
    }

    public function test_upload_with_tiny(Request $request){
        //dd($request);
        $article_media = Article::findOrFail($request->article_id)->getFirstMedia('content_imgs');
        return response()->json(['location' => $article_media->original_url]);
    }


}
