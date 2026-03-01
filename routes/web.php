<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ArticleTranslationController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CategoryTranslationController;
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::view('test', "test");
Route::post('/upload', [TestController::class, 'upload'])->name('test.upload');
Route::get('/showtest', [TestController::class, 'showimgs'])->name('test.show');


Route::get('/articles/{slug}', [ArticleTranslationController::class, 'show'])->name('visitor.articles.show');
Route::post('/articles/content_img_info', [ArticleController::class, 'get_content_img_info'])->name('visitor.articles.content_img.info');

Route::view('admin/dashboard', 'admin.layout.dashboard');


Route::get('/admin/articles', [ArticleController::class, 'index'])->name('admin.articles.index');
Route::get('/admin/articles/create', [ArticleController::class, 'create'])->name('admin.articles.create');
Route::post('/admin/articles', [ArticleController::class, 'store'])->name('admin.articles.store');
Route::get('/admin/articles/{article_id}/edit', [ArticleController::class, 'edit'])->name('admin.articles.edit');
Route::put('/admin/articles/{article_id}', [ArticleController::class, 'update'])->name('admin.articles.update');
Route::delete('/admin/articles/{article_id}', [ArticleController::class, 'destroy'])->name('admin.articles.destroy');

//Route::get('/admin/articles-trans', [ArticleTranslationController::class, 'index'])->name('admin.articles-trans.index');
Route::get('/admin/articles-trans/create/{article_id}', [ArticleTranslationController::class, 'create'])->name('admin.articles-trans.create');
Route::post('/admin/articles-trans', [ArticleTranslationController::class, 'store'])->name('admin.articles-trans.store');
Route::get('/admin/articles-trans/{article_id}/edit', [ArticleTranslationController::class, 'edit'])->name('admin.articles-trans.edit');
Route::put('/admin/articles-trans/{article_id}', [ArticleTranslationController::class, 'update'])->name('admin.articles-trans.update');
Route::delete('/admin/articles-trans/{article_id}', [ArticleTranslationController::class, 'destroy'])->name('admin.articles-trans.destroy');

Route::post('/admin/articles/upload-article-content-img', [ArticleController::class, 'uploadContentImg'])->name('admin.articles.upload.content');
Route::post('/admin/articles/get-article-content-imgs', [ArticleController::class, 'getArticleContentImgs'])->name('admin.articles.get.content');
Route::post('/admin/articles/delete-article-image', [ArticleController::class, 'deleteContentImg'])->name('admin.articles.delete.content_img');

Route::get('/admin/categories/create', [CategoryTranslationController::class, 'create'])->name('admin.categories.create');
Route::get('/admin/categories', [CategoryController::class, 'index'])->name('admin.categories.index');
Route::post('/admin/categories', [CategoryTranslationController::class, 'store'])->name('admin.categories.store');
Route::get('/admin/categories/{cat_id}/edit', [CategoryTranslationController::class, 'edit'])->name('admin.categories.edit');
Route::put('/admin/categories/{cat_id}', [CategoryTranslationController::class, 'update'])->name('admin.categories.update');
Route::delete('/admin/categories/{cat_id}', [CategoryTranslationController::class, 'destroy'])->name('admin.categories.destroy');


//Route::post('/admin/articles/upload-article-test-img', [ArticleController::class, 'test_upload_with_tiny'])->name('articles.upload.test');
