<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleTranslation;
use App\Models\CategoryTranslation;
use Illuminate\Http\Request;

class ArticleTranslationController extends Controller
{
    public function index() {
        $articles_trans = ArticleTranslation::all();//->where('language_code', 'ar');
        return view('admin.articles-trans', compact('articles_trans'));
    }

    public function create(int $article_id ) {
        $cats = CategoryTranslation::all()->where('language_code', 'ar');
        return view('admin.add-article-trans', compact('cats', 'article_id'));
    }


    public function store(Request $request) {
        $validated = $request->validate([
            'article_id' => 'required|numeric|exists:articles,id',
            'language_code' => 'required|in:ar,en',
            'slug' => "required|string|unique:article_translations,slug",
            'title' => "required|string|min:3",
            'description' => "required|string|min:20",
            'meta_title' => "required|string|min:3",
            'meta_description' => "required|string|min:20",
        ]);

        // The language for this Article should NOT be exist
        $language_exists = ArticleTranslation::where('article_id', $request->article_id)->where('language_code', $request->language_code)->first();
        if($language_exists){
            return back()->withErrors(['language_exists' => 'this language is already exist for this article'] )->withInput();
        }

        // Adding a new Article
        ArticleTranslation::create([
            'article_id' => $request->article_id,
            'language_code' => $request->language_code,
            'slug' => $request->slug,
            'title' => $request->title,
            'description' => $request->description,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
        ]);
        
        return to_route('articles.index');

    }

    public function edit(Request $request, int $article_trans_id) {
        $cats = CategoryTranslation::all()->where('language_code', 'ar');
        $article = ArticleTranslation::findOrFail($article_trans_id);
        return view('admin.edit-article-trans', compact('cats', 'article'));
    }

    public function update(Request $request, int $article_trans_id) {
        $validated = $request->validate([
            'article_id' => 'required|numeric|exists:articles,id',
            'language_code' => 'required|in:ar,en',
            'slug' => "required|string|unique:article_translations,slug,$article_trans_id",
            'title' => "required|string|min:3",
            'description' => "required|string|min:20",
            'meta_title' => "required|string|min:3",
            'meta_description' => "required|string|min:20",
        ]);

        // The language for this Article should NOT be exist (except the current article_trans)
        $language_exists = ArticleTranslation::where('article_id', $request->article_id)->where('language_code', $request->language_code)->whereNot('id', $article_trans_id)->first();
        if($language_exists){
            return back()->withErrors(['language_exists' => 'this language is already exist for this article'] )->withInput();
        }

        $article_trans = ArticleTranslation::findOrFail($article_trans_id);
        $article_trans->update([
            'language_code' => $request->language_code,
            'slug' => $request->slug,
            'title' => $request->title,
            'description' => $request->description,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
        ]);

        return to_route('articles.index');
    }

    public function destroy($article_trans_id) {
        $article_trans = ArticleTranslation::findOrFail($article_trans_id);
        $article_trans->delete();

        return to_route('articles.index');
    }
}
