<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ContentStatus;
use App\Enums\MediaType;
use App\Http\Controllers\Controller;
use App\Models\MediaSummary;
use App\Models\ArticleLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class MediaSummaryController extends Controller
{
    public function index()
    {
        $mediaList = MediaSummary::with(['translations', 'media'])->paginate(10);
        return view("admin.media_summaries.index", compact('mediaList'));
    }

    public function create()
    {
        $locations = ArticleLocation::with('translations')->get();
        return view('admin.media_summaries.create', compact('locations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            //'type' => 'required|string|max:255',
            'type' => ['required', new Enum(MediaType::class)],
            'release_year' => 'nullable|integer|min:1900|max:2100',
            'imdb_rating' => 'nullable|numeric|min:0|max:10',
            'article_location_id' => 'nullable|exists:article_locations,id',
            'featured_img' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
            //'status' => 'required|in:draft,pending,published',
            'status' => ['required', new Enum(ContentStatus::class)]
        ]);

        $mediaSummary = MediaSummary::create([
            'user_id' => Auth::id(),
            'type' => $request->type,
            'release_year' => $request->release_year,
            'imdb_rating' => $request->imdb_rating,
            'article_location_id' => $request->article_location_id,
            'status' => $request->status,
            'views' => 0,
        ]);


        if ($request->hasFile('featured_img')) {
            $file = $request->file('featured_img');
            $file_ext = strtolower($file->getClientOriginalExtension());
            $fileName = uniqid() . '.' . $file_ext;

            $mediaSummary->addMediaFromRequest('featured_img')
                ->usingFileName($fileName)
                ->withResponsiveImages()
                ->toMediaCollection('featured_imgs');
        }

        return to_route('admin.media-summaries.index')->with('success', 'Media Summary Created Successfully. Now add translations.');
    }

    public function edit(int $id)
    {
        $mediaSummary = MediaSummary::with(['translations', 'media'])->findOrFail($id);
        $locations = ArticleLocation::with('translations')->get();

        // جلب اللغات المدعومة والترجمات الحالية لتجنب خطأ Undefined variable
        $supportedLocales = LaravelLocalization::getSupportedLocales();
        $existingTrans = $mediaSummary->translations->keyBy('locale');
        $media = $mediaSummary;

        return view('admin.media_summaries.edit', compact('media', 'locations', 'supportedLocales', 'existingTrans'));
    }

    public function update(Request $request, int $id)
    {
        $mediaSummary = MediaSummary::findOrFail($id);

        $request->validate([
            //'type' => 'required|string|max:255',
            'type' => ['required', new Enum(MediaType::class)],
            'release_year' => 'nullable|integer|min:1900|max:2100',
            'imdb_rating' => 'nullable|numeric|min:0|max:10',
            'article_location_id' => 'nullable|exists:article_locations,id',
            'featured_img' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            //'status' => 'required|in:draft,pending,published',
            'status' => ['required', new Enum(ContentStatus::class)]
        ]);

        $mediaSummary->update([
            'type' => $request->type,
            'release_year' => $request->release_year,
            'imdb_rating' => $request->imdb_rating,
            'article_location_id' => $request->article_location_id,
            'status' => $request->status,
        ]);

        if ($request->hasFile('featured_img')) {
            $file = $request->file('featured_img');
            $file_ext = strtolower($file->getClientOriginalExtension());
            $fileName = uniqid() . '.' . $file_ext;

            $mediaSummary->clearMediaCollection('featured_imgs');
            $mediaSummary->addMediaFromRequest('featured_img')
                ->usingFileName($fileName)
                ->withResponsiveImages()
                ->toMediaCollection('featured_imgs');
        }

        return to_route('admin.media-summaries.index')->with('success', 'Media Summary Updated Successfully.');
    }

    public function destroy(int $id)
    {
        $mediaSummary = MediaSummary::findOrFail($id);
        $mediaSummary->clearMediaCollection('featured_imgs');
        $mediaSummary->clearMediaCollection('content_imgs');
        $mediaSummary->delete();

        return to_route('admin.media-summaries.index')->with('success', 'Media Summary Deleted Successfully.');
    }

    // دوال إدارة معرض الصور (Gallery) المطابقة لنظام المقالات
    public function uploadContentImg(Request $request)
    {
        $request->validate([
            'media_summary_id' => 'required|numeric|exists:media_summaries,id',
            'content_img' => "required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048"
        ]);

        $mediaSummary = MediaSummary::findOrFail($request->media_summary_id);
        if ($request->hasFile('content_img')) {
            $file = $request->file('content_img');
            $fileName = uniqid() . '.' . strtolower($file->getClientOriginalExtension());
            $mediaSummary->addMedia($file)
                ->usingFileName($fileName)
                ->withResponsiveImages()
                ->toMediaCollection("content_imgs");
        }

        return response()->json(['content_imgs' => $mediaSummary->getMedia('content_imgs')]);
    }

    public function getMediaContentImgs(Request $request)
    {
        $imgs = MediaSummary::findOrFail($request->media_summary_id)->getMedia('content_imgs')->all();
        return response()->json($imgs);
    }

    public function deleteContentImg(Request $request)
    {
        $media = \Spatie\MediaLibrary\MediaCollections\Models\Media::find($request->content_img_id);
        if ($media) {
            $media->delete();
            return response()->json(['message' => 'media deleted successfully']);
        }
        return response()->json(['error' => 'error in delete media'], 400);
    }
}
