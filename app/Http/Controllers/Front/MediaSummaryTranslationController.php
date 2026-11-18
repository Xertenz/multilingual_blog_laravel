<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\MediaSummaryTranslation;
use DOMDocument;
use Illuminate\Http\Request;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaSummaryTranslationController extends Controller
{
    /**
     * عرض كل ملخصات الوسائط المنشورة (الواجهة الأمامية)
     */
    public function index(Request $request)
    {
        $currentLocale = LaravelLocalization::getCurrentLocale();

        $summaries = MediaSummaryTranslation::with([
            'mediaSummary.media',
            'mediaSummary.category.translations',
            'mediaSummary.tags.translations',
            'mediaSummary.location.translations',
        ])
            ->where('locale', $currentLocale)
            ->where('status', 'published')
            ->whereHas('mediaSummary', function ($q) {
                $q->where('status', 'published');
            })
            ->latest()
            ->paginate(12);

        return view('media_summaries.index', compact('summaries'));
    }

    /**
     * عرض ملخص وسائط واحد (الواجهة الأمامية)
     */
    public function show(int $id, ?string $slug = null)
    {
        $currentLocale = LaravelLocalization::getCurrentLocale();

        $trans = MediaSummaryTranslation::with([
            'mediaSummary.media',
            'mediaSummary.translations',
            'mediaSummary.category.translations',
            'mediaSummary.tags.translations',
            'mediaSummary.location.translations',
        ])->find($id);

        if (!$trans) {
            return to_route('home.index')->with('error', __('Media summary not found.'));
        }

        if (($trans->mediaSummary->status?->value ?? $trans->mediaSummary->status) !== 'published') {
            return to_route('home.index')->with('info', __('This media summary is not published yet.'));
        }

        if (($trans->status?->value ?? $trans->status) !== 'published') {
            return to_route('home.index')->with('info', __('This translation is not published yet.'));
        }

        $trans->increment('views');
        $trans->mediaSummary->increment('views');

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
            $target = $media_trans->mediaSummary->translations
                ->where('locale', $currentLocale)
                ->where('status', 'published')
                ->first();

            if ($target) {
                return redirect()->route('media-summaries.show', [
                    'id' => $target->id,
                    'slug' => $target->slug,
                ]);
            }
            return to_route('home.index')->with('info', __('This media summary is not available in your language.'));
        }

        if (empty($slug) || $media_trans->slug !== $slug) {
            return redirect()->route('media-summaries.show', [
                'id' => $media_trans->id,
                'slug' => $media_trans->slug,
            ], 301);
        }

        return view('media_summaries.show', compact('media_trans'));
    }
}
