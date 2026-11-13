<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookTranslation;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * عرض قائمة الكتب وملخصاتها للزوار
     */
    public function index()
    {
        $currentLocale = app()->getLocale();

        // جلب الترجمات الخاصة باللغة الحالية مباشرة مع كافة العلاقات المرتبطة لتجنب N+1
        $translations = BookTranslation::where('locale', $currentLocale)
            ->with([
                'book.bookCategory.translations',
                'book.bookTags.translations',
                'book.media',
                'media' // لضمان جلب غلاف الترجمة إن وجد
            ])
            ->latest()
            ->paginate(10);

        return view('front.books.index', compact('translations'));
    }

    /**
     * عرض تفاصيل كتاب معين مع ملخصه ورابط الشراء
     */
    public function show(int $id, $slug = null)
    {
        $currentLocale = app()->getLocale();

        // جلب الكتاب مع الفئات، الوسوم، والوسائط
        //$book = Book::with(['bookCategory.translations', 'bookTags.translations', 'media'])->findOrFail($id);
        $trans = BookTranslation::with(['book.bookCategory.translations', 'book.bookTags.translations', 'book.media'])->findOrFail($id);
        $book = $trans->book;

        // جلب الترجمة الخاصة باللغة الحالية فقط
        $translation = $book->translations()->where('locale', $currentLocale)->first();

        if (!$translation) {
            return response()->redirectToRoute('books.index');
        }

        // 📈 زيادة عدد المشاهدات لكل من الترجمة والكتاب الأساسي معاً
        $translation->increment('views');
        $book->increment('views');

        return view('front.books.show', compact('book', 'translation'));
    }
}
