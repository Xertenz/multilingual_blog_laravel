<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');
*/



use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\ArticleTranslationController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CategoryTranslationController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReactionController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;


Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
], function () {

    // For Auth
    Route::middleware('auth')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        Route::get('/notifications/fetch', [NotificationController::class, 'fetch'])->name('notifications.fetch');
        Route::get('/notifications/{id}/read', [NotificationController::class, 'read'])->name('notifications.read');
    });
    require __DIR__ . '/auth.php';

    // For all users and visitors
    Route::get('/', function () {
        return view('welcome');
    })->name('home');
    Route::get('/articles/{id}/{slug?}', [ArticleTranslationController::class, 'show'])
        ->where('id', '[0-9]+')
        ->name('articles.show');
    Route::post('/articles/content_img_info', [ArticleController::class, 'get_content_img_info'])->name('visitor.articles.content_img.info');

    // For Users only (not visitors)
    Route::post('/articles/comments', [CommentController::class, 'store'])
        ->middleware('auth')
        ->name('comments.store');
    Route::put('/articles/comments', [CommentController::class, 'update'])
        ->middleware('auth')
        ->name('comments.update');
    Route::delete('/articles/comments', [CommentController::class, 'delete'])
        ->middleware('auth')
        ->name('comments.delete');

    Route::post('/articles/reactions/toggle', [ReactionController::class, 'toggleArticleReaction'])
        ->middleware('auth')
        ->name('articles.reactions.toggle');

    // For Admin(s)
    Route::group(['middleware' => ['auth', 'admin'], 'prefix' => "admin"], function () {
        Route::view('/dashboard', 'admin.layout.dashboard')->name('dashboard');

        Route::get('/articles', [ArticleController::class, 'index'])->name('admin.articles.index');
        Route::get('/articles/create', [ArticleController::class, 'create'])->name('admin.articles.create');
        Route::post('/articles', [ArticleController::class, 'store'])->name('admin.articles.store');
        Route::get('/articles/{article_id}/edit', [ArticleController::class, 'edit'])->name('admin.articles.edit');
        Route::put('/articles/{article_id}', [ArticleController::class, 'update'])->name('admin.articles.update');
        Route::delete('/articles/{article_id}', [ArticleController::class, 'destroy'])->name('admin.articles.destroy');

        //Route::get('/admin/articles-trans', [ArticleTranslationController::class, 'index'])->name('admin.articles-trans.index');
        Route::get('/articles-trans/create/{article_id}', [ArticleTranslationController::class, 'create'])->name('admin.articles-trans.create');
        Route::post('/articles-trans', [ArticleTranslationController::class, 'store'])->name('admin.articles-trans.store');
        Route::get('/articles-trans/{article_id}/edit', [ArticleTranslationController::class, 'edit'])->name('admin.articles-trans.edit');
        Route::put('/articles-trans/{article_id}', [ArticleTranslationController::class, 'update'])->name('admin.articles-trans.update');
        Route::delete('/articles-trans/{article_id}', [ArticleTranslationController::class, 'destroy'])->name('admin.articles-trans.destroy');

        Route::post('/articles/upload-article-content-img', [ArticleController::class, 'uploadContentImg'])->name('admin.articles.upload.content');
        Route::post('/articles/get-article-content-imgs', [ArticleController::class, 'getArticleContentImgs'])->name('admin.articles.get.content');
        Route::post('/articles/delete-article-image', [ArticleController::class, 'deleteContentImg'])->name('admin.articles.delete.content_img');

        Route::get('/categories/create', [CategoryTranslationController::class, 'create'])->name('admin.categories.create');
        Route::get('/categories', [CategoryController::class, 'index'])->name('admin.categories.index');
        Route::post('/categories', [CategoryTranslationController::class, 'store'])->name('admin.categories.store');
        Route::get('/categories/{cat_id}/edit', [CategoryTranslationController::class, 'edit'])->name('admin.categories.edit');
        Route::put('/categories/{cat_id}', [CategoryTranslationController::class, 'update'])->name('admin.categories.update');
        Route::delete('/categories/{cat_id}', [CategoryTranslationController::class, 'destroy'])->name('admin.categories.destroy');
    });

    Route::view('/privacy', 'privacy')->name('privacy');
    Route::view('/terms', 'terms')->name('terms');
    Route::view('/contact', 'contact')->name('contact');
    Route::post('/contact', function () {})->name('contact.submit');
    Route::view('/about', 'about')->name('about');
});
