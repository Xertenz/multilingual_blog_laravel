<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleTranslation;
use App\Models\CategoryTranslation;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    //
    public function index() {
        $articles = Article::with('translations')->get();

        //$test = Article::with('translations')->get();
        return view('admin.articles', compact('articles'));
    }

    public function create() {
        $cats = CategoryTranslation::all()->where('language_code', 'ar');
        return view('admin.add-article', compact('cats'));
    }


    public function store(Request $request) {
        $validated = $request->validate([
            'category_id' => 'required|numeric|exists:categories,id',
            'featured_img' => "required|image",
        ]);

        $article = Article::create([
            'category_id' => (int)$request->category_id,
            'featured_img' => '',
            'views' => 0,
            'status' => 'published',
        ]);

        return to_route('articles.index');
    }

    public function edit(int $article_id) {
        $article = Article::findOrFail($article_id);
        $cats = CategoryTranslation::all()->where('language_code', 'ar');
        return view('admin.edit-article', compact('article', 'cats'));
    }

    public function update(Request $request, int $article_id) {
        $validated = $request->validate([
            'category_id' => 'required|numeric|exists:categories,id',
            'featured_img' => "required|image",
        ]);
        $article = Article::findOrFail($article_id);
        $article->update([
            'category_id' => (int)($request->category_id),
            'featured_img' => '',
            //'views' => 0,
            //'status' => 'published',
        ]);

        return to_route('articles.index');
    }


    public function destroy($article_id) {
        $article = Article::findOrFail($article_id);
        $article->delete();

        return to_route('articles.index');
    }
}
