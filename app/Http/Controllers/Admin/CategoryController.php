<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;


use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    //
    public function index() {
        $cats = Category::paginate(10);
        return view('admin.categories.index', compact('cats'));
    }
}
