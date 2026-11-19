<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\MediaSummaryCategory;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class MediaSummaryCategoryController extends Controller
{
    public function index()
    {
        $locale = LaravelLocalization::getCurrentLocale();

        $categories = MediaSummaryCategory::query()
            ->with([
                'translations' => fn($query) => $query->where('locale', $locale),
                'media',
            ])
            ->withCount(['mediaSummaries', 'followers'])
            ->latest()
            ->get();

        return view('media_summary_categories.index', compact('categories'));
    }
}
