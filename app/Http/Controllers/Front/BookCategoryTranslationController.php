<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\BookCategoryTranslation;
use App\Models\BookTranslation;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class BookCategoryTranslationController extends Controller
{
    public function show(int $id, ?string $slug = null)
    {
        $category_trans = BookCategoryTranslation::with([
            'category' => function ($query) {
                $query->where('is_active', true);
            },
            'category.translations',
        ])->find($id);

        if (!$category_trans || !$category_trans->category) {
            return to_route('books.index');
        }

        $currentLocale = LaravelLocalization::getCurrentLocale();

        // التحويل إلى ترجمة الفئة نفسها باللغة الحالية إن وُجدت
        if ($category_trans->locale !== $currentLocale) {
            $targetTrans = $category_trans->category->translations
                ? $category_trans->category->translations->firstWhere('locale', $currentLocale)
                : null;

            if ($targetTrans) {
                return redirect()->route('book-categories.show', [
                    'id'   => $targetTrans->id,
                    'slug' => $targetTrans->slug,
                ]);
            }
        }

        // تصحيح الـ slug في الرابط لدعم SEO
        if (!empty($category_trans->slug) && $slug !== $category_trans->slug) {
            return redirect()->route('book-categories.show', [
                'id'   => $category_trans->id,
                'slug' => $category_trans->slug,
            ], 301);
        }

        $categoryId = $category_trans->book_category_id ?? $category_trans->category->id;

        $translations = BookTranslation::where('locale', $currentLocale)
            ->whereHas('book', function ($query) use ($categoryId) {
                $query->where('book_category_id', $categoryId);
            })
            ->with([
                'book.category.translations',
                'book.tags.translations',
                'book.media',
                'media',
                'author',
            ])
            ->latest()
            ->paginate(12);

        return view('books.category', compact('category_trans', 'translations'));
    }
}
