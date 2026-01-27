<?php

namespace App\Providers;

use App\Support\PathGenerator\CustomPathGenerator;
use Illuminate\Support\ServiceProvider;
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
        //
    }
}
