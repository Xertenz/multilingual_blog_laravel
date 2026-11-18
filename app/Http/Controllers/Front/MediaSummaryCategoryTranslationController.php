<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\MediaSummary;
use App\Models\MediaSummaryCategoryTranslation;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class MediaSummaryCategoryTranslationController extends Controller
{
    public function show(int $category_trans_id, ?string $slug = null)
    {
        // 1. جلب ترجمة الفئة مع الفئة الأساسية وترجماتها
        $category_trans = MediaSummaryCategoryTranslation::with(['category.translations'])
            ->find($category_trans_id);

        if (!$category_trans) {
            return to_route('home.index');
        }

        $current_locale = LaravelLocalization::getCurrentLocale();

        // 2. التحقق من تطابق اللغة
        if ($category_trans->locale !== $current_locale) {
            $target = $category_trans->category->translations
                ->where('locale', $current_locale)
                ->first();

            if ($target) {
                return redirect()->route('media-summary-categories.show', [
                    'id'   => $target->id,
                    'slug' => $target->slug,
                ]);
            }

            return to_route('home.index')->with('info', __('This category is not available in your language.'));
        }

        // 3. تصحيح الـ slug في الـ URL (SEO)
        if (empty($slug) || $category_trans->slug !== $slug) {
            return redirect()->route('media-summary-categories.show', [
                'id'   => $category_trans->id,
                'slug' => $category_trans->slug,
            ]);
        }

        // 4. جلب ملخصات الوسائط المنشورة في هذه الفئة
        $mediaSummaries = MediaSummary::with([
            'translations',
            'media',
            'user',
        ])
            ->where('media_summary_category_id', $category_trans->category->id)
            ->where('status', 'published')
            ->latest()
            ->paginate(12);

        return view('media_summary_category', compact('category_trans', 'mediaSummaries'));
    }
}
