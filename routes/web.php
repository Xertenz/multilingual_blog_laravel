<?php

use App\Http\Controllers\Admin\ArticleCategoryController;
use App\Http\Controllers\Admin\ArticleCategoryTranslationController;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

// Front & Shared Controllers
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReactionController;
use App\Http\Controllers\Front\CategoryController as FrontCategoryController;
use App\Http\Controllers\Front\ArticleSavedController;
use App\Http\Controllers\Front\CategoryFavoriteController;
use App\Http\Controllers\Front\SeriesController as FrontSeriesController;
use App\Livewire\UserProfile;
use App\Livewire\Suggestions;
use App\Http\Controllers\Front\BookController as FrontBookController;

// Admin Controllers
use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\ArticleLocationController;
use App\Http\Controllers\Admin\ArticleTagController;
use App\Http\Controllers\Admin\ArticleTranslationController;
use App\Http\Controllers\Admin\BookController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CategoryTranslationController;
use App\Http\Controllers\Admin\CommunityCaseCategoryController;
use App\Http\Controllers\Admin\PenaltyController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\ArticleTagTranslationController;
use App\Http\Controllers\Admin\SeriesController as AdminSeriesController;
use App\Http\Controllers\Admin\SuggestionController;
use App\Http\Controllers\Admin\UserController;
use App\Livewire\Admin\CaseModeration;
use App\Livewire\CommunityBoard;
use App\Livewire\CommunityCaseShow;
use App\Http\Controllers\Auth\SocialController;
use App\Http\Controllers\Front\MapController;
use App\Http\Controllers\Admin\MediaSummaryController;
use App\Http\Controllers\Admin\MediaSummaryTranslationController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\UserReportController;
use App\Livewire\AuthorProfile;
use App\Http\Controllers\Admin\BookCategoryController;
use App\Http\Controllers\Admin\BookTagController;
use App\Http\Controllers\Admin\GameCategoryController;
use App\Http\Controllers\Admin\GameController;
use App\Http\Controllers\Admin\GameTagController;
use App\Http\Controllers\Admin\GameTranslationController;
use App\Http\Controllers\Front\GameController as FrontGameController;

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => [
        'localeSessionRedirect',
        'localizationRedirect',
        'localeViewPath',
        \App\Http\Middleware\UpdateUserLocale::class
    ]
], function () {

    // مسارات ملخصات ومراجعات الألعاب (Frontend)
    Route::get('/games', [FrontGameController::class, 'index'])->name('games.index');
    Route::get('/games/{id}/{slug?}', [FrontGameController::class, 'show'])->where('id', '[0-9]+')->name('games.show');


    Route::get('/true-crime-media', [MediaSummaryTranslationController::class, 'index'])->name('media-summaries.index');
    Route::get('/true-crime-media/{id}/{slug?}', [MediaSummaryTranslationController::class, 'show'])->name('media-summaries.show');


    Route::get('/detective-board', CommunityBoard::class)->name('community.cases.index');
    Route::get('/detective-board/cases/{slug}', CommunityCaseShow::class)->name('community.cases.show');

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



    /*
    Route::middleware(['auth', 'banned'])->group(function () {
        // المسارات التي تتطلب حساباً غير محظور (مثل إنشاء القضايا، كتابة التعليقات، إلخ)
        Route::post('/community-cases', [CommunityBoard::class, 'storeCase'])->name('community-board.storeCase');
        Route::post('/comments', [CommentController::class, 'store'])->name('comments.store');
    });
    */


    // =========================================================================
    // 2. الواجهة العامة للموقع (Public Visitors)
    // =========================================================================
    Route::get('/', [HomeController::class, 'index'])->name('home.index');
    Route::get('/categories', [FrontCategoryController::class, 'index'])->name('article-categories.index');
    Route::get('/category/{id}/{slug?}', [ArticleCategoryTranslationController::class, 'show'])->name('article-categories.show');
    Route::get('/tags/{id}/{slug?}', [ArticleTagTranslationController::class, 'show'])->name('article-tags.show');
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

            // إدارة البيانات الأساسية لملخصات الوسائط
            Route::resource('media-summaries', MediaSummaryController::class)->names([
                'index' => 'admin.media-summaries.index',
                'create' => 'admin.media-summaries.create',
                'store' => 'admin.media-summaries.store',
                'edit' => 'admin.media-summaries.edit',
                'update' => 'admin.media-summaries.update',
                'destroy' => 'admin.media-summaries.destroy',
            ]);

            // إدارة ترجمات ملخصات الوسائط بشكل منفصل (على غرار المقالات)
            Route::get('/media-summaries-trans/create/{media_summary_id}', [MediaSummaryTranslationController::class, 'create'])->name('admin.media-summaries-trans.create');
            Route::post('/media-summaries-trans', [MediaSummaryTranslationController::class, 'store'])->name('admin.media-summaries-trans.store');
            Route::get('/media-summaries-trans/{id}/edit', [MediaSummaryTranslationController::class, 'edit'])->name('admin.media-summaries-trans.edit');
            Route::put('/media-summaries-trans/{id}', [MediaSummaryTranslationController::class, 'update'])->name('admin.media-summaries-trans.update');
            Route::delete('/media-summaries-trans/{id}', [MediaSummaryTranslationController::class, 'destroy'])->name('admin.media-summaries-trans.destroy');

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


            // -----------------------------------------------------------------
            // إدارة فئات الألعاب (Game Categories)
            // -----------------------------------------------------------------
            Route::get('/game-categories', [GameCategoryController::class, 'index'])->name('admin.game-categories.index');
            Route::get('/game-categories/create', [GameCategoryController::class, 'create'])->name('admin.game-categories.create');
            Route::post('/game-categories', [GameCategoryController::class, 'store'])->name('admin.game-categories.store');
            Route::get('/game-categories/{cat_id}/edit', [GameCategoryController::class, 'edit'])->name('admin.game-categories.edit');
            Route::put('/game-categories/{cat_id}', [GameCategoryController::class, 'update'])->name('admin.game-categories.update');
            Route::delete('/game-categories/{cat_id}', [GameCategoryController::class, 'destroy'])->name('admin.game-categories.destroy');

            // -----------------------------------------------------------------
            // إدارة وسوم الألعاب (Game Tags)
            // -----------------------------------------------------------------
            Route::get('/game-tags', [GameTagController::class, 'index'])->name('admin.game-tags.index');
            Route::get('/game-tags/create', [GameTagController::class, 'create'])->name('admin.game-tags.create');
            Route::post('/game-tags', [GameTagController::class, 'store'])->name('admin.game-tags.store');
            Route::get('/game-tags/{tag}/edit', [GameTagController::class, 'edit'])->name('admin.game-tags.edit');
            Route::put('/game-tags/{tag}', [GameTagController::class, 'update'])->name('admin.game-tags.update');
            Route::delete('/game-tags/{tag}', [GameTagController::class, 'destroy'])->name('admin.game-tags.destroy');

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


            // -----------------------------------------------------------------
            // إدارة فئات الكتب (Book Categories)
            // -----------------------------------------------------------------
            Route::get('/book-categories', [BookCategoryController::class, 'index'])->name('admin.book-categories.index');
            Route::get('/book-categories/create', [BookCategoryController::class, 'create'])->name('admin.book-categories.create');
            Route::post('/book-categories', [BookCategoryController::class, 'store'])->name('admin.book-categories.store');
            Route::get('/book-categories/{cat_id}/edit', [BookCategoryController::class, 'edit'])->name('admin.book-categories.edit');
            Route::put('/book-categories/{cat_id}', [BookCategoryController::class, 'update'])->name('admin.book-categories.update');
            Route::delete('/book-categories/{cat_id}', [BookCategoryController::class, 'destroy'])->name('admin.book-categories.destroy');

            // -----------------------------------------------------------------
            // إدارة وسوم الكتب (Book Tags)
            // -----------------------------------------------------------------
            Route::get('/book-tags', [BookTagController::class, 'index'])->name('admin.book-tags.index');
            Route::get('/book-tags/create', [BookTagController::class, 'create'])->name('admin.book-tags.create');
            Route::post('/book-tags', [BookTagController::class, 'store'])->name('admin.book-tags.store');
            Route::get('/book-tags/{tag}/edit', [BookTagController::class, 'edit'])->name('admin.book-tags.edit');
            Route::put('/book-tags/{tag}', [BookTagController::class, 'update'])->name('admin.book-tags.update');
            Route::delete('/book-tags/{tag}', [BookTagController::class, 'destroy'])->name('admin.book-tags.destroy');


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

            Route::resource('community-case-categories', CommunityCaseCategoryController::class, [
                'as' => 'admin'
            ]);


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

            // الفئات (Categories)
            Route::get('/categories', [ArticleCategoryController::class, 'index'])->name('admin.article-categories.index');
            Route::get('/categories/create', [ArticleCategoryTranslationController::class, 'create'])->name('admin.article-categories.create');
            Route::post('/categories', [ArticleCategoryTranslationController::class, 'store'])->name('admin.article-categories.store');
            Route::get('/categories/{cat_id}/edit', [ArticleCategoryTranslationController::class, 'edit'])->name('admin.article-categories.edit');
            Route::put('/categories/{cat_id}', [ArticleCategoryTranslationController::class, 'update'])->name('admin.article-categories.update');
            Route::delete('/categories/{cat_id}', [ArticleCategoryTranslationController::class, 'destroy'])->name('admin.article-categories.destroy');

            // الوسوم (Tags)
            Route::get('/tags', [ArticleTagController::class, 'index'])->name('admin.article-tags.index');
            Route::get('/tags/create', [ArticleTagTranslationController::class, 'create'])->name('admin.article-tags.create');
            Route::post('/tags', [ArticleTagTranslationController::class, 'store'])->name('admin.article-tags.store');
            Route::get('/tags/{tag}/edit', [ArticleTagTranslationController::class, 'edit'])->name('admin.article-tags.edit');
            Route::put('/tags/{tag}', [ArticleTagTranslationController::class, 'update'])->name('admin.article-tags.update');
            Route::delete('/tags/{tag}', [ArticleTagTranslationController::class, 'destroy'])->name('admin.article-tags.destroy');

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
