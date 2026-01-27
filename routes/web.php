<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ArticleTranslationController;
use App\Http\Controllers\TestController;
use App\Models\Article;
use App\Models\ArticleTranslation;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::view('test', "test");
Route::post('/upload', [TestController::class, 'upload'])->name('test.upload');
Route::get('/showtest', [TestController::class, 'showimgs'])->name('test.show');


Route::get('/articles/{slug}', [ArticleTranslationController::class, 'show'])->name('visitor.articles.show');


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

Route::post('/admin/articles/upload-article-content-img', [ArticleController::class, 'uploadContentImg'])->name('articles.upload.content');
Route::post('/admin/articles/get-article-content-imgs', [ArticleController::class, 'getArticleContentImgs'])->name('articles.get.content');
Route::post('/admin/articles/delete-article-image', [ArticleController::class, 'deleteContentImg'])->name('articles.delete.content_img');


Route::post('/admin/articles/upload-article-test-img', [ArticleController::class, 'test_upload_with_tiny'])->name('articles.upload.test');

    /*
    @php
        $widths = [1200, 1003, 839, 702, 587, 491, 411, 344, 288, 241];
        $links = [];
        foreach($media->responsive_images['responsive']['urls'] as $url) {
            $links[] = asset('storage/article/content/'.$media->created_at->format('Y/m').'/responsive/'.$url);
        }
        $srcset = '';
        for($i=0; $i<min([count($widths), count($links)]); $i++) {
            $srcset .= "$links[$i] $widths[$i]w, ";
        }
    @endphp
    <img
        src="{{$media->getUrl()}}"
        srcset="{{$srcset}}"
        sizes="100vw"
        loading="lazy"
    />
    */