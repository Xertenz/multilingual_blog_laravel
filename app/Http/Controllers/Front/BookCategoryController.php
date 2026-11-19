<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\BookCategory;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class BookCategoryController extends Controller
{
    public function index()
    {
        $locale = LaravelLocalization::getCurrentLocale();

        $categories = BookCategory::query()
            ->where('is_active', true)
            ->with([
                'translations' => fn($query) => $query->where('locale', $locale),
                'media',
            ])
            ->withCount(['books', 'followers'])
            ->latest()
            ->get();

        return view('book_categories.index', compact('categories'));
    }
}
