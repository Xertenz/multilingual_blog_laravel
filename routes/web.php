<?php

use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

// Front & Shared Controllers
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReactionController;
use App\Http\Controllers\CategoryVisitorController;
use App\Http\Controllers\Front\ArticleSavedController;
use App\Http\Controllers\Front\CategoryFavoriteController;
use App\Http\Controllers\Front\SeriesController as FrontSeriesController;
use App\Livewire\UserProfile;
use App\Livewire\Suggestions;
use App\Http\Controllers\Front\BookController as FrontBookController;

// Admin Controllers
use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\ArticleTranslationController;
use App\Http\Controllers\Admin\BookController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CategoryTranslationController;
use App\Http\Controllers\Admin\CommunityCaseCategoryController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\TagTranslationController;
use App\Http\Controllers\Admin\SeriesController as AdminSeriesController;
use App\Http\Controllers\Admin\SuggestionController;
use App\Http\Controllers\Admin\UserController;
use App\Livewire\Admin\CaseModeration;
use App\Livewire\CommunityBoard;
use App\Livewire\CommunityCaseShow;
use App\Http\Controllers\Auth\SocialController;
use App\Http\Controllers\UserReportController;

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
], function () {




    Route::get('/detective-board', CommunityBoard::class)->name('community.cases.index');
    Route::get('/detective-board/cases/{slug}', CommunityCaseShow::class)->name('community.cases.show');

    Route::get('/books', [FrontBookController::class, 'index'])->name('books.index');
    Route::get('/books/{id}/{slug?}', [FrontBookController::class, 'show'])->where('id', '[0-9]+')->name('books.show');


    Route::get('auth/{provider}', [SocialController::class, 'redirect'])->name('social.redirect');
    Route::get('auth/{provider}/callback', [SocialController::class, 'callback'])->name('social.callback');


    // =========================================================================
    // 1. حسابات الأعضاء والزوار المسجلين (Auth Users)
    // =========================================================================
    Route::middleware('auth')->group(function () {


        Route::post('/reports', [UserReportController::class, 'store'])->name('reports.store');

        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
        Route::get('/profile/{id}', UserProfile::class)->name('profile.show');

        // الإشعارات والتفاعلات
        Route::get('/notifications/fetch', [NotificationController::class, 'fetch'])->name('notifications.fetch');
        Route::get('/notifications/{id}/read', [NotificationController::class, 'read'])->name('notifications.read');
        Route::post('/notifications/mark-read-time', [NotificationController::class, 'updateLastRead'])->name('notifications.markReadTime');

        // التفضيلات والمقالات المحفوظة
        Route::get('/my-favorite-categories', [ProfileController::class, 'favoriteCategories'])->name('profile.favorites');
        Route::post('/categories/{category}/favorite', [CategoryFavoriteController::class, 'toggle'])->name('categories.favorite');
        Route::post('/articles/reactions/toggle', [ReactionController::class, 'toggleArticleReaction'])->name('articles.reactions.toggle');
        Route::get('/my-saved-articles', [ProfileController::class, 'savedArticles'])->name('profile.saved-articles');
        Route::post('/articles/{article}/saved', [ArticleSavedController::class, 'toggle'])->name('articles.saved');
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
    Route::get('/categories', [CategoryVisitorController::class, 'index'])->name('categories.index');
    Route::get('/category/{id}/{slug?}', [CategoryTranslationController::class, 'show'])->name('categories.show');
    Route::get('/tags/{id}/{slug?}', [TagTranslationController::class, 'show'])->name('tags.show');
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

    // =========================================================================
    // 3. لوحة التحكم (Admin Panel Structure)
    // =========================================================================
    Route::group(['prefix' => 'admin', 'middleware' => ['auth']], function () {


        // ---------------------------------------------------------------------
        // أ. المكونات المتاحة للجميع (Super-Admin, Editor & Writer)
        // ---------------------------------------------------------------------
        Route::group(['middleware' => ['role:super-admin|editor|writer']], function () {
            Route::view('/dashboard', 'admin.layout.dashboard')->name('dashboard');

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



            Route::get('/cases/moderation', CaseModeration::class)->name('admin.cases.moderation');

            // مسارات البلاغات
            Route::get('/reports', [ReportController::class, 'index'])->name('admin.reports.index');
            Route::patch('/reports/{report}/status', [ReportController::class, 'updateStatus'])->name('admin.reports.update-status');
            Route::delete('/reports/{report}/content', [ReportController::class, 'destroyContent'])->name('admin.reports.destroy-content');


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
            Route::get('/categories', [CategoryController::class, 'index'])->name('admin.categories.index');
            Route::get('/categories/create', [CategoryTranslationController::class, 'create'])->name('admin.categories.create');
            Route::post('/categories', [CategoryTranslationController::class, 'store'])->name('admin.categories.store');
            Route::get('/categories/{cat_id}/edit', [CategoryTranslationController::class, 'edit'])->name('admin.categories.edit');
            Route::put('/categories/{cat_id}', [CategoryTranslationController::class, 'update'])->name('admin.categories.update');
            Route::delete('/categories/{cat_id}', [CategoryTranslationController::class, 'destroy'])->name('admin.categories.destroy');

            // الوسوم (Tags)
            Route::get('/tags', [TagController::class, 'index'])->name('admin.tags.index');
            Route::get('/tags/create', [TagTranslationController::class, 'create'])->name('admin.tags.create');
            Route::post('/tags', [TagTranslationController::class, 'store'])->name('admin.tags.store');
            Route::get('/tags/{tag}/edit', [TagTranslationController::class, 'edit'])->name('admin.tags.edit');
            Route::put('/tags/{tag}', [TagTranslationController::class, 'update'])->name('admin.tags.update');
            Route::delete('/tags/{tag}', [TagTranslationController::class, 'destroy'])->name('admin.tags.destroy');

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
