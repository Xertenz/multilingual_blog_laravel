<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::with(['translations', 'user'])->paginate(10);
        return view("admin.articles.index", compact('articles'));
    }

    public function create(Request $request)
    {
        if (!$request->user()->hasRole(['super-admin', 'editor'])) {
            abort(403, 'غير مسموح لك بإنشاء المقالات الأساسية.');
        }

        $cats = Category::orderBy('label', 'asc')->get();
        $tags = Tag::all();
        return view('admin.articles.create', compact('cats', 'tags'));
    }

    public function store(Request $request)
    {
        // 🔒 التحقق الأمني
        if (!$request->user()->hasRole(['super-admin', 'editor'])) {
            abort(403, 'غير مسموح لك بإنشاء المقالات الأساسية.');
        }

        $status = $request->input('status', 'draft');

        $request->validate([
            'category_id' => 'required|numeric|exists:categories,id',
            'featured_img' => "required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120",
            'internal_title' => 'required|string|unique:articles,internal_title',
            'status' => 'required|in:draft,pending,published',
        ]);

        $article = Article::create([
            'category_id' => (int)$request->category_id,
            'user_id' => Auth::id(),
            'internal_title' => trim($request->internal_title),
            'views' => 0,
            'status' => $status
        ]);

        if ($request->has('tags')) {
            $article->tags()->sync($request->tags);
        }

        $file = $request->file('featured_img');
        $file_ext = strtolower($file->getClientOriginalExtension());
        $fileName = uniqid() . '.' . $file_ext;

        $article->addMedia($file)
            ->usingFileName($fileName)
            ->withResponsiveImages()
            ->toMediaCollection("featured_imgs");

        return to_route('admin.articles.index')->with('success', 'Article Created Successfully.');
    }

    public function edit(Request $request, int $article_id)
    {
        $user = $request->user();

        if (!$user->hasRole(['super-admin', 'editor'])) {
            abort(403, 'غير مسموح لك بتعديل المقالات.');
        }

        if (!$user->hasRole(['super-admin', 'editor'])) {
            abort(403, 'غير مسموح لك بتعديل المقالات الأساسية.');
        }

        $article = Article::with('category', 'tags')->findOrFail($article_id);
        $cats = Category::all();
        $tags = Tag::all();
        return view('admin.articles.edit', compact('article', 'cats', 'tags'));
    }

    public function update(Request $request, int $article_id)
    {
        $user = $request->user();

        if (!$user->hasRole(['super-admin', 'editor'])) {
            abort(403, 'غير مسموح لك بتحديث المقالات الأساسية.');
        }

        $article = Article::findOrFail($article_id);

        $status = $request->input('status', $article->status);

        $request->validate([
            'category_id' => 'required|numeric|exists:categories,id',
            'featured_img' => "nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120",
            'internal_title' => [
                'string',
                Rule::unique('articles', 'internal_title')->ignore($article_id),
            ],
            'status' => 'required|in:draft,pending,published',
        ]);

        try {
            $article->category_id = (int)($request->category_id);
            // ملاحظة: يُفضل عدم تغيير user_id عند التعديل ليبقى الكاتب الأصلي محفوظاً، إلا إذا رغبت بغير ذلك
            // $article->user_id = Auth::id(); 

            if ($request->hasFile('featured_img')) {
                if ($article->hasMedia('featured_imgs')) {
                    $article->clearMediaCollection('featured_imgs');
                }
                $file = $request->file('featured_img');
                $file_ext = strtolower($file->getClientOriginalExtension());
                $fileName = uniqid() . '.' . $file_ext;

                $article->addMedia($file)
                    ->usingFileName($fileName)
                    ->withResponsiveImages()
                    ->toMediaCollection("featured_imgs");
            }

            $article->internal_title = trim($request->internal_title);
            $article->status = $status;
            $article->save();

            $tags = $request->input('tags', []);
            $article->tags()->sync($tags);

            return to_route('admin.articles.index')->with('success', 'Article Updated Successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update article. Please try again.');
        }
    }

    public function destroy(Request $request, int $article_id)
    {
        // 🔒 حصر الحذف حصراً بالمشرف والمحرر
        if (!$request->user()->hasRole(['super-admin', 'editor'])) {
            abort(403, 'غير مسموح لك بحذف المقالات.');
        }

        try {
            $article = Article::findOrFail($article_id);
            $article->clearMediaCollection('featured_imgs');
            $article->clearMediaCollection('content_imgs');
            $article->delete();

            return to_route('admin.articles.index')->with('success', 'Article Deleted Successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete article. Please try again.');
        }
    }

    public function uploadContentImg(Request $request)
    {
        // 🔒 حماية رفع صور المحتوى لتكون مخصصة للكاتب/المحرر/الأدمن فقط
        if (!$request->user()->hasRole(['super-admin', 'editor', 'writer'])) {
            abort(403, 'غير مسموح لك برفع صور للمحتوى.');
        }

        $request->validate([
            'article_id' => 'required|numeric|exists:articles,id',
            'content_img' => "required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048"
        ]);

        $article = Article::findOrFail($request->article_id);
        if ($request->hasFile('content_img')) {
            $file = $request->file('content_img');
            $file_ext = strtolower($file->getClientOriginalExtension());
            $fileName = uniqid() . '.' . $file_ext;

            $article->addMedia($file)
                ->usingFileName($fileName)
                ->withResponsiveImages()
                ->toMediaCollection("content_imgs");
        }

        //$article_media = Article::findOrFail($request->article_id)->getMedia('content_imgs');
        $article_media = $article->getMedia('content_imgs');

        return response()->json(['content_imgs' => $article_media]);
    }

    public function getArticleContentImgs(Request $request)
    {
        $article_content_imgs = Article::findOrFail($request->article_id)->getMedia('content_imgs')->all();
        return response()->json($article_content_imgs);
    }

    public function deleteContentImg(Request $request)
    {
        $content_img_id = $request->content_img_id;
        $content_img = Media::find($content_img_id);
        if ($content_img) {
            $content_img->delete();
            return response()->json(['message' => 'media deleted successfully']);
        } else {
            return response()->json(['error' => 'error in delete media']);
        }
    }

    public function get_content_img_info(Request $request)
    {
        $content_img_id = (int)$request->content_img_id;
        $media = Media::find($content_img_id);
        return response()->json($media);
    }

    public function pending(Request $request)
    {
        if (!$request->user()->hasRole(['super-admin', 'editor'])) {
            abort(403, 'غير مسموح لك بمراجعة المقالات.');
        }

        $articles = Article::with(['translations', 'user'])
            ->where('status', 'pending')
            ->latest()
            ->paginate(10);

        return view('admin.articles.pending', compact('articles'));
    }

    public function approve(Request $request, int $article_id)
    {
        if (!$request->user()->hasRole(['super-admin', 'editor'])) {
            abort(403, 'غير مسموح لك بالموافقة على المقالات.');
        }

        $article = Article::findOrFail($article_id);
        $article->update(['status' => 'published']);

        return redirect()->route('admin.articles.pending')->with('success', 'Article approved and published successfully.');
    }

    public function reject(Request $request, int $article_id)
    {
        if (!$request->user()->hasRole(['super-admin', 'editor'])) {
            abort(403, 'غير مسموح لك برفض المقالات.');
        }

        $article = Article::findOrFail($article_id);
        $article->update(['status' => 'draft']);

        return redirect()->route('admin.articles.pending')->with('success', 'Article rejected and moved back to draft.');
    }
}
