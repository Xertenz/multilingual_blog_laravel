<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\BookTranslation;
use App\Models\GameTranslation;
use App\Models\MediaSummaryTranslation;
use Illuminate\Support\Facades\Cache;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class HomeController extends Controller
{
    /*
    public function index()
    {
        // 1. جلب المقالة البارزة (مثلاً آخر مقالة مميزة أو أحدث مقالة)
        $featuredArticle = Article::with(['translations', 'category', 'category.translations'])
            ->where('status', 'published')
            ->latest()
            ->first();

        // 2. جلب بقية المقالات المتبقية
        $articles = Article::with(['translations', 'category', 'category.translations', 'author'])
            ->where('status', 'published')
            ->when($featuredArticle, fn($q) => $q->where('id', '!=', $featuredArticle->id))
            ->latest()
            ->paginate(6);

        // 3. جلب الفئات مع عدد المقالات فيها
        $categories = ArticleCategory::withCount('articles')->get();

        return view('home', compact('featuredArticle', 'articles', 'categories'));
    }
    */


    public function index()
    {
        $locale = LaravelLocalization::getCurrentLocale();

        $data = Cache::remember("home_page_{$locale}", 600, function () use ($locale) {
            $baseArticleQuery = Article::query()
                ->where('status', 'published')
                ->whereHas('translations', function ($q) use ($locale) {
                    $q->where('locale', $locale)->where('status', 'published');
                })
                ->with([
                    'translations' => function ($q) use ($locale) {
                        $q->where('locale', $locale)->where('status', 'published');
                    },
                    'category' => function ($q) {
                        $q->where('is_active', true);
                    },
                    'category.translations' => function ($q) use ($locale) {
                        $q->where('locale', $locale);
                    },
                    'author',
                    'media',
                ]);

            $featuredArticle = (clone $baseArticleQuery)
                ->latest()
                ->first();

            $articles = (clone $baseArticleQuery)
                ->when($featuredArticle, fn($q) => $q->where('id', '!=', $featuredArticle->id))
                ->latest()
                ->take(8)
                ->get();

            $categories = ArticleCategory::query()
                ->where('is_active', true)
                ->whereHas('translations', function ($q) use ($locale) {
                    $q->where('locale', $locale);
                })
                ->with([
                    'translations' => function ($q) use ($locale) {
                        $q->where('locale', $locale);
                    },
                ])
                ->withCount([
                    'articles as published_articles_count' => function ($q) use ($locale) {
                        $q->where('status', 'published')
                            ->whereHas('translations', function ($tq) use ($locale) {
                                $tq->where('locale', $locale)->where('status', 'published');
                            });
                    },
                ])
                ->orderByDesc('published_articles_count')
                ->take(12)
                ->get();

            $latestMedia = MediaSummaryTranslation::query()
                ->where('locale', $locale)
                ->where('status', 'published')
                ->whereHas('mediaSummary', fn($q) => $q->where('status', 'published'))
                ->with(['mediaSummary.category.translations', 'mediaSummary.media'])
                ->latest()
                ->take(4)
                ->get();

            $latestBooks = BookTranslation::query()
                ->where('locale', $locale)
                ->with(['book.category.translations', 'book.media', 'media', 'author'])
                ->latest()
                ->take(4)
                ->get();

            $latestGames = GameTranslation::query()
                ->where('locale', $locale)
                ->where('status', 'published')
                ->whereHas('game', fn($q) => $q->where('status', 'published'))
                ->with(['game.category.translations', 'game.media', 'media'])
                ->latest()
                ->take(4)
                ->get();

            return compact(
                'featuredArticle',
                'articles',
                'categories',
                'latestMedia',
                'latestBooks',
                'latestGames'
            );
        });

        return view('home', $data);
    }
}
