<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ArticleTranslationController;
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::view('test', "test");
Route::post('/upload', [TestController::class, 'upload'])->name('upload.image');



Route::get('/admin/articles', [ArticleController::class, 'index'])->name('articles.index');
Route::get('/admin/articles/create', [ArticleController::class, 'create'])->name('articles.create');
Route::post('/admin/articles', [ArticleController::class, 'store'])->name('articles.store');
Route::get('/admin/articles/{article_id}/edit', [ArticleController::class, 'edit'])->name('articles.edit');
Route::put('/admin/articles/{article_id}', [ArticleController::class, 'update'])->name('articles.update');
Route::delete('/admin/articles/{article_id}', [ArticleController::class, 'destroy'])->name('articles.destroy');

Route::get('/admin/articles-trans', [ArticleTranslationController::class, 'index'])->name('articles-trans.index');
Route::get('/admin/articles-trans/create/{article_id}', [ArticleTranslationController::class, 'create'])->name('articles-trans.create');
Route::post('/admin/articles-trans', [ArticleTranslationController::class, 'store'])->name('articles-trans.store');
Route::get('/admin/articles-trans/{article_id}/edit', [ArticleTranslationController::class, 'edit'])->name('articles-trans.edit');
Route::put('/admin/articles-trans/{article_id}', [ArticleTranslationController::class, 'update'])->name('articles-trans.update');
Route::delete('/admin/articles-trans/{article_id}', [ArticleTranslationController::class, 'destroy'])->name('articles-trans.destroy');
