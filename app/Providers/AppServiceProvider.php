<?php

namespace App\Providers;

use App\Models\Category;
use App\Support\PathGenerator\CustomPathGenerator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        /*
        $this->app->bind(PathGenerator::class, function ($app) {
            return new CustomPathGenerator();
        });
        */
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('partials.navbar', function ($navbar_view) {
            $current_locale = LaravelLocalization::getCurrentLocale();
            $cacheKey = "navbar_categories_{$current_locale}";

            $categories = Cache::remember($cacheKey, 3600, function () use ($current_locale) {
                return Category::with(['translations' => function ($query) use ($current_locale) {
                    $query->where('locale', $current_locale);
                }])
                    ->whereHas('translations', function ($query) use ($current_locale) {
                        $query->where('locale', $current_locale);
                    })
                    ->orderBy('label', 'asc')
                    ->get();
            });
            $navbar_view->with('categories', $categories);
        });
    }
}
