<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class CategoryVisitorController extends Controller
{
    public function index()
    {
        $current_locale = LaravelLocalization::getCurrentLocale();
        $cacheKey = "all_categories_page_{$current_locale}";
        $categories = Cache::remember($cacheKey, 3600, function () use ($current_locale) {
            return Category::with([
                'media',
                'translations' => function ($query) use ($current_locale) {
                    $query->where('locale', $current_locale);
                },
                'articles' => function ($query) {
                    $query->where('status', 'published');
                }
            ])->get();
        });

        return view('categories.index', compact('categories'));
    }
}
