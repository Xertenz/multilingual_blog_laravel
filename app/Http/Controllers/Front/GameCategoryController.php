<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\GameCategory;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class GameCategoryController extends Controller
{
    public function index()
    {
        $locale = LaravelLocalization::getCurrentLocale();

        $categories = GameCategory::query()
            ->where('is_active', true)
            ->with([
                'translations' => fn($query) => $query->where('locale', $locale),
                'media',
            ])
            ->withCount(['games', 'followers'])
            ->latest()
            ->get();

        return view('game_categories.index', compact('categories'));
    }
}
