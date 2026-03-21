<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use App\Models\Article;
use App\Models\ArticleTranslation;
use App\Models\Category;
use App\Models\CategoryTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ArticleController extends Controller
{
    //
    public function index() {
        $articles = Article::with('translations')->paginate(10);
        return view("admin.articles.index", compact('articles'));
    }

    public function create() {
        $cats = Category::orderBy('sign', 'asc')->get();
        return view('admin.articles.create', compact('cats'));
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'category_id' => 'required|numeric|exists:categories,id',
            'featured_img' => "required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120",
            'sign' => 'required|string|unique:articles,sign',
            'status' => 'required|in:published,draft',
        ]);

        $article = Article::create([
            'category_id' => (int)$request->category_id,
            'user_id' => Auth::id(),
            'sign' => $request->sign,
            'views' => 0,
            'status' => $request->status,
        ]);

        $file = $request->file('featured_img');
        $file_ext = $file->getClientOriginalExtension();
        $fileName = Str::random(8) . '_' . time() . '.' . $file_ext;

        $article
            ->addMedia($file) // $article->addMediaFromRequest('content_img)
            //->usingFileName(Str::random(32).'.'.$file_ext)
            ->usingFileName($fileName)
            //->withResponsiveImages()
            ->toMediaCollection("featured_imgs");

        return to_route('admin.articles.index');
    }

    public function edit(int $article_id) {
        $article = Article::findOrFail($article_id);
        $cats = Category::all();
        return view('admin.articles.edit', compact('article', 'cats'));
    }

    public function update(Request $request, int $article_id) {
        $validated = $request->validate([
            'category_id' => 'required|numeric|exists:categories,id',
            'featured_img' => "nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120",
            'sign' => [
                'string',
                Rule::unique('articles', 'sign')->ignore($article_id),
            ],
            'status' => 'required|in:published,draft',
        ]);
        $article = Article::findOrFail($article_id);
        $article->category_id = (int)($request->category_id);
        $article->user_id = Auth::id();
        if($request->hasFile('featured_img')) {
            if ($article->hasMedia('featured_imgs')) {
                $article->clearMediaCollection('featured_imgs');
            }
            $file = $request->file('featured_img');
            $file_ext = $file->getClientOriginalExtension();
            $fileName = Str::random(8) . '_' . time() . '.' . $file_ext;

            $article
                ->addMedia($file) // $article->addMediaFromRequest('content_img)
                //->usingFileName(Str::random(32).'.'.$file_ext)
                ->usingFileName($fileName)
                //->withResponsiveImages()
                ->toMediaCollection("featured_imgs");
        }
        $article->sign = $request->sign;
        $article->status = $request->status;
        $article->save();
            //'views' => 0,
            //'status' => 'published',

        return to_route('admin.articles.index');
    }


    public function destroy($article_id) {
        $article = Article::findOrFail($article_id);
        $article->delete();

        return to_route('admin.articles.index');
    }

    public function uploadContentImg(Request $request) {
        $request->validate([
            'article_id' => 'required|numeric|exists:articles,id',
            'content_img' => "required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048"
        ]);

        $article = Article::findOrFail($request->article_id);
        if($request->hasFile('content_img')) {
            $file = $request->file('content_img');
            $file_ext = $file->getClientOriginalExtension();
            $fileName = Str::random(8) . '_' . time() . '.' . $file_ext;
            //$fileName = Str::uuid() . '_' . time() . '.' . $file_ext;

            $article
                ->addMedia($file) // $article->addMediaFromRequest('content_img)
                //->usingFileName(Str::random(32).'.'.$file_ext)
                ->usingFileName($fileName)
                //->withResponsiveImages()
                ->toMediaCollection("content_imgs");
        }
        
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

    public function get_content_img_info(Request $request) {
        $content_img_id = (int)$request->content_img_id;
        $media = Media::find($content_img_id);
        return response()->json($media);
    }

    public function test_upload_with_tiny(Request $request){
        //dd($request);
        $article_media = Article::findOrFail($request->article_id)->getFirstMedia('content_imgs');
        return response()->json(['location' => $article_media->original_url]);
    }

}
