<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Article;
use App\Models\ArticleTranslation;
use App\Models\Category;
use App\Models\CategoryTranslation;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ArticleController extends Controller
{
    //
    public function index()
    {
        $articles = Article::with('translations')->paginate(10);
        return view("admin.articles.index", compact('articles'));
    }

    public function create()
    {
        $cats = Category::orderBy('sign', 'asc')->get();
        $tags = Tag::all();
        return view('admin.articles.create', compact('cats', 'tags'));
    }

    public function store(Request $request)
    {
        // إذا لم يكن المستخدم يملك صلاحية النشر، نجبر الحالة تلقائياً على أن تكون draft (مسودة)
        // حتى لو حاول التلاعب بالطلب وإرسال status = published
        $status = 'draft';
        if ($request->user()->can('publish-articles')) {
            $status = $request->input('status', 'draft');
        }

        $request->validate([
            'category_id' => 'required|numeric|exists:categories,id',
            'featured_img' => "required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120",
            'sign' => 'required|string|unique:articles,sign',
            'status' => 'required|in:published,draft',
        ]);

        //Create a new article
        $article = Article::create([
            'category_id' => (int)$request->category_id,
            'user_id' => Auth::id(),
            'sign' => trim($request->sign),
            'views' => 0,
            //'status' => $request->status,
            'status' => $status
        ]);

        // ربط الوسوم بالمقالة
        if ($request->has('tags')) {
            $article->tags()->sync($request->tags);
        }

        $file = $request->file('featured_img');
        $file_ext = strtolower($file->getClientOriginalExtension());
        $fileName = uniqid() . '.' . $file_ext;
        //$fileName = Str::random(8) . '_' . time() . '.' . $file_ext;

        $article
            ->addMedia($file) // $article->addMediaFromRequest('content_img)
            //->usingFileName(Str::random(32).'.'.$file_ext)
            ->usingFileName($fileName)
            ->withResponsiveImages()
            ->toMediaCollection("featured_imgs");

        return to_route('admin.articles.index');
    }

    public function edit(Request $request, int $article_id)
    {
        //$article = Article::findOrFail($article_id);
        $article = Article::with('category', 'tags')->findOrFail($article_id);

        if (!$request->user()->hasRole(['super-admin', 'editor']) && Auth::id() !== $article->user_id) {
            abort(403, 'غير مسموح لك بتعديل مقال لا تملكه.');
        }

        $cats = Category::all();
        $tags = Tag::all();
        return view('admin.articles.edit', compact('article', 'cats', 'tags'));
    }

    public function update(Request $request, int $article_id)
    {
        $article = Article::findOrFail($article_id);

        if (!$request->user()->hasRole(['super-admin', 'editor']) && Auth::id() !== $article->user_id) {
            abort(403, 'غير مسموح لك بتعديل مقال لا تملكه.');
        }


        // تحديد الحالة بناءً على الصلاحيات
        $status = $article->status; // افتراضياً نبقي الحالة القديمة كما هي إذا لم يكن يملك صلاحية النشر
        if ($request->user()->can('publish-articles')) {
            $status = $request->input('status', 'draft');
        }

        $request->validate([
            'category_id' => 'required|numeric|exists:categories,id',
            'featured_img' => "nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120",
            'sign' => [
                'string',
                Rule::unique('articles', 'sign')->ignore($article_id),
            ],
            'status' => 'required|in:published,draft',
        ]);
        try {

            $article->category_id = (int)($request->category_id);
            $article->user_id = Auth::id();
            if ($request->hasFile('featured_img')) {
                if ($article->hasMedia('featured_imgs')) {
                    $article->clearMediaCollection('featured_imgs');
                }
                $file = $request->file('featured_img');
                $file_ext = strtolower($file->getClientOriginalExtension());
                $fileName = uniqid() . '.' . $file_ext;
                //$fileName = Str::random(8) . '_' . time() . '.' . $file_ext;

                $article
                    ->addMedia($file) // $article->addMediaFromRequest('content_img)
                    //->usingFileName(Str::random(32).'.'.$file_ext)
                    ->usingFileName($fileName)
                    ->withResponsiveImages()
                    ->toMediaCollection("featured_imgs");
            }
            $article->sign = trim($request->sign);
            $article->status = $status;
            $article->save();
            //'views' => 0,
            //'status' => 'published',

            // تحديث ومزامنة الأوسمة (Tags) في الجدول الوسيط
            // إذا قام الأدمن بإلغاء اختيار كافة الأوسمة، نمرر مصفوفة فارغة لتصفية الارتباط
            $tags = $request->input('tags', []);
            $article->tags()->sync($tags);
            //return redirect()->back()->with('success', 'Article Updated Successfully.');
            return to_route('admin.articles.index')->with('success', 'Article Updated Successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update article. Please try again.');
        }

        //return to_route('admin.articles.index');
    }


    public function destroy(int $article_id)
    {
        try {
            $article = Article::findOrFail($article_id);
            $article->delete();
            return to_route('admin.articles.index')->with('success', 'Article Deleted Successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to delete article. Please try again.');
        }
    }

    public function uploadContentImg(Request $request)
    {
        $request->validate([
            'article_id' => 'required|numeric|exists:articles,id',
            'content_img' => "required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048"
        ]);

        $article = Article::findOrFail($request->article_id);
        if ($request->hasFile('content_img')) {
            $file = $request->file('content_img');
            $file_ext = strtolower($file->getClientOriginalExtension());
            //$fileName = Str::random(8) . '_' . time() . '.' . $file_ext;
            //$fileName = Str::uuid() . '_' . time() . '.' . $file_ext;
            $fileName = uniqid() . '.' . $file_ext;

            $article
                ->addMedia($file) // $article->addMediaFromRequest('content_img)
                //->usingFileName(Str::random(32).'.'.$file_ext)
                ->usingFileName($fileName)
                ->withResponsiveImages()
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

    public function getArticleContentImgs(Request $request)
    {
        $article_content_imgs = Article::findOrFail($request->article_id)->getMedia('content_imgs')->all();
        return response()->json($article_content_imgs);
    }


    public function deleteContentImg(Request $request)
    {
        // getting the media
        $content_img_id = $request->content_img_id;
        $content_img = Media::find($content_img_id);
        if ($content_img) {
            // deleting the media
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
}
