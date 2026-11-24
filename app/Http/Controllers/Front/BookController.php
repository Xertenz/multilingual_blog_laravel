<?php

namespace App\Http\Controllers\Front;

use App\Enums\ContentStatus;
use App\Http\Controllers\Controller;
use App\Models\BookTranslation;

class BookController extends Controller
{
    /**
     * عرض قائمة الكتب وملخصاتها المنشورة للزوار
     */
    public function index()
    {
        $currentLocale = app()->getLocale();

        $translations = BookTranslation::where('locale', $currentLocale)
            ->where('status', ContentStatus::PUBLISHED->value)
            ->whereHas('book', function ($query) {
                $query->where('status', ContentStatus::PUBLISHED->value);
            })
            ->with([
                'book.category.translations',
                'book.tags.translations',
                'book.media',
                'media',
            ])
            ->latest()
            ->paginate(10);

        return view('books.index', compact('translations'));
    }

    /**
     * عرض تفاصيل كتاب منشور مع ملخصه ورابط الشراء
     */
    public function show(int $id, $slug = null)
    {
        $currentLocale = app()->getLocale();

        $trans = BookTranslation::with([
            'book.category.translations',
            'book.tags.translations',
            'book.media',
            'book.translations',
        ])->findOrFail($id);

        $book = $trans->book;

        if (
            !$book
            || $book->status !== ContentStatus::PUBLISHED
            || $trans->status !== ContentStatus::PUBLISHED
        ) {
            return redirect()->route('books.index')
                ->with('info', __('This book is not available at the moment.'));
        }

        $translation = $book->translations
            ->where('locale', $currentLocale)
            ->where('status', ContentStatus::PUBLISHED)
            ->first();

        if (!$translation) {
            return redirect()->route('books.index')
                ->with('info', __('This book is not available in your language.'));
        }

        if (!empty($translation->slug) && $slug !== $translation->slug) {
            return redirect()->route('books.show', [
                'id'   => $translation->id,
                'slug' => $translation->slug,
            ], 301);
        }

        $translation->increment('views');
        $book->increment('views');

        return view('books.show', compact('book', 'translation'));
    }
}
