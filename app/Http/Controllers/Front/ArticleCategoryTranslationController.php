<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleCategoryTranslation;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class ArticleCategoryTranslationController extends Controller
{
    public function show(int $id, ?string $slug = null)
    {
        $category_trans = ArticleCategoryTranslation::with(['category.translations'])->find($id);

        if (!$category_trans || !$category_trans->category) {
            return to_route('home.index');
        }

        $currentLocale = LaravelLocalization::getCurrentLocale();

        // التحويل إلى ترجمة الفئة نفسها باللغة الحالية إن وُجدت
        if ($category_trans->locale !== $currentLocale) {
            $targetTrans = $category_trans->category->translations
                ->firstWhere('locale', $currentLocale);

            if ($targetTrans) {
                return redirect()->route('article-categories.show', [
                    'id'   => $targetTrans->id,
                    'slug' => $targetTrans->slug,
                ]);
            }

            return to_route('home.index')->with('info', __('This category is not available in your language.'));
        }

        // تصحيح الـ slug في الرابط لدعم SEO
        if (empty($slug) || $category_trans->slug !== $slug) {
            return redirect()->route('article-categories.show', [
                'id'   => $category_trans->id,
                'slug' => $category_trans->slug,
            ], 301);
        }

        $articles = Article::with([
            'translations' => function ($query) use ($currentLocale) {
                $query->where('locale', $currentLocale)->where('status', 'published');
            },
            'category.translations' => function ($query) use ($currentLocale) {
                $query->where('locale', $currentLocale);
            },
            'media',
            'author',
        ])
            ->where('article_category_id', $category_trans->category->id)
            ->where('status', 'published')
            ->whereHas('translations', function ($query) use ($currentLocale) {
                $query->where('locale', $currentLocale)->where('status', 'published');
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('articles.category', compact('category_trans', 'articles'));
    }
}
