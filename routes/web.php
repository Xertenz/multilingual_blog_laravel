<?php

use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

// =============================================================================
// Front & Shared Controllers
// =============================================================================
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReactionController;
use App\Http\Controllers\UserReportController;
use App\Http\Controllers\Auth\SocialController;
use App\Http\Controllers\Front\MapController;
use App\Http\Controllers\Front\SeriesController as FrontSeriesController;
use App\Http\Controllers\Front\FollowController as FrontFollowController;
use App\Http\Controllers\Front\SavedItemController as FrontSavedItemController;

// Articles (Front)
use App\Http\Controllers\Front\ArticleCategoryController as FrontCategoryController;
use App\Http\Controllers\Front\ArticleCategoryTranslationController as FrontArticleCategoryTranslationController;
use App\Http\Controllers\Front\ArticleTagTranslationController as FrontArticleTagTranslationController;

// Books (Front)
use App\Http\Controllers\Front\BookController as FrontBookController;
use App\Http\Controllers\Front\BookCategoryController as FrontBookCategoryController;
use App\Http\Controllers\Front\BookCategoryTranslationController as FrontBookCategoryTranslationController;
use App\Http\Controllers\Front\BookTagTranslationController as FrontBookTagTranslationController;

// Games (Front)
use App\Http\Controllers\Front\GameController as FrontGameController;
use App\Http\Controllers\Front\GameCategoryController as FrontGameCategoryController;
use App\Http\Controllers\Front\GameCategoryTranslationController as FrontGameCategoryTranslationController;
use App\Http\Controllers\Front\GameTagTranslationController as FrontGameTagTranslationController;

// Media Summaries (Front)
use App\Http\Controllers\Front\MediaSummaryCategoryController as FrontMediaSummaryCategoryController;
use App\Http\Controllers\Front\MediaSummaryTranslationController as FrontMediaSummaryTranslationController;
use App\Http\Controllers\Front\MediaSummaryCategoryTranslationController as FrontMediaSummaryCategoryTranslationController;
use App\Http\Controllers\Front\MediaSummaryTagTranslationController as FrontMediaSummaryTagTranslationController;

// =============================================================================
// Livewire Components
// =============================================================================
use App\Livewire\AuthorProfile;
use App\Livewire\UserProfile;
use App\Livewire\Suggestions;
use App\Livewire\CommunityBoard;
use App\Livewire\CommunityCaseShow;
use App\Livewire\Admin\CaseModeration;

// =============================================================================
// Admin Controllers
// =============================================================================
use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\ArticleTranslationController;
use App\Http\Controllers\Admin\BookController;
use App\Http\Controllers\Admin\GameController;
use App\Http\Controllers\Admin\GameTranslationController;
use App\Http\Controllers\Admin\MediaSummaryController;
use App\Http\Controllers\Admin\MediaSummaryTranslationController as AdminMediaSummaryTranslationController;
use App\Http\Controllers\Admin\SeriesController as AdminSeriesController;
use App\Http\Controllers\Admin\CommunityCaseController;
use App\Http\Controllers\Admin\SuggestionController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\PenaltyController;
use App\Http\Controllers\Admin\TimelineController;
use App\Http\Controllers\Admin\UserController;
use App\Livewire\Profile\MyCommunityCases;

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => [
        'localeSessionRedirect',
        'localizationRedirect',
        'localeViewPath',
        \App\Http\Middleware\UpdateUserLocale::class,
    ],
], function () {

    // =========================================================================
    // 1. الواجهة العامة للموقع (Public Visitors)
    // =========================================================================
    Route::get('/', [HomeController::class, 'index'])->name('home.index');
    Route::get('/search', SearchController::class)->name('search');
    Route::get('/map', [MapController::class, 'index'])->name('map');
    Route::get('/suggestions', Suggestions::class)->name('suggestions.index');
    Route::get('/series/{id}/{slug?}', [FrontSeriesController::class, 'show'])
        ->where('id', '[0-9]+')
        ->name('series.show');

    // المقالات (Articles)
    Route::get('/articles/categories', [FrontCategoryController::class, 'index'])
        ->name('article-categories.index');
    Route::get('/articles/category/{id}/{slug?}', [FrontArticleCategoryTranslationController::class, 'show'])
        ->where('id', '[0-9]+')
        ->name('article-categories.show');
    Route::get('/articles/tag/{id}/{slug?}', [FrontArticleTagTranslationController::class, 'show'])
        ->where('id', '[0-9]+')
        ->name('article-tags.show');
    Route::get('/articles/{id}/{slug?}', [ArticleTranslationController::class, 'show'])
        ->where('id', '[0-9]+')
        ->name('articles.show');
    Route::post('/articles/content_img_info', [ArticleController::class, 'get_content_img_info'])
        ->name('visitor.articles.content_img.info');

    // الكتب (Books)
    Route::get('/books', [FrontBookController::class, 'index'])->name('books.index');
    Route::get('/books/categories', [FrontBookCategoryController::class, 'index'])
        ->name('book-categories.index');
    Route::get('/books/category/{id}/{slug?}', [FrontBookCategoryTranslationController::class, 'show'])
        ->where('id', '[0-9]+')
        ->name('book-categories.show');
    Route::get('/books/tag/{id}/{slug?}', [FrontBookTagTranslationController::class, 'show'])
        ->where('id', '[0-9]+')
        ->name('book-tags.show');
    Route::get('/books/{id}/{slug?}', [FrontBookController::class, 'show'])
        ->where('id', '[0-9]+')
        ->name('books.show');

    // الألعاب (Games)
    Route::get('/games', [FrontGameController::class, 'index'])->name('games.index');
    Route::get('/games/categories', [FrontGameCategoryController::class, 'index'])
        ->name('game-categories.index');
    Route::get('/games/category/{id}/{slug?}', [FrontGameCategoryTranslationController::class, 'show'])
        ->where('id', '[0-9]+')
        ->name('game-categories.show');
    Route::get('/games/tag/{id}/{slug?}', [FrontGameTagTranslationController::class, 'show'])
        ->where('id', '[0-9]+')
        ->name('game-tags.show');
    Route::get('/games/{id}/{slug?}', [FrontGameController::class, 'show'])
        ->where('id', '[0-9]+')
        ->name('games.show');

    // ملخصات الوسائط (Media Summaries)
    Route::get('/media-summaries', [FrontMediaSummaryTranslationController::class, 'index'])
        ->name('media-summaries.index');
    Route::get('/media-summaries/categories', [FrontMediaSummaryCategoryController::class, 'index'])
        ->name('media-summary-categories.index');
    Route::get('/media-summaries/category/{id}/{slug?}', [FrontMediaSummaryCategoryTranslationController::class, 'show'])
        ->where('id', '[0-9]+')
        ->name('media-summary-categories.show');
    Route::get('/media-summaries/tag/{id}/{slug?}', [FrontMediaSummaryTagTranslationController::class, 'show'])
        ->where('id', '[0-9]+')
        ->name('media-summary-tags.show');
    Route::get('/media-summaries/{id}/{slug?}', [FrontMediaSummaryTranslationController::class, 'show'])
        ->where('id', '[0-9]+')
        ->name('media-summaries.show');

    // المجتمع والكتّاب (Community & Authors)
    Route::get('/community', CommunityBoard::class)->name('community.cases.index');
    //Route::get('/community/cases/{slug}', CommunityCaseShow::class)->name('community.cases.show');
    Route::get('/community/cases/{id}/{slug?}', CommunityCaseShow::class)
        ->where('id', '[0-9]+')
        ->name('community.cases.show');
    Route::get('/authors/{id}', AuthorProfile::class)
        ->where('id', '[0-9]+')
        ->name('authors.show');

    // الصفحات الثابتة (Static Pages)
    Route::view('/privacy', 'privacy')->name('privacy');
    Route::view('/terms', 'terms')->name('terms');
    Route::view('/about', 'about')->name('about');
    Route::view('/contact', 'contact')->name('contact');
    Route::post('/contact', function () {})->name('contact.submit');

    // تسجيل الدخول الاجتماعي (Social Auth)
    Route::get('auth/{provider}', [SocialController::class, 'redirect'])->name('social.redirect');
    Route::get('auth/{provider}/callback', [SocialController::class, 'callback'])->name('social.callback');


    // =========================================================================
    // 2. حسابات الأعضاء والزوار المسجلين (Authenticated Users)
    // =========================================================================
    Route::middleware('auth')->group(function () {

        // قضايا المجتمع الخاصة بالعضو (My Cases)
        Route::get('/profile/cases', MyCommunityCases::class)->name('profile.cases');

        // العناصر المحفوظة الموحدة (Saved Items)
        Route::get('/profile/saved', [ProfileController::class, 'savedItems'])->name('profile.saved');

        // المتابعات الموحدة (Following)
        Route::get('/profile/following', [ProfileController::class, 'following'])->name('profile.following');

        // نظام الحفظ الموحد (Bookmarks / Saved Items)
        Route::post('/saved/{type}/{id}/toggle', [FrontSavedItemController::class, 'toggle'])
            ->whereIn('type', [
                'article',
                'book',
                'game',
                'media_summary',
            ])
            ->where('id', '[0-9]+')
            ->name('saved.toggle');

        // نظام المتابعة الموحد (Follows)
        Route::post('/follows/{type}/{id}/toggle', [FrontFollowController::class, 'toggle'])
            ->whereIn('type', [
                'author',
                'article_category',
                'article_tag',
                'book_category',
                'book_tag',
                'game_category',
                'game_tag',
                'media_summary_category',
                'media_summary_tag',
            ])
            ->where('id', '[0-9]+')
            ->name('follows.toggle');

        // الملف الشخصي (Profile)
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
        Route::get('/profile/{id}', UserProfile::class)
            ->where('id', '[0-9]+')
            ->name('profile.show');

        // التفاعلات والبلاغات (Reactions & Reports)
        Route::post('/articles/reactions/toggle', [ReactionController::class, 'toggleArticleReaction'])
            ->name('articles.reactions.toggle');
        Route::post('/reports', [UserReportController::class, 'store'])->name('reports.store');

        // الإشعارات (Notifications)
        Route::get('/notifications/fetch', [NotificationController::class, 'fetch'])->name('notifications.fetch');
        Route::get('/notifications/{id}/read', [NotificationController::class, 'read'])->name('notifications.read');
        Route::post('/notifications/mark-read-time', [NotificationController::class, 'updateLastRead'])->name('notifications.markReadTime');
    });

    require __DIR__ . '/auth.php';


    // =========================================================================
    // 3. لوحة التحكم (Admin Panel Structure)
    // =========================================================================
    Route::group(['prefix' => 'admin', 'middleware' => ['auth']], function () {

        // ---------------------------------------------------------------------
        // أ. المكونات المتاحة للجميع (Super-Admin, Editor & Writer)
        // ---------------------------------------------------------------------
        Route::group(['middleware' => ['role:super-admin|editor|writer']], function () {
            Route::view('/dashboard', 'admin.layout.dashboard')->name('dashboard');

            // المقالات والترجمات (عرض القائمة وإدارة الترجمات والصور)
            Route::get('/articles', [ArticleController::class, 'index'])->name('admin.articles.index');
            Route::get('/articles-trans/create/{article_id}', [ArticleTranslationController::class, 'create'])->name('admin.articles-trans.create');
            Route::post('/articles-trans', [ArticleTranslationController::class, 'store'])->name('admin.articles-trans.store');
            Route::get('/articles-trans/{article_id}/edit', [ArticleTranslationController::class, 'edit'])->name('admin.articles-trans.edit');
            Route::put('/articles-trans/{article_id}', [ArticleTranslationController::class, 'update'])->name('admin.articles-trans.update');
            Route::delete('/articles-trans/{article_id}', [ArticleTranslationController::class, 'destroy'])->name('admin.articles-trans.destroy');

            Route::post('/articles/upload-article-content-img', [ArticleController::class, 'uploadContentImg'])->name('admin.articles.upload.content');
            Route::post('/articles/get-article-content-imgs', [ArticleController::class, 'getArticleContentImgs'])->name('admin.articles.get.content');
            Route::post('/articles/delete-article-image', [ArticleController::class, 'deleteContentImg'])->name('admin.articles.delete.content_img');

            // ملخصات الوسائط والترجمات (عرض القائمة وإدارة الترجمات والصور)
            Route::get('/media-summaries', [MediaSummaryController::class, 'index'])->name('admin.media-summaries.index');
            Route::get('/media-summaries-trans/create/{media_summary_id}', [AdminMediaSummaryTranslationController::class, 'create'])->name('admin.media-summaries-trans.create');
            Route::post('/media-summaries-trans', [AdminMediaSummaryTranslationController::class, 'store'])->name('admin.media-summaries-trans.store');
            Route::get('/media-summaries-trans/{id}/edit', [AdminMediaSummaryTranslationController::class, 'edit'])->name('admin.media-summaries-trans.edit');
            Route::put('/media-summaries-trans/{id}', [AdminMediaSummaryTranslationController::class, 'update'])->name('admin.media-summaries-trans.update');
            Route::delete('/media-summaries-trans/{id}', [AdminMediaSummaryTranslationController::class, 'destroy'])->name('admin.media-summaries-trans.destroy');

            Route::post('/media-summaries/upload-content-img', [MediaSummaryController::class, 'uploadContentImg'])->name('admin.media-summaries.upload.content');
            Route::post('/media-summaries/get-content-imgs', [MediaSummaryController::class, 'getMediaContentImgs'])->name('admin.media-summaries.get.content');
            Route::post('/media-summaries/delete-image', [MediaSummaryController::class, 'deleteContentImg'])->name('admin.media-summaries.delete.content_img');
        });

        // ---------------------------------------------------------------------
        // ب. إدارة الهيكل العام والمراجعة (Super-Admin & Editor Only)
        // ---------------------------------------------------------------------
        Route::group(['middleware' => ['role:super-admin|editor']], function () {

            Route::get('/timelines', [TimelineController::class, 'index'])->name('admin.timelines.index');
            Route::post('/articles/{article}/timelines', [TimelineController::class, 'store'])->name('admin.timelines.store');
            Route::put('/timelines/{timeline}', [TimelineController::class, 'update'])->name('admin.timelines.update');
            Route::delete('/timelines/{timeline}', [TimelineController::class, 'destroy'])->name('admin.timelines.destroy');

            // المقالات الأساسية والمراجعة والمواقع الجغرافية
            Route::get('/articles/create', [ArticleController::class, 'create'])->name('admin.articles.create');
            Route::post('/articles', [ArticleController::class, 'store'])->name('admin.articles.store');
            Route::get('/articles/{article_id}/edit', [ArticleController::class, 'edit'])->name('admin.articles.edit');
            Route::put('/articles/{article_id}', [ArticleController::class, 'update'])->name('admin.articles.update');
            Route::delete('/articles/{article_id}', [ArticleController::class, 'destroy'])->name('admin.articles.destroy');

            Route::get('/articles/pending', [ArticleController::class, 'pending'])->name('admin.articles.pending');
            Route::post('/articles/{id}/approve', [ArticleController::class, 'approve'])->name('admin.articles.approve');
            Route::post('/articles/{id}/reject', [ArticleController::class, 'reject'])->name('admin.articles.reject');

            Route::view('/categories', 'admin.article_categories.index')->name('admin.article-categories.index');
            Route::view('/tags', 'admin.article_tags.index')->name('admin.article-tags.index');

            Route::get('/locations', [LocationController::class, 'index'])->name('admin.locations.index');
            Route::post('/articles/{article}/locations', [LocationController::class, 'store'])->name('admin.locations.store');
            Route::put('/locations/{location}', [LocationController::class, 'update'])->name('admin.locations.update');
            Route::delete('/locations/{location}', [LocationController::class, 'destroy'])->name('admin.locations.destroy');

            // الكتب وفئاتها ووسومها
            Route::get('/books', [BookController::class, 'index'])->name('admin.books.index');
            Route::get('/books/create', [BookController::class, 'create'])->name('admin.books.create');
            Route::post('/books/store', [BookController::class, 'store'])->name('admin.books.store');
            Route::get('/books/{book}/edit', [BookController::class, 'edit'])->name('admin.books.edit');
            Route::put('/books/{book}', [BookController::class, 'update'])->name('admin.books.update');
            Route::delete('/books/{book}', [BookController::class, 'destroy'])->name('admin.books.destroy');

            Route::view('/book-categories', 'admin.book_categories.index')->name('admin.book-categories.index');
            Route::view('/book-tags', 'admin.book_tags.index')->name('admin.book-tags.index');

            // الألعاب وفئاتها ووسومها وترجماتها
            Route::get('/games', [GameController::class, 'index'])->name('admin.games.index');
            Route::get('/games/create', [GameController::class, 'create'])->name('admin.games.create');
            Route::post('/games', [GameController::class, 'store'])->name('admin.games.store');
            Route::get('/games/{id}/edit', [GameController::class, 'edit'])->name('admin.games.edit');
            Route::put('/games/{id}', [GameController::class, 'update'])->name('admin.games.update');
            Route::delete('/games/{id}', [GameController::class, 'destroy'])->name('admin.games.destroy');

            Route::get('/games/pending', [GameController::class, 'pending'])->name('admin.games.pending');
            Route::post('/games/{id}/approve', [GameController::class, 'approve'])->name('admin.games.approve');
            Route::post('/games/{id}/reject', [GameController::class, 'reject'])->name('admin.games.reject');

            Route::get('/games-trans/create/{game_id}', [GameTranslationController::class, 'create'])->name('admin.games-trans.create');
            Route::post('/games-trans', [GameTranslationController::class, 'store'])->name('admin.games-trans.store');
            Route::get('/games-trans/{id}/edit', [GameTranslationController::class, 'edit'])->name('admin.games-trans.edit');
            Route::put('/games-trans/{id}', [GameTranslationController::class, 'update'])->name('admin.games-trans.update');
            Route::delete('/games-trans/{id}', [GameTranslationController::class, 'destroy'])->name('admin.games-trans.destroy');

            Route::post('/games/upload-content-img', [GameController::class, 'uploadContentImg'])->name('admin.games.upload.content');
            Route::post('/games/get-content-imgs', [GameController::class, 'getGameContentImgs'])->name('admin.games.get.content');
            Route::post('/games/delete-image', [GameController::class, 'deleteContentImg'])->name('admin.games.delete.content_img');

            Route::view('/game-categories', 'admin.game_categories.index')->name('admin.game-categories.index');
            Route::view('/game-tags', 'admin.game_tags.index')->name('admin.game-tags.index');

            // ملخصات الوسائط الأساسية وفئاتها ووسومها
            Route::get('/media-summaries/create', [MediaSummaryController::class, 'create'])->name('admin.media-summaries.create');
            Route::post('/media-summaries', [MediaSummaryController::class, 'store'])->name('admin.media-summaries.store');
            Route::get('/media-summaries/{id}/edit', [MediaSummaryController::class, 'edit'])->name('admin.media-summaries.edit');
            Route::put('/media-summaries/{id}', [MediaSummaryController::class, 'update'])->name('admin.media-summaries.update');
            Route::delete('/media-summaries/{id}', [MediaSummaryController::class, 'destroy'])->name('admin.media-summaries.destroy');

            Route::get('/media-summaries/pending', [MediaSummaryController::class, 'pending'])->name('admin.media-summaries.pending');
            Route::post('/media-summaries/{id}/approve', [MediaSummaryController::class, 'approve'])->name('admin.media-summaries.approve');
            Route::post('/media-summaries/{id}/reject', [MediaSummaryController::class, 'reject'])->name('admin.media-summaries.reject');

            Route::view('/media-summary-categories', 'admin.media_summary_categories.index')->name('admin.media-summary-categories.index');
            Route::view('/media-summary-tags', 'admin.media_summary_tags.index')->name('admin.media-summary-tags.index');

            // قضايا المجتمع (Community Cases)
            Route::view('/community-case-categories', 'admin.community_case_categories.index')->name('admin.community-case-categories.index');
            Route::view('/community-case-tags', 'admin.community_case_tags.index')->name('admin.community-case-tags.index');
            Route::get('/cases/moderation', CaseModeration::class)->name('admin.cases.moderation');

            Route::get('/community-cases', [CommunityCaseController::class, 'index'])->name('admin.community-cases.index');
            Route::patch('/community-cases/{id}/approve', [CommunityCaseController::class, 'approve'])->name('admin.community-cases.approve');
            Route::patch('/community-cases/{id}/reject', [CommunityCaseController::class, 'reject'])->name('admin.community-cases.reject');
            Route::delete('/community-cases/{id}', [CommunityCaseController::class, 'destroy'])->name('admin.community-cases.destroy');

            // السلاسل والاقتراحات والبلاغات والعقوبات
            Route::get('/series', [AdminSeriesController::class, 'index'])->name('admin.series.index');
            Route::get('/series/create', [AdminSeriesController::class, 'create'])->name('admin.series.create');
            Route::post('/series', [AdminSeriesController::class, 'store'])->name('admin.series.store');
            Route::get('/series/{series}/edit', [AdminSeriesController::class, 'edit'])->name('admin.series.edit');
            Route::put('/series/{series}', [AdminSeriesController::class, 'update'])->name('admin.series.update');
            Route::delete('/series/{series}', [AdminSeriesController::class, 'destroy'])->name('admin.series.destroy');

            Route::get('/suggestions', [SuggestionController::class, 'index'])->name('admin.suggestions.index');
            Route::patch('/suggestions/{suggestion}/status', [SuggestionController::class, 'updateStatus'])->name('admin.suggestions.status');
            Route::delete('/suggestions/{suggestion}', [SuggestionController::class, 'destroy'])->name('admin.suggestions.destroy');

            Route::get('/reports', [ReportController::class, 'index'])->name('admin.reports.index');
            Route::patch('/reports/{report}/status', [ReportController::class, 'updateStatus'])->name('admin.reports.update-status');
            Route::delete('/reports/{report}/content', [ReportController::class, 'destroyContent'])->name('admin.reports.destroy-content');

            Route::post('/users/{user}/penalties', [PenaltyController::class, 'store'])->name('admin.penalties.store');
        });

        // ---------------------------------------------------------------------
        // ج. إدارة الأعضاء والمستخدمين (Super-Admin Only)
        // ---------------------------------------------------------------------
        Route::group(['middleware' => ['role:super-admin']], function () {
            Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
            Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
            Route::put('/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
        });
    });
});
