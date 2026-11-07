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
        $currentLocale = app()->getLocale();
        // جلب الكتب مع الترجمات والصور مرتبة من الأحدث
        $books = Book::whereHas('translations', function ($query) use ($currentLocale) {
            $query->where('locale', $currentLocale);
        })->with(['translations' => function ($query) use ($currentLocale) {
            $query->where('locale', $currentLocale);
        }, 'media'])->latest()->paginate(10);

        return view('front.books.index', compact('books'));
    }

    /**
     * عرض تفاصيل كتاب معين مع ملخصه ورابط الشراء
     */
    public function show(int $id, $slug = null)
    {

        $currentLocale = app()->getLocale();

        $book = Book::with(['media'])->findOrFail($id);

        // جلب الترجمة الخاصة باللغة الحالية فقط
        $translation = $book->translations()->where('locale', $currentLocale)->first();

        if (!$translation) {
            //abort(404); // إذا لم تكن الترجمة بهذه اللغة موجودة، اعرض صفحة غير موجود
            return response()->redirectToRoute('books.index');
        }

        return view('front.books.show', compact('book', 'translation'));
    }
}
