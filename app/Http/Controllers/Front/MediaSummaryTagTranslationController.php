<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\MediaSummaryTagTranslation;
use App\Models\MediaSummaryTranslation;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class MediaSummaryTagTranslationController extends Controller
{
    public function show(int $id, ?string $slug = null)
    {
        $tag_trans = MediaSummaryTagTranslation::with([
            'tag' => function ($query) {
                $query->where('is_active', true);
            },
            'tag.translations',
        ])->find($id);

        if (!$tag_trans || !$tag_trans->tag) {
            return to_route('media-summaries.index');
        }

        $currentLocale = LaravelLocalization::getCurrentLocale();

        // التحويل إلى ترجمة الوسم نفسها باللغة الحالية إن وُجدت
        if ($tag_trans->locale !== $currentLocale) {
            $targetTrans = $tag_trans->tag->translations
                ? $tag_trans->tag->translations->firstWhere('locale', $currentLocale)
                : null;

            if ($targetTrans) {
                return redirect()->route('media-summary-tags.show', [
                    'id'   => $targetTrans->id,
                    'slug' => $targetTrans->slug,
                ]);
            }
        }

        // تصحيح الـ slug في الرابط لدعم SEO
        if (!empty($tag_trans->slug) && $slug !== $tag_trans->slug) {
            return redirect()->route('media-summary-tags.show', [
                'id'   => $tag_trans->id,
                'slug' => $tag_trans->slug,
            ], 301);
        }

        $tagId = $tag_trans->media_summary_tag_id ?? $tag_trans->tag->id;

        $summaries = MediaSummaryTranslation::with([
            'mediaSummary.media',
            'mediaSummary.category.translations',
            'mediaSummary.tags.translations',
            'mediaSummary.location.translations',
        ])
            ->where('locale', $currentLocale)
            ->where('status', 'published')
            ->whereHas('mediaSummary', function ($q) use ($tagId) {
                $q->where('status', 'published')
                    ->whereHas('tags', function ($tagQuery) use ($tagId) {
                        $tagQuery->where('media_summary_tags.id', $tagId);
                    });
            })
            ->latest()
            ->paginate(12);

        return view('media_summaries.tag', compact('tag_trans', 'summaries'));
    }
}
