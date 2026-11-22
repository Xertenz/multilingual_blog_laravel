<?php

namespace App\Providers;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\ArticleTag;
use App\Models\Book;
use App\Models\BookCategory;
use App\Models\BookTag;
use App\Models\Category;
use App\Models\CommunityCase;
use App\Models\Game;
use App\Models\GameCategory;
use App\Models\GameTag;
use App\Models\MediaSummary;
use App\Models\MediaSummaryCategory;
use App\Models\MediaSummaryTag;
use App\Models\User;
use App\Support\PathGenerator\CustomPathGenerator;
use App\Models\Comment;
use App\Models\Timeline;
use Illuminate\Database\Eloquent\Relations\Relation;
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
        Relation::morphMap([
            'author'                  => User::class,
            'article_category'        => ArticleCategory::class,
            'article_tag'             => ArticleTag::class,
            'book_category'           => BookCategory::class,
            'book_tag'                => BookTag::class,
            'game_category'           => GameCategory::class,
            'game_tag'                => GameTag::class,
            'media_summary_category'  => MediaSummaryCategory::class,
            'media_summary_tag'       => MediaSummaryTag::class,

            'article'                => Article::class,
            'book'                   => Book::class,
            'game'                   => Game::class,
            'media_summary'          => MediaSummary::class,
            'community_case'         => CommunityCase::class,

            'comment'                => Comment::class,

            'timeline'               => Timeline::class

        ]);
    }
}
