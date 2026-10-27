<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class BookController extends Controller
{
    /**
     * عرض قائمة الكتب في لوحة التحكم
     */
    public function index()
    {
        $books = Book::with(['translations', 'media'])->latest()->paginate(10);
        return view('admin.books.index', compact('books'));
    }

    /**
     * عرض صفحة إنشاء كتاب جديد
     */
    public function create()
    {
        $locales = LaravelLocalization::getSupportedLocales();
        return view('admin.books.create', compact('locales'));
    }

    /**
     * حفظ الكتاب الجديد في قاعدة البيانات
     */
    public function store(Request $request)
    {
        $locales = LaravelLocalization::getSupportedLocales();

        // 1. التجميع والتأكد من قواعد التحقق (Validation) لكل لغة
        $rules = [
            'internal_title' => 'required|string|max:255|unique:books,internal_title,',
            'published_year' => 'nullable|integer|min:1500|max:' . (date('Y') + 1),
            'pages_count'    => 'nullable|integer|min:1',
            'cover_image'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];

        foreach ($locales as $localeCode => $properties) {
            $rules["translations.{$localeCode}.title"]         = 'required|string|max:255';
            $rules["translations.{$localeCode}.author"]        = 'required|string|max:255';
            $rules["translations.{$localeCode}.summary"]       = 'nullable|string';
            $rules["translations.{$localeCode}.affiliate_url"] = 'nullable|url';
            $rules["translations.{$localeCode}.price"]         = 'nullable|numeric|min:0';
            $rules["translations.{$localeCode}.currency"]      = 'nullable|string|max:10';

            // تحقق من عدم تكرار الـ Slug لكل لغة
            $rules["translations.{$localeCode}.slug"] = [
                'nullable',
                'string',
                'max:255',
                Rule::unique('book_translations', 'slug')->where('locale', $localeCode),
            ];
        }

        $request->validate($rules);

        // 2. إنشاء السجل العام للكتاب
        $book = Book::create([
            'internal_title' => $request->internal_title,
            'published_year' => $request->published_year,
            'pages_count'    => $request->pages_count,
        ]);

        // 3. رفع غلاف الكتاب باستخدام Spatie Media Library
        if ($request->hasFile('cover_image')) {
            $book->addMediaFromRequest('cover_image')
                ->toMediaCollection('cover_imgs');
        }

        // 4. حفظ الترجمات لكل لغة
        foreach ($locales as $localeCode => $properties) {
            $transData = $request->input("translations.{$localeCode}");

            // توليد الـ Slug تلقائياً من العنوان إذا لم يُدخل يدويًا
            $slug = !empty($transData['slug'])
                ? Str::slug($transData['slug'])
                : Str::slug($transData['title']);

            // حماية إضافية للغتين العربية/غير اللاتينية إذا أراد النظام دعمها في الـ Slug
            if (empty($slug)) {
                $slug = Str::slug($transData['title'], '-', null);
            }

            $book->translations()->create([
                'locale'        => $localeCode,
                'title'         => $transData['title'],
                'author'        => $transData['author'],
                'slug'          => $slug,
                'summary'       => $transData['summary'] ?? null,
                'affiliate_url' => $transData['affiliate_url'] ?? null,
                'price'         => $transData['price'] ?? null,
                'currency'      => $transData['currency'] ?? 'USD',
            ]);
        }

        return redirect()->route('admin.books.index')
            ->with('success', __('Book created successfully with all translations.'));
    }

    /**
     * عرض صفحة تعديل الكتاب
     */
    public function edit(Book $book)
    {
        $book->load(['translations', 'media']);
        $locales = LaravelLocalization::getSupportedLocales();

        return view('admin.books.edit', compact('book', 'locales'));
    }

    /**
     * تحديث بيانات الكتاب والترجمات
     */
    public function update(Request $request, Book $book)
    {
        $locales = LaravelLocalization::getSupportedLocales();

        $rules = [
            'internal_title' => ['required', 'string', 'max:255', Rule::unique('books', 'internal_title')->ignore($book->id)],
            'published_year' => 'nullable|integer|min:1500|max:' . (date('Y') + 1),
            'pages_count'    => 'nullable|integer|min:1',
            'cover_image'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];

        foreach ($locales as $localeCode => $properties) {
            $existingTranslation = $book->translations->where('locale', $localeCode)->first();
            $transId = $existingTranslation ? $existingTranslation->id : null;

            $rules["translations.{$localeCode}.title"]         = 'required|string|max:255';
            $rules["translations.{$localeCode}.author"]        = 'required|string|max:255';
            $rules["translations.{$localeCode}.summary"]       = 'nullable|string';
            $rules["translations.{$localeCode}.affiliate_url"] = 'nullable|url';
            $rules["translations.{$localeCode}.price"]         = 'nullable|numeric|min:0';
            $rules["translations.{$localeCode}.currency"]      = 'nullable|string|max:10';

            $rules["translations.{$localeCode}.slug"] = [
                'nullable',
                'string',
                'max:255',
                Rule::unique('book_translations', 'slug')
                    ->where('locale', $localeCode)
                    ->ignore($transId),
            ];
        }

        $request->validate($rules);

        // 1. تحديث البيانات العامة
        $book->update([
            'internal_title' => $request->internal_title,
            'published_year' => $request->published_year,
            'pages_count'    => $request->pages_count,
        ]);

        // 2. تحديث صورة الغلاف إن وُجدت
        if ($request->hasFile('cover_image')) {
            $book->clearMediaCollection('cover_imgs'); // مسح الغلاف القديم
            $book->addMediaFromRequest('cover_image')
                ->toMediaCollection('cover_imgs');
        }

        // 3. تحديث أو إنشاء الترجمات (updateOrCreate)
        foreach ($locales as $localeCode => $properties) {
            $transData = $request->input("translations.{$localeCode}");

            $slug = !empty($transData['slug'])
                ? Str::slug($transData['slug'])
                : Str::slug($transData['title']);

            $book->translations()->updateOrCreate(
                ['locale' => $localeCode],
                [
                    'title'         => $transData['title'],
                    'author'        => $transData['author'],
                    'slug'          => $slug,
                    'summary'       => $transData['summary'] ?? null,
                    'affiliate_url' => $transData['affiliate_url'] ?? null,
                    'price'         => $transData['price'] ?? null,
                    'currency'      => $transData['currency'] ?? 'USD',
                ]
            );
        }

        return redirect()->route('admin.books.index')
            ->with('success', __('Book updated successfully.'));
    }

    /**
     * حذف الكتاب مع صوره وترجماته
     */
    public function destroy(Book $book)
    {
        $book->clearMediaCollection('cover_imgs');
        $book->delete(); // سيحذف الترجمات تلقائياً بسبب cascadeOnDelete

        return redirect()->route('admin.books.index')
            ->with('success', __('Book deleted successfully.'));
    }
}
