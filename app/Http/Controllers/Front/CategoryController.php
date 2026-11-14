<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\ArticleCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class CategoryController extends Controller
{
    public function index()
    {
        $current_locale = LaravelLocalization::getCurrentLocale();
        $cacheKey = "all_categories_page_{$current_locale}";
        //$categories = Cache::remember($cacheKey, 3600, function () use ($current_locale) {
        $categories = ArticleCategory::with([
            'media',
            'translations' => function ($query) use ($current_locale) {
                $query->where('locale', $current_locale);
            },
            'articles' => function ($query) {
                $query->where('status', 'published');
            }
        ])->get();
        //});

        return view('categories.index', compact('categories'));
    }
}
