<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArticleCategory;
use App\Models\Category;

class ArticleCategoryController extends Controller
{
    //
    public function index()
    {
        $cats = ArticleCategory::with('media')
            ->withCount('articles')
            ->latest()
            ->paginate(10);
        return view('admin.article_categories.index', compact('cats'));
    }
}
