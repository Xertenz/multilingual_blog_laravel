<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ContentStatus;
use App\Http\Controllers\Controller;
use App\Models\MediaSummary;
use App\Models\MediaSummaryTranslation;
use DOMDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Enum;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaSummaryTranslationController extends Controller
{
    public function index(Request $request)
    {
        $currentLocale = LaravelLocalization::getCurrentLocale();

        // جلب الترجمات المنشورة للغة الحالية والتي يتبع لها ملخص مغطى بحالة "منشور"
        $query = MediaSummaryTranslation::with([
            'mediaSummary.media',
            'mediaSummary.location.translations'
        ])
            ->where('locale', $currentLocale)
            ->where('status', 'published')
            ->whereHas('mediaSummary', function ($q) {
                $q->where('status', 'published');
            });

        // فلترة اختيارية حسب نوع العمل (movie, series, documentary)
        if ($request->filled('type')) {
            $query->whereHas('mediaSummary', function ($q) use ($request) {
                $q->where('type', $request->type);
            });
        }

        $summaries = $query->latest()->paginate(12);

        return view('media_summaries.index', compact('summaries'));
    }

    public function create(Request $request, int $media_summary_id)
    {
        $mediaSummary = MediaSummary::findOrFail($media_summary_id);
        return view('admin.media_summaries.create-trans', compact('mediaSummary', 'media_summary_id'));
    }

    public function store(Request $request)
    {
        $supportedLocales = implode(',', LaravelLocalization::getSupportedLanguagesKeys());

        $request->validate([
            'media_summary_id' => 'required|numeric|exists:media_summaries,id',
            'locale' => [
                'required',
                'in:' . $supportedLocales,
                Rule::unique('media_summary_translations', 'locale')->where(function ($query) use ($request) {
                    return $query->where('media_summary_id', (int)$request->media_summary_id);
                })
            ],
            'title' => 'required|string|max:255',
            'slug' => [
                'required',
                'string',
                Rule::unique('media_summary_translations', 'slug')->where(function ($query) use ($request) {
                    return $query->where('locale', $request->locale);
                })
            ],
            'synopsis' => 'nullable|string',
            'review_analysis' => 'nullable|string',
            'meta_title' => 'required|string|max:255',
            'meta_description' => 'required|string',
            //'status' => 'required|in:draft,pending,published',
            'status' => ['required', new Enum(ContentStatus::class)]
        ]);

        $slug = Str::slug($request->slug) ?: Str::slug($request->title);

        MediaSummaryTranslation::create([
            'media_summary_id' => $request->media_summary_id,
            'user_id' => Auth::id(),
            'locale' => $request->locale,
            'title' => trim($request->title),
            'slug' => $slug,
            'synopsis' => trim($request->synopsis),
            'review_analysis' => trim($request->review_analysis),
            'meta_title' => trim($request->meta_title),
            'meta_description' => trim($request->meta_description),
            'status' => $request->status,
            'views' => 0,
        ]);

        return to_route('admin.media-summaries.index')->with('success', 'Media Translation Added Successfully.');
    }




    public function show(int $media_trans_id, ?string $slug = null)
    {
        $currentLocale = LaravelLocalization::getCurrentLocale();

        $trans = MediaSummaryTranslation::with([
            'mediaSummary.media',
            'mediaSummary.translations',
            'mediaSummary.location.translations'
        ])->find($media_trans_id);

        // التحقق من أن العمل الأساسي والترجمة منشوران
        if (!$trans || $trans->mediaSummary->status?->value != "published" || $trans->status?->value != "published") {
            return redirect()->route('home.index');
        }

        $trans->increment('views');
        $trans->mediaSummary->increment('views');

        // معالجة صور المحتوى (Synopsis و Review Analysis) لتوليد srcset تلقائياً تماماً مثل المقالات
        foreach (['synopsis', 'review_analysis'] as $field) {
            if (!empty($trans->{$field})) {
                $doc = new DOMDocument();
                @$doc->loadHTML(
                    mb_convert_encoding($trans->{$field}, 'HTML-ENTITIES', 'UTF-8'),
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
                $trans->{$field} = $doc->saveHTML();
            }
        }

        $media_trans = $trans;

        if ($media_trans->locale !== $currentLocale) {
            $target_translation = $media_trans->mediaSummary->translations
                ->where('locale', $currentLocale)
                ->where('status', 'published')
                ->first();

            if ($target_translation) {
                return redirect()->route('media-summaries.show', [
                    'id'   => $target_translation->id,
                    'slug' => $target_translation->slug
                ]);
            } else {
                return to_route('home.index')->with('info', __('This media summary is not available in your language.'));
            }
        }

        if (empty($slug) || $media_trans->slug !== $slug) {
            return redirect()->route('media-summaries.show', [
                'id'   => $media_trans->id,
                'slug' => $media_trans->slug
            ], 301);
        }

        return view('media_summaries.show', compact('media_trans'));
    }




    public function edit(Request $request, int $id)
    {
        $translation = MediaSummaryTranslation::with('mediaSummary')->findOrFail($id);
        $mediaSummary = $translation->mediaSummary; // جلب العمل الأساسي للوصول إلى صور المعرض

        return view('admin.media_summaries.edit-trans', compact('translation', 'mediaSummary'));
    }

    public function update(Request $request, int $id)
    {
        $translation = MediaSummaryTranslation::findOrFail($id);
        $supportedLocales = implode(',', LaravelLocalization::getSupportedLanguagesKeys());

        $request->validate([
            'locale' => [
                'required',
                'in:' . $supportedLocales,
                Rule::unique('media_summary_translations')->where(function ($query) use ($translation) {
                    return $query->where('media_summary_id', $translation->media_summary_id);
                })->ignore($id),
            ],
            'title' => 'required|string|max:255',
            'slug' => [
                'required',
                'string',
                Rule::unique('media_summary_translations')->where(function ($query) use ($request) {
                    return $query->where('locale', $request->locale);
                })->ignore($id)
            ],
            'synopsis' => 'nullable|string',
            'review_analysis' => 'nullable|string',
            'meta_title' => 'required|string|max:255',
            'meta_description' => 'required|string',
            //'status' => 'required|in:draft,pending,published',
            'status' => ['required', new Enum(ContentStatus::class)]
        ]);

        $slug = Str::slug($request->slug) ?: Str::slug($request->title);

        $translation->update([
            'locale' => $request->locale,
            'title' => trim($request->title),
            'slug' => $slug,
            'synopsis' => trim($request->synopsis),
            'review_analysis' => trim($request->review_analysis),
            'meta_title' => trim($request->meta_title),
            'meta_description' => trim($request->meta_description),
            'status' => $request->status,
        ]);

        return to_route('admin.media-summaries.index')->with('success', 'Media Translation Updated Successfully.');
    }




    public function destroy(Request $request, int $id)
    {
        $translation = MediaSummaryTranslation::findOrFail($id);
        $translation->delete();

        return to_route('admin.media-summaries.index')->with('success', 'Media Translation Deleted Successfully.');
    }
}
