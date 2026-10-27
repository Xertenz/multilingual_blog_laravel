<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * عرض قائمة الكتب وملخصاتها للزوار
     */
    public function index()
    {
        // جلب الكتب مع الترجمات والصور مرتبة من الأحدث
        $books = Book::with(['translations', 'media'])->latest()->paginate(9);

        return view('front.books.index', compact('books'));
    }

    /**
     * عرض تفاصيل كتاب معين مع ملخصه ورابط الشراء
     */
    public function show($id, $slug = null)
    {
        $book = Book::with(['translations', 'media'])->findOrFail($id);

        // جلب ترجمة الكتاب بناءً على اللغة الحالية للموقع
        $translation = $book->translate(app()->getLocale());

        return view('front.books.show', compact('book', 'translation'));
    }
}
