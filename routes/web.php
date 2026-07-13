<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\ArticleTranslationController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CategoryTranslationController;
use App\Http\Controllers\Admin\TagTranslationController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\Front\ArticleFavoriteController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReactionController;
use App\Http\Controllers\Front\CategoryFavoriteController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
], function () {

    // ==========================================
    // 1. للمستخدمين المسجلين فقط (Auth Users)
    // ==========================================
    Route::middleware('auth')->group(function () {
        // إدارة الحساب الأساسية (Breeze)
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        // الإشعارات
        Route::get('/notifications/fetch', [NotificationController::class, 'fetch'])->name('notifications.fetch');
        Route::get('/notifications/{id}/read', [NotificationController::class, 'read'])->name('notifications.read');

        // ⭐ التعديل الجديد: صفحة الفئات المفضلة الخاصة بالعضو العادي ⭐
        Route::get('/my-favorite-categories', [ProfileController::class, 'favoriteCategories'])->name('profile.favorites');

        // إجراء الـ toggle للفئة (تم إبقاؤه هنا وحذفه من الأسفل لمنع التكرار)
        Route::post('categories/{category}/favorite', [CategoryFavoriteController::class, 'toggle'])
            ->name('categories.favorite');

        // التعليقات والتفاعلات (تم نقلهم هنا داخل مجموعة الـ auth لتنظيف الكود بدلاً من كتابة middleware منفصل لكل سطر)
        Route::post('/articles/comments', [CommentController::class, 'store'])->name('comments.store');
        Route::put('/articles/comments', [CommentController::class, 'update'])->name('comments.update');
        Route::delete('/articles/comments', [CommentController::class, 'delete'])->name('comments.delete');
        Route::post('/articles/reactions/toggle', [ReactionController::class, 'toggleArticleReaction'])->name('articles.reactions.toggle');

        // رابط عرض صفحة المقالات المفضلة للعضو
        Route::get('/my-favorite-articles', [ProfileController::class, 'favoriteArticles'])->name('profile.favorite-articles');
        // رابط إجراء الـ toggle لحفظ المقالة
        Route::post('articles/{article}/favorite', [ArticleFavoriteController::class, 'toggle'])->name('articles.favorite');
    });

    // استدعاء ملف الـ routes الخاص بـ Breeze (الـ login والـ register)
    require __DIR__ . '/auth.php';

    // ==========================================
    // 2. للجميع (زوار ومستخدمين)
    // ==========================================
    Route::get('/', function () {
        return view('welcome');
    })->name('home');

    Route::get('/articles/{id}/{slug?}', [ArticleTranslationController::class, 'show'])
        ->where('id', '[0-9]+')
        ->name('articles.show');

    Route::post('/articles/content_img_info', [ArticleController::class, 'get_content_img_info'])->name('visitor.articles.content_img.info');

    // ==========================================
    // 3. للـ Admin فقط
    // ==========================================
    Route::group(['middleware' => ['auth', 'admin'], 'prefix' => "admin"], function () {
        // لوحة تحكم الأدمن
        Route::view('/dashboard', 'admin.layout.dashboard')->name('dashboard');

        // إدارة المقالات
        Route::get('/articles', [ArticleController::class, 'index'])->name('admin.articles.index');
        Route::get('/articles/create', [ArticleController::class, 'create'])->name('admin.articles.create');
        Route::post('/articles', [ArticleController::class, 'store'])->name('admin.articles.store');
        Route::get('/articles/{article_id}/edit', [ArticleController::class, 'edit'])->name('admin.articles.edit');
        Route::put('/articles/{article_id}', [ArticleController::class, 'update'])->name('admin.articles.update');
        Route::delete('/articles/{article_id}', [ArticleController::class, 'destroy'])->name('admin.articles.destroy');

        // إدارة تراجم المقالات
        Route::get('/articles-trans/create/{article_id}', [ArticleTranslationController::class, 'create'])->name('admin.articles-trans.create');
        Route::post('/articles-trans', [ArticleTranslationController::class, 'store'])->name('admin.articles-trans.store');
        Route::get('/articles-trans/{article_id}/edit', [ArticleTranslationController::class, 'edit'])->name('admin.articles-trans.edit');
        Route::put('/articles-trans/{article_id}', [ArticleTranslationController::class, 'update'])->name('admin.articles-trans.update');
        Route::delete('/articles-trans/{article_id}', [ArticleTranslationController::class, 'destroy'])->name('admin.articles-trans.destroy');

        // رفع وإدارة الصور داخل المحتوى
        Route::post('/articles/upload-article-content-img', [ArticleController::class, 'uploadContentImg'])->name('admin.articles.upload.content');
        Route::post('/articles/get-article-content-imgs', [ArticleController::class, 'getArticleContentImgs'])->name('admin.articles.get.content');
        Route::post('/articles/delete-article-image', [ArticleController::class, 'deleteContentImg'])->name('admin.articles.delete.content_img');

        // إدارة الفئات والتراجم
        Route::get('/categories/create', [CategoryTranslationController::class, 'create'])->name('admin.categories.create');
        Route::get('/categories', [CategoryController::class, 'index'])->name('admin.categories.index');
        Route::post('/categories', [CategoryTranslationController::class, 'store'])->name('admin.categories.store');
        Route::get('/categories/{cat_id}/edit', [CategoryTranslationController::class, 'edit'])->name('admin.categories.edit');
        Route::put('/categories/{cat_id}', [CategoryTranslationController::class, 'update'])->name('admin.categories.update');
        Route::delete('/categories/{cat_id}', [CategoryTranslationController::class, 'destroy'])->name('admin.categories.destroy');

        // إدارة الأوسمة والتراجم
        Route::get('/tags/create', [TagTranslationController::class, 'create'])->name('admin.tags.create');
        Route::get('/tags', [TagController::class, 'index'])->name('admin.tags.index');
        Route::post('/tags', [TagTranslationController::class, 'store'])->name('admin.tags.store');
        Route::get('/tags/{tag}/edit', [TagTranslationController::class, 'edit'])->name('admin.tags.edit');
        Route::put('/tags/{tag}', [TagTranslationController::class, 'update'])->name('admin.tags.update');
        Route::delete('/tags/{tag}', [TagTranslationController::class, 'destroy'])->name('admin.tags.destroy');
    });

    // الصفحات العامة الثابتة
    Route::view('/privacy', 'privacy')->name('privacy');
    Route::view('/terms', 'terms')->name('terms');
    Route::view('/contact', 'contact')->name('contact');
    Route::post('/contact', function () {})->name('contact.submit');
    Route::view('/about', 'about')->name('about');
});
