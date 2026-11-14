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
        $cats = ArticleCategory::with('media', 'articles')->withCount('articles')->paginate(10);
        return view('admin.categories.index', compact('cats'));
    }
}
