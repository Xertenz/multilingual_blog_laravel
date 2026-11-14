<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArticleTag;
use App\Models\Tag;
use Illuminate\Http\Request;

class ArticleTagController extends Controller
{
    //
    public function index()
    {
        $tags = ArticleTag::paginate(10);
        return view('admin.tags.index', compact('tags'));
    }
}
