<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;

class HomeController extends Controller
{
    public function index()
    {
        // 1. جلب المقالة البارزة (مثلاً آخر مقالة مميزة أو أحدث مقالة)
        $featuredArticle = Article::with(['translations', 'category', 'category.translations'])
            ->where('status', 'published')
            ->latest()
            ->first();

        // 2. جلب بقية المقالات المتبقية
        $articles = Article::with(['translations', 'category', 'category.translations', 'user'])
            ->where('status', 'published')
            ->when($featuredArticle, fn($q) => $q->where('id', '!=', $featuredArticle->id))
            ->latest()
            ->paginate(6);

        // 3. جلب الفئات مع عدد المقالات فيها
        $categories = Category::withCount('articles')->get();

        return view('home', compact('featuredArticle', 'articles', 'categories'));
    }
}
