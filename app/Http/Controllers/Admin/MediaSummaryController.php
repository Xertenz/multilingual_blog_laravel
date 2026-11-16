<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ContentStatus;
use App\Http\Controllers\Controller;
use App\Models\ArticleLocation;
use App\Models\MediaSummary;
use App\Models\MediaSummaryCategory;
use App\Models\MediaSummaryTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaSummaryController extends Controller
{
    public function index()
    {
        $summaries = MediaSummary::with(['translations', 'author', 'category.translations', 'tags.translations'])
            ->withCount(['media as content_imgs_count' => function ($q) {
                $q->where('collection_name', 'content_imgs');
            }])
            ->latest()
            ->paginate(10);

        $supportedLocales = LaravelLocalization::getSupportedLocales();

        return view('admin.media_summaries.index', compact('summaries', 'supportedLocales'));
    }

    public function create(Request $request)
    {
        // ✅ الكاتب مسموح
        if (!$request->user()->hasRole(['super-admin', 'editor', 'writer'])) {
            abort(403, 'غير مسموح لك بإنشاء ملخصات الوسائط.');
        }

        $locations = ArticleLocation::with('translations')->get();

        // ✅ الفئات النشطة فقط
        $categories = MediaSummaryCategory::where('is_active', true)
            ->with('translations')
            ->get();

        $tags = MediaSummaryTag::with('translations')->get();

        return view('admin.media_summaries.create', compact('locations', 'categories', 'tags'));
    }

    public function store(Request $request)
    {
        // ✅ الكاتب مسموح
        if (!$request->user()->hasRole(['super-admin', 'editor', 'writer'])) {
            abort(403, 'غير مسموح لك بإنشاء ملخصات الوسائط.');
        }

        // ✅ الكاتب لا يمكنه النشر مباشرة → pending
        $status = $request->input('status', 'pending');
        if (!$request->user()->hasRole(['super-admin', 'editor']) && !$request->user()->can('publish-articles')) {
            $status = 'pending';
        }

        $request->validate([
            // ✅ التحقق الصارم: يجب أن تكون الفئة نشطة
            'media_summary_category_id' => [
                'required',
                Rule::exists('media_summary_categories', 'id')->where('is_active', true),
            ],
            'label' => 'required|string|unique:media_summaries,label|max:255',
            'tags' => 'nullable|array',
            'tags.*' => 'integer|exists:media_summary_tags,id',
            'release_year' => 'nullable|integer|min:1900|max:2100',
            'imdb_rating' => 'nullable|numeric|min:0|max:10',
            'article_location_id' => 'nullable|exists:article_locations,id',
            'featured_img' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
            'status' => ['sometimes', new Enum(ContentStatus::class)],
        ]);

        $summary = MediaSummary::create([
            'user_id' => Auth::id(),
            'label' => trim($request->label),
            'media_summary_category_id' => $request->media_summary_category_id,
            'release_year' => $request->release_year,
            'imdb_rating' => $request->imdb_rating,
            'article_location_id' => $request->article_location_id,
            'status' => $status,  // ✅ pending للكاتب
            'views' => 0,
        ]);

        if ($request->has('tags')) {
            $summary->tags()->sync($request->tags ?? []);
        }

        if ($request->hasFile('featured_img')) {
            $file = $request->file('featured_img');
            $fileName = uniqid() . '.' . strtolower($file->getClientOriginalExtension());

            $summary->addMedia($file)
                ->usingFileName($fileName)
                ->withResponsiveImages()
                ->toMediaCollection('featured_imgs');
        }

        return to_route('admin.media-summaries.index')
            ->with('success', 'Media Summary Created Successfully. Now add translations.');
    }

    public function edit(Request $request, int $id)
    {
        $mediaSummary = MediaSummary::with(['translations', 'media', 'tags'])->findOrFail($id);

        // ✅ المحرر/المدير: أي سجل. الكاتب: فقط سجله.
        if (
            !$request->user()->hasRole(['super-admin', 'editor'])
            && $mediaSummary->user_id !== $request->user()->id
        ) {
            abort(403, 'غير مسموح لك بتعديل هذا الملخص.');
        }

        $locations = ArticleLocation::with('translations')->get();

        // ✅ الفئات النشطة + الفئة الحالية (حتى لو كانت معطلة)
        $categories = MediaSummaryCategory::where(function ($query) use ($mediaSummary) {
            $query->where('is_active', true)
                ->orWhere('id', $mediaSummary->media_summary_category_id);
        })
            ->with('translations')
            ->get();

        $tags = MediaSummaryTag::with('translations')->get();

        return view('admin.media_summaries.edit', compact('mediaSummary', 'locations', 'categories', 'tags'));
    }

    public function update(Request $request, int $id)
    {
        $summary = MediaSummary::findOrFail($id);

        // ✅ المحرر/المدير: أي سجل. الكاتب: فقط سجله.
        if (
            !$request->user()->hasRole(['super-admin', 'editor'])
            && $summary->user_id !== $request->user()->id
        ) {
            abort(403, 'غير مسموح لك بتحديث هذا الملخص.');
        }

        // ✅ الكاتب: التعديل يعيد الحالة إلى pending
        $status = $request->input('status', $summary->status);
        if (!$request->user()->hasRole(['super-admin', 'editor']) && !$request->user()->can('publish-articles')) {
            $status = 'pending';
        }

        $request->validate([
            // ✅ مرن: يسمح بالفئة الحالية حتى لو كانت معطلة
            'media_summary_category_id' => 'required|exists:media_summary_categories,id',
            'label' => [
                'required',
                'string',
                'max:255',
                Rule::unique('media_summaries', 'label')->ignore($id),
            ],
            'tags' => 'nullable|array',
            'tags.*' => 'integer|exists:media_summary_tags,id',
            'release_year' => 'nullable|integer|min:1900|max:2100',
            'imdb_rating' => 'nullable|numeric|min:0|max:10',
            'article_location_id' => 'nullable|exists:article_locations,id',
            'featured_img' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'status' => ['sometimes', new Enum(ContentStatus::class)],
        ]);

        try {
            MediaSummary::where('id', $id)->update([
                'label' => trim($request->label),
                'media_summary_category_id' => $request->media_summary_category_id,
                'release_year' => $request->release_year,
                'imdb_rating' => $request->imdb_rating,
                'article_location_id' => $request->article_location_id,
                'status' => $status,
            ]);

            $summary->tags()->sync($request->tags ?? []);

            if ($request->hasFile('featured_img')) {
                if ($summary->hasMedia('featured_imgs')) {
                    $summary->clearMediaCollection('featured_imgs');
                }
                $file = $request->file('featured_img');
                $fileName = uniqid() . '.' . strtolower($file->getClientOriginalExtension());
                $summary->addMedia($file)
                    ->usingFileName($fileName)
                    ->withResponsiveImages()
                    ->toMediaCollection('featured_imgs');
            }

            return to_route('admin.media-summaries.index')
                ->with('success', 'Media Summary Updated Successfully.');
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()
                ->with('error', 'Failed to update media summary: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request, int $id)
    {
        $summary = MediaSummary::findOrFail($id);

        // ✅ المحرر/المدير: أي سجل. الكاتب: فقط سجله غير المنشور.
        if (!$request->user()->hasRole(['super-admin', 'editor'])) {
            if ($summary->user_id !== $request->user()->id || $summary->status === 'published') {
                abort(403, 'لا يمكنك حذف هذا الملخص.');
            }
        }

        try {
            $summary->clearMediaCollection('featured_imgs');
            $summary->clearMediaCollection('content_imgs');
            $summary->delete();

            return to_route('admin.media-summaries.index')
                ->with('success', 'Media Summary Deleted Successfully.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Failed to delete: ' . $e->getMessage());
        }
    }

    // ===== رفع صور المحتوى (TinyMCE) =====
    public function uploadContentImg(Request $request)
    {
        if (!$request->user()->hasRole(['super-admin', 'editor', 'writer'])) {
            abort(403);
        }

        $request->validate([
            'media_summary_id' => 'required|numeric|exists:media_summaries,id',  // ✅
            'content_img' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        $summary = MediaSummary::findOrFail($request->media_summary_id);
        if ($request->hasFile('content_img')) {
            $file = $request->file('content_img');
            $fileName = uniqid() . '.' . strtolower($file->getClientOriginalExtension());
            $summary->addMedia($file)
                ->usingFileName($fileName)
                ->withResponsiveImages()
                ->toMediaCollection('content_imgs');
        }

        return response()->json(['content_imgs' => $summary->getMedia('content_imgs')]);
    }

    public function getMediaContentImgs(Request $request)
    {
        $imgs = MediaSummary::findOrFail($request->media_summary_id)->getMedia('content_imgs')->all();
        return response()->json($imgs);
    }

    public function deleteContentImg(Request $request)
    {
        $media = Media::find($request->content_img_id);
        if ($media) {
            $media->delete();
            return response()->json(['message' => 'media deleted successfully']);
        }
        return response()->json(['error' => 'error in delete media'], 400);
    }

    // ===== المراجعة (للمحرر/المدير فقط) =====
    public function pending(Request $request)
    {
        if (!$request->user()->hasRole(['super-admin', 'editor'])) {
            abort(403, 'غير مسموح لك بمراجعة ملخصات الوسائط.');
        }

        $summaries = MediaSummary::with(['translations', 'author'])
            ->where('status', 'pending')
            ->latest()
            ->paginate(10);

        return view('admin.media_summaries.pending', compact('summaries'));
    }

    public function approve(Request $request, int $id)
    {
        if (!$request->user()->hasRole(['super-admin', 'editor'])) {
            abort(403, 'غير مسموح لك بالموافقة.');
        }

        $summary = MediaSummary::findOrFail($id);
        $summary->update(['status' => 'published']);

        return redirect()->route('admin.media-summaries.pending')
            ->with('success', 'Media summary approved and published successfully.');
    }

    public function reject(Request $request, int $id)
    {
        if (!$request->user()->hasRole(['super-admin', 'editor'])) {
            abort(403, 'غير مسموح لك بالرفض.');
        }

        $summary = MediaSummary::findOrFail($id);
        $summary->update(['status' => 'draft']);

        return redirect()->route('admin.media-summaries.pending')
            ->with('success', 'Media summary rejected and moved back to draft.');
    }
}
