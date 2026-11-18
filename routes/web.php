<?php

use App\Http\Controllers\Admin\ArticleCategoryTranslationController;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

// Front & Shared Controllers
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReactionController;
use App\Http\Controllers\Front\ArticleCategoryController as FrontCategoryController;
use App\Http\Controllers\Front\ArticleSavedController;
use App\Http\Controllers\Front\CategoryFavoriteController;
use App\Http\Controllers\Front\SeriesController as FrontSeriesController;
use App\Livewire\UserProfile;
use App\Livewire\Suggestions;
use App\Http\Controllers\Front\BookController as FrontBookController;

// Admin Controllers
use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\ArticleLocationController;
use App\Http\Controllers\Admin\ArticleTranslationController;
use App\Http\Controllers\Admin\BookController;
use App\Http\Controllers\Admin\PenaltyController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SeriesController as AdminSeriesController;
use App\Http\Controllers\Admin\SuggestionController;
use App\Http\Controllers\Admin\UserController;
use App\Livewire\Admin\CaseModeration;
use App\Livewire\CommunityBoard;
use App\Livewire\CommunityCaseShow;
use App\Http\Controllers\Auth\SocialController;
use App\Http\Controllers\Front\MapController;
use App\Http\Controllers\Admin\MediaSummaryController;
use App\Http\Controllers\Admin\MediaSummaryTranslationController as AdminMediaSummaryTranslationController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\UserReportController;
use App\Livewire\AuthorProfile;
use App\Http\Controllers\Admin\CommunityCaseController;
use App\Http\Controllers\Admin\GameController;
use App\Http\Controllers\Admin\GameTranslationController;

use App\Http\Controllers\Front\GameTagTranslationController as FrontGameTagTranslationController;

use App\Http\Controllers\Front\ArticleTagTranslationController as FrontArticleTagTranslationController;
use App\Http\Controllers\Front\GameController as FrontGameController;
use App\Http\Controllers\Front\MediaSummaryCategoryTranslationController;
use App\Http\Controllers\Front\MediaSummaryTranslationController as FrontMediaSummaryTranslationController;

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => [
        'localeSessionRedirect',
        'localizationRedirect',
        'localeViewPath',
        \App\Http\Middleware\UpdateUserLocale::class
    ]
], function () {

    Route::get('/media-categories/{id}/{slug?}', [MediaSummaryCategoryTranslationController::class, 'show'])
        ->name('media-summary-categories.show');

    // مسارات ملخصات ومراجعات الألعاب (Frontend)
    Route::get('/games', [FrontGameController::class, 'index'])->name('games.index');
    Route::get('/games/{id}/{slug?}', [FrontGameController::class, 'show'])->where('id', '[0-9]+')->name('games.show');

    Route::get('/games/tag/{id}/{slug?}', [FrontGameTagTranslationController::class, 'show'])
        ->name('game-tags.show');


    Route::get('/media-summaries', [FrontMediaSummaryTranslationController::class, 'index'])->name('media-summaries.index');
    Route::get('/media-summaries/{id}/{slug?}', [FrontMediaSummaryTranslationController::class, 'show'])->name('media-summaries.show');


    Route::get('/community', CommunityBoard::class)->name('community.cases.index');
    Route::get('/community/cases/{slug}', CommunityCaseShow::class)->name('community.cases.show');

    Route::get('/books', [FrontBookController::class, 'index'])->name('books.index');
    Route::get('/books/{id}/{slug?}', [FrontBookController::class, 'show'])->where('id', '[0-9]+')->name('books.show');


    Route::get('auth/{provider}', [SocialController::class, 'redirect'])->name('social.redirect');
    Route::get('auth/{provider}/callback', [SocialController::class, 'callback'])->name('social.callback');

    Route::get('/authors/{id}', AuthorProfile::class)->name('authors.show');


    // =========================================================================
    // 1. حسابات الأعضاء والزوار المسجلين (Auth Users)
    // =========================================================================
    Route::middleware('auth')->group(function () {

        Route::post('/authors/{author}/follow', [AuthorController::class, 'toggleFollow'])->name('authors.follow');

        Route::post('/reports', [UserReportController::class, 'store'])->name('reports.store');

        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
        Route::get('/profile/following-authors', [ProfileController::class, 'followingAuthors'])->name('profile.following-authors');
        Route::get('/profile/{id}', UserProfile::class)->name('profile.show');
        // التفضيلات والمقالات المحفوظة
        Route::get('/my-favorite-categories', [ProfileController::class, 'favoriteCategories'])->name('profile.favorites');
        Route::post('/categories/{category}/favorite', [CategoryFavoriteController::class, 'toggle'])->name('article-categories.favorite');
        Route::post('/articles/reactions/toggle', [ReactionController::class, 'toggleArticleReaction'])->name('articles.reactions.toggle');
        Route::get('/my-saved-articles', [ProfileController::class, 'savedArticles'])->name('profile.saved-articles');
        Route::post('/articles/{article}/saved', [ArticleSavedController::class, 'toggle'])->name('articles.saved');

        // الإشعارات والتفاعلات
        Route::get('/notifications/fetch', [NotificationController::class, 'fetch'])->name('notifications.fetch');
        Route::get('/notifications/{id}/read', [NotificationController::class, 'read'])->name('notifications.read');
        Route::post('/notifications/mark-read-time', [NotificationController::class, 'updateLastRead'])->name('notifications.markReadTime');
    });

    require __DIR__ . '/auth.php';


    // =========================================================================
    // 2. الواجهة العامة للموقع (Public Visitors)
    // =========================================================================
    Route::get('/', [HomeController::class, 'index'])->name('home.index');
    Route::get('/categories', [FrontCategoryController::class, 'index'])->name('article-categories.index');
    Route::get('/category/{id}/{slug?}', [ArticleCategoryTranslationController::class, 'show'])->name('article-categories.show');
    Route::get('/tags/{id}/{slug?}', [FrontArticleTagTranslationController::class, 'show'])->name('article-tags.show');
    Route::get('/search', SearchController::class)->name('search');
    Route::get('/articles/{id}/{slug?}', [ArticleTranslationController::class, 'show'])->where('id', '[0-9]+')->name('articles.show');
    Route::get('/series/{id}/{slug?}', [FrontSeriesController::class, 'show'])->name('series.show');
    Route::post('/articles/content_img_info', [ArticleController::class, 'get_content_img_info'])->name('visitor.articles.content_img.info');

    // الصفحات الثابتة والمقترحات
    Route::view('/privacy', 'privacy')->name('privacy');
    Route::view('/terms', 'terms')->name('terms');
    Route::view('/contact', 'contact')->name('contact');
    Route::post('/contact', function () {})->name('contact.submit');
    Route::view('/about', 'about')->name('about');
    Route::get('/suggestions', Suggestions::class)->name('suggestions.index');
    //Route::get('/map', [MapController::class, 'index'])->name('map');
    Route::get('/map', [MapController::class, 'index'])->name('map');

    // =========================================================================
    // 3. لوحة التحكم (Admin Panel Structure)
    // =========================================================================
    Route::group(['prefix' => 'admin', 'middleware' => ['auth']], function () {

        // ---------------------------------------------------------------------
        // أ. المكونات المتاحة للجميع (Super-Admin, Editor & Writer)
        // ---------------------------------------------------------------------
        Route::group(['middleware' => ['role:super-admin|editor|writer']], function () {
            Route::view('/dashboard', 'admin.layout.dashboard')->name('dashboard');

            // عرض القائمة (الجميع)
            Route::get('/media-summaries', [MediaSummaryController::class, 'index'])->name('admin.media-summaries.index');

            // الترجمات (الجميع)
            Route::get('/media-summaries-trans/create/{media_summary_id}', [AdminMediaSummaryTranslationController::class, 'create'])->name('admin.media-summaries-trans.create');
            Route::post('/media-summaries-trans', [AdminMediaSummaryTranslationController::class, 'store'])->name('admin.media-summaries-trans.store');
            Route::get('/media-summaries-trans/{id}/edit', [AdminMediaSummaryTranslationController::class, 'edit'])->name('admin.media-summaries-trans.edit');
            Route::put('/media-summaries-trans/{id}', [AdminMediaSummaryTranslationController::class, 'update'])->name('admin.media-summaries-trans.update');
            Route::delete('/media-summaries-trans/{id}', [AdminMediaSummaryTranslationController::class, 'destroy'])->name('admin.media-summaries-trans.destroy');

            // رفع صور المحتوى (TinyMCE)
            Route::post('/media-summaries/upload-content-img', [MediaSummaryController::class, 'uploadContentImg'])->name('admin.media-summaries.upload.content');
            Route::post('/media-summaries/get-content-imgs', [MediaSummaryController::class, 'getMediaContentImgs'])->name('admin.media-summaries.get.content');
            Route::post('/media-summaries/delete-image', [MediaSummaryController::class, 'deleteContentImg'])->name('admin.media-summaries.delete.content_img');

            // الاطلاع على المقالات الأساسية
            Route::get('/articles', [ArticleController::class, 'index'])->name('admin.articles.index');

            // إنشاء وتعديل ترجمات المقالات
            Route::get('/articles-trans/create/{article_id}', [ArticleTranslationController::class, 'create'])->name('admin.articles-trans.create');
            Route::post('/articles-trans', [ArticleTranslationController::class, 'store'])->name('admin.articles-trans.store');
            Route::get('/articles-trans/{article_id}/edit', [ArticleTranslationController::class, 'edit'])->name('admin.articles-trans.edit');
            Route::put('/articles-trans/{article_id}', [ArticleTranslationController::class, 'update'])->name('admin.articles-trans.update');
            Route::delete('/articles-trans/{article_id}', [ArticleTranslationController::class, 'destroy'])->name('admin.articles-trans.destroy');

            // رفع وإدارة صور المحتوى
            Route::post('/articles/upload-article-content-img', [ArticleController::class, 'uploadContentImg'])->name('admin.articles.upload.content');
            Route::post('/articles/get-article-content-imgs', [ArticleController::class, 'getArticleContentImgs'])->name('admin.articles.get.content');
            Route::post('/articles/delete-article-image', [ArticleController::class, 'deleteContentImg'])->name('admin.articles.delete.content_img');
        });

        // ---------------------------------------------------------------------
        // ب. إدارة الهيكل العام والمراجعة (Super-Admin & Editor Only)
        // ---------------------------------------------------------------------
        Route::group(['middleware' => ['role:super-admin|editor']], function () {




            // ✅ Media Summary Categories
            Route::view('/media-summary-categories', 'admin.media_summary_categories.index')->name('admin.media-summary-categories.index');
            // -----------------------------------------------------------------
            // إدارة وسوم ملخصات الوسائط (Media Summary Tags)
            // -----------------------------------------------------------------
            Route::view('/media-summary-tags', 'admin.media_summary_tags.index')->name('admin.media-summary-tags.index');

            // إنشاء/تعديل/حذف MediaSummary الأساسي
            Route::get('/media-summaries/create', [MediaSummaryController::class, 'create'])->name('admin.media-summaries.create');
            Route::post('/media-summaries', [MediaSummaryController::class, 'store'])->name('admin.media-summaries.store');
            Route::get('/media-summaries/{id}/edit', [MediaSummaryController::class, 'edit'])->name('admin.media-summaries.edit');
            Route::put('/media-summaries/{id}', [MediaSummaryController::class, 'update'])->name('admin.media-summaries.update');
            Route::delete('/media-summaries/{id}', [MediaSummaryController::class, 'destroy'])->name('admin.media-summaries.destroy');

            // المراجعة (pending/approve/reject) — أضفها بعد إضافة الدوال في Controller
            Route::get('/media-summaries/pending', [MediaSummaryController::class, 'pending'])->name('admin.media-summaries.pending');
            Route::post('/media-summaries/{id}/approve', [MediaSummaryController::class, 'approve'])->name('admin.media-summaries.approve');
            Route::post('/media-summaries/{id}/reject', [MediaSummaryController::class, 'reject'])->name('admin.media-summaries.reject');


            // ✅ Game Categories
            Route::view('/game-categories', 'admin.game_categories.index')->name('admin.game-categories.index');
            // -----------------------------------------------------------------
            // إدارة وسوم الألعاب (Game Tags)
            // -----------------------------------------------------------------
            Route::view('/game-tags', 'admin.game_tags.index')->name('admin.game-tags.index');

            // -----------------------------------------------------------------
            // إدارة ملخصات ومراجعات الألعاب (Games)
            // -----------------------------------------------------------------
            // داخل مجموعة الأدمن المسموحة للكاتب/المحرر/الأدمن
            Route::get('/games', [GameController::class, 'index'])->name('admin.games.index');
            Route::get('/games/create', [GameController::class, 'create'])->name('admin.games.create');
            Route::post('/games', [GameController::class, 'store'])->name('admin.games.store');
            Route::get('/games/{id}/edit', [GameController::class, 'edit'])->name('admin.games.edit');
            Route::put('/games/{id}', [GameController::class, 'update'])->name('admin.games.update');
            Route::delete('/games/{id}', [GameController::class, 'destroy'])->name('admin.games.destroy');

            // مسارات ترجمة الألعاب المنفصلة (على غرار articles-trans)
            Route::get('/games-trans/create/{game_id}', [GameTranslationController::class, 'create'])->name('admin.games-trans.create');
            Route::post('/games-trans', [GameTranslationController::class, 'store'])->name('admin.games-trans.store');
            Route::get('/games-trans/{id}/edit', [GameTranslationController::class, 'edit'])->name('admin.games-trans.edit');
            Route::put('/games-trans/{id}', [GameTranslationController::class, 'update'])->name('admin.games-trans.update');
            Route::delete('/games-trans/{id}', [GameTranslationController::class, 'destroy'])->name('admin.games-trans.destroy');

            // رفع وإدارة صور المحتوى التفاعلي داخل TinyMCE
            Route::post('/games/upload-content-img', [GameController::class, 'uploadContentImg'])->name('admin.games.upload.content');
            Route::post('/games/get-content-imgs', [GameController::class, 'getGameContentImgs'])->name('admin.games.get.content');
            Route::post('/games/delete-image', [GameController::class, 'deleteContentImg'])->name('admin.games.delete.content_img');

            // داخل مجموعة role:super-admin|editor
            Route::get('/games/pending', [GameController::class, 'pending'])->name('admin.games.pending');
            Route::post('/games/{id}/approve', [GameController::class, 'approve'])->name('admin.games.approve');
            Route::post('/games/{id}/reject', [GameController::class, 'reject'])->name('admin.games.reject');


            // ✅ Book Categories
            Route::view('/book-categories', 'admin.book_categories.index')->name('admin.book-categories.index');
            // -----------------------------------------------------------------
            // إدارة وسوم الكتب (Book Tags)
            // -----------------------------------------------------------------
            Route::view('/book-tags', 'admin.book_tags.index')->name('admin.book-tags.index');

            Route::get('/article-locations', [ArticleLocationController::class, 'index'])->name('admin.article-locations.index');
            Route::post('/articles/{article}/locations', [ArticleLocationController::class, 'store'])->name('admin.article-locations.store');
            Route::put('/article-locations/{location}', [ArticleLocationController::class, 'update'])->name('admin.article-locations.update');
            Route::delete('/article-locations/{location}', [ArticleLocationController::class, 'destroy'])->name('admin.article-locations.destroy');

            Route::get('/cases/moderation', CaseModeration::class)->name('admin.cases.moderation');

            // مسارات البلاغات
            Route::get('/reports', [ReportController::class, 'index'])->name('admin.reports.index');
            Route::patch('/reports/{report}/status', [ReportController::class, 'updateStatus'])->name('admin.reports.update-status');
            Route::delete('/reports/{report}/content', [ReportController::class, 'destroyContent'])->name('admin.reports.destroy-content');


            Route::post('/users/{user}/penalties', [PenaltyController::class, 'store'])->name('admin.penalties.store');


            //Route::resource('/books', BookController::class)->names('admin.books');
            Route::get('/books', [BookController::class, 'index'])->name('admin.books.index');
            Route::get('/books/create', [BookController::class, 'create'])->name('admin.books.create');
            Route::post('/books/store', [BookController::class, 'store'])->name('admin.books.store');
            Route::get('/books/{book}/edit', [BookController::class, 'edit'])->name('admin.books.edit');
            Route::put('/books/{book}', [BookController::class, 'update'])->name('admin.books.update');
            Route::delete('/books/{book}', [BookController::class, 'destroy'])->name('admin.books.destroy');

            // Community Case
            // ✅ Community Case Categories
            Route::view('/community-case-categories', 'admin.community_case_categories.index')->name('admin.community-case-categories.index');

            Route::view('/community-case-tags', 'admin.community_case_tags.index')
                ->name('admin.community-case-tags.index');

            Route::get('/community-cases', [CommunityCaseController::class, 'index'])->name('admin.community-cases.index');
            Route::patch('/community-cases/{id}/approve', [CommunityCaseController::class, 'approve'])->name('admin.community-cases.approve');
            Route::patch('/community-cases/{id}/reject', [CommunityCaseController::class, 'reject'])->name('admin.community-cases.reject');
            Route::delete('/community-cases/{id}', [CommunityCaseController::class, 'destroy'])->name('admin.community-cases.destroy');


            // الهيكل الأساسي للمقالات
            Route::get('/articles/create', [ArticleController::class, 'create'])->name('admin.articles.create');
            Route::post('/articles', [ArticleController::class, 'store'])->name('admin.articles.store');
            Route::get('/articles/{article_id}/edit', [ArticleController::class, 'edit'])->name('admin.articles.edit');
            Route::put('/articles/{article_id}', [ArticleController::class, 'update'])->name('admin.articles.update');
            Route::delete('/articles/{article_id}', [ArticleController::class, 'destroy'])->name('admin.articles.destroy');

            // المقالات المعلقة والاعتماد
            Route::get('/articles/pending', [ArticleController::class, 'pending'])->name('admin.articles.pending');
            Route::post('/articles/{id}/approve', [ArticleController::class, 'approve'])->name('admin.articles.approve');
            Route::post('/articles/{id}/reject', [ArticleController::class, 'reject'])->name('admin.articles.reject');

            // ✅ Article Categories
            Route::view('/categories', 'admin.article_categories.index')->name('admin.article-categories.index');

            // الوسوم (Tags)
            Route::view('/tags', 'admin.article_tags.index')->name('admin.article-tags.index');

            // السلاسل (Series)
            Route::get('/series', [AdminSeriesController::class, 'index'])->name('admin.series.index');
            Route::get('/series/create', [AdminSeriesController::class, 'create'])->name('admin.series.create');
            Route::post('/series', [AdminSeriesController::class, 'store'])->name('admin.series.store');
            Route::get('/series/{series}/edit', [AdminSeriesController::class, 'edit'])->name('admin.series.edit');
            Route::put('/series/{series}', [AdminSeriesController::class, 'update'])->name('admin.series.update');
            Route::delete('/series/{series}', [AdminSeriesController::class, 'destroy'])->name('admin.series.destroy');

            // اقتراحات المقالات (Suggestions)
            Route::get('/suggestions', [SuggestionController::class, 'index'])->name('admin.suggestions.index');
            Route::patch('/suggestions/{suggestion}/status', [SuggestionController::class, 'updateStatus'])->name('admin.suggestions.status');
            Route::delete('/suggestions/{suggestion}', [SuggestionController::class, 'destroy'])->name('admin.suggestions.destroy');
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
