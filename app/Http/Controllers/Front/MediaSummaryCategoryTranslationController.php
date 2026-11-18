<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\MediaSummaryCategoryTranslation;
use App\Models\MediaSummaryTranslation;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class MediaSummaryCategoryTranslationController extends Controller
{
    public function show(int $id, ?string $slug = null)
    {
        $category_trans = MediaSummaryCategoryTranslation::with([
            'category' => function ($query) {
                $query->where('is_active', true);
            },
            'category.translations',
        ])->find($id);

        if (!$category_trans || !$category_trans->category) {
            return to_route('media-summaries.index');
        }

        $currentLocale = LaravelLocalization::getCurrentLocale();

        // التحويل إلى ترجمة الفئة نفسها باللغة الحالية إن وُجدت
        if ($category_trans->locale !== $currentLocale) {
            $targetTrans = $category_trans->category->translations
                ? $category_trans->category->translations->firstWhere('locale', $currentLocale)
                : null;

            if ($targetTrans) {
                return redirect()->route('media-summary-categories.show', [
                    'id'   => $targetTrans->id,
                    'slug' => $targetTrans->slug,
                ]);
            }
        }

        // تصحيح الـ slug في الرابط لدعم SEO
        if (!empty($category_trans->slug) && $slug !== $category_trans->slug) {
            return redirect()->route('media-summary-categories.show', [
                'id'   => $category_trans->id,
                'slug' => $category_trans->slug,
            ], 301);
        }

        $categoryId = $category_trans->media_summary_category_id ?? $category_trans->category->id;

        $summaries = MediaSummaryTranslation::with([
            'mediaSummary.media',
            'mediaSummary.category.translations',
            'mediaSummary.tags.translations',
            'mediaSummary.location.translations',
        ])
            ->where('locale', $currentLocale)
            ->where('status', 'published')
            ->whereHas('mediaSummary', function ($q) use ($categoryId) {
                $q->where('status', 'published')
                    ->where('media_summary_category_id', $categoryId);
            })
            ->latest()
            ->paginate(12);

        return view('media_summaries.category', compact('category_trans', 'summaries'));
    }
}
