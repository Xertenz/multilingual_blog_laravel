<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;


use App\Models\Category;

class CategoryController extends Controller
{
    //
    public function index()
    {
        $cats = Category::with('media', 'articles')->withCount('articles')->paginate(10);
        return view('admin.categories.index', compact('cats'));
    }
}
