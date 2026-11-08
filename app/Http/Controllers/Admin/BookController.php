<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookCategory;
use App\Models\BookTag;
use App\Models\BookTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $books = Book::with(['translations', 'media', 'bookCategory.translations', 'bookTags.translations'])->latest()->paginate(10);
        return view('admin.books.index', compact('books'));
    }

    /**
     * عرض صفحة إنشاء كتاب جديد
     */
    public function create()
    {
        $locales = LaravelLocalization::getSupportedLocales();
        $bookCategories = BookCategory::with('translations')->get();
        $bookTags = BookTag::with('translations')->get();
        return view('admin.books.create', compact('locales', 'bookCategories', 'bookTags'));
    }

    /**
     * حفظ الكتاب الجديد في قاعدة البيانات
     */
    public function store(Request $request)
    {
        $validLocales = LaravelLocalization::getSupportedLanguagesKeys();

        // 1. قواعد التحقق العامة والفرعية (جعل حقول الترجمة اختيارية nullable)
        $rules = [
            'internal_title'   => 'required|string|max:255|unique:books,internal_title',
            'book_category_id' => 'required|exists:book_categories,id',
            'tags'             => 'nullable|array',
            'tags.*'           => 'exists:book_tags,id',
            'published_year'   => 'nullable|integer|min:1500|max:' . (date('Y') + 1),
            'pages_count'      => 'nullable|integer|min:1',
            'cover_image'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

            'translations.*.title'         => 'nullable|string|max:255',
            'translations.*.author'        => 'nullable|string|max:255',
            'translations.*.summary'       => 'nullable|string',
            'translations.*.affiliate_url' => 'nullable|url',
            'translations.*.price'         => 'nullable|numeric|min:0',
            'translations.*.currency'      => 'nullable|string|max:10',
            'translations.*.slug'          => 'nullable|string|max:255|unique:book_translations,slug',
        ];

        $request->validate($rules);

        // 2. إنشاء السجل العام للكتاب
        $book = Book::create([
            'user_id' => Auth::id(),
            'book_category_id' => (int) $request->book_category_id,
            'internal_title'   => $request->internal_title,
            'published_year'   => $request->published_year,
            'pages_count'      => (int) $request->pages_count,
        ]);

        // ربط الوسوم (Many-to-Many)
        if ($request->has('tags')) {
            $book->bookTags()->sync($request->tags);
        }

        // 3. رفع غلاف الكتاب باستخدام Spatie Media Library
        if ($request->hasFile('cover_image')) {
            $book->addMediaFromRequest('cover_image')
                ->toMediaCollection('cover_imgs');
        }

        // 4. حفظ الترجمات للغات التي أدخلها المشرف فقط
        if ($request->has('translations')) {
            foreach ($request->translations as $localeCode => $transData) {
                if (!in_array($localeCode, $validLocales) || empty($transData['title'])) {
                    continue;
                }

                $slugSource = !empty($transData['slug']) ? $transData['slug'] : $transData['title'];
                $slug = (string) Str::of($slugSource)->slug('-');

                $translation = $book->translations()->create([
                    'user_id'       => Auth::id(),
                    'locale'        => $localeCode,
                    'title'         => trim($transData['title']),
                    'author'        => trim($transData['author'] ?? ''),
                    'slug'          => trim($slug),
                    'summary'       => $transData['summary'] ?? null,
                    'affiliate_url' => $transData['affiliate_url'] ?? null,
                    'price'         => $transData['price'] ?? null,
                    'currency'      => $transData['currency'] ?? 'USD',
                ]);

                // رفع غلاف خاص لهذه الترجمة
                if ($request->hasFile("translations.{$localeCode}.cover_image")) {
                    $translation->clearMediaCollection('cover_imgs');
                    $translation->addMediaFromRequest("translations.{$localeCode}.cover_image")
                        ->toMediaCollection('cover_imgs');
                }
            }
        }

        return redirect()->route('admin.books.index')
            ->with('success', __('Book created successfully.'));
    }

    /**
     * عرض صفحة تعديل الكتاب
     */
    public function edit(Book $book)
    {
        $book->load(['translations', 'media', 'bookTags']);
        $locales = LaravelLocalization::getSupportedLocales();
        $bookCategories = BookCategory::with('translations')->get();
        $bookTags = BookTag::with('translations')->get();
        // تجهيز الترجمات كمفتاح باللغة لتسهيل عرضها في الفورم (مثل الأقسام)
        $translations = $book->translations->keyBy('locale');

        return view('admin.books.edit', compact('book', 'locales', 'translations', 'bookCategories', 'bookTags'));
    }

    /**
     * تحديث بيانات الكتاب والترجمات
     */
    public function update(Request $request, Book $book)
    {
        $validLocales = LaravelLocalization::getSupportedLanguagesKeys();

        $rules = [
            'internal_title'   => ['required', 'string', 'max:255', Rule::unique('books', 'internal_title')->ignore($book->id)],
            'book_category_id' => 'required|exists:book_categories,id',
            'tags'             => 'nullable|array',
            'tags.*'           => 'exists:book_tags,id',
            'published_year'   => 'nullable|integer|min:1500|max:' . (date('Y') + 1),
            'pages_count'      => 'nullable|integer|min:1',
            'cover_image'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

            'translations.*.title'         => 'nullable|string|max:255',
            'translations.*.author'        => 'nullable|string|max:255',
            'translations.*.summary'       => 'nullable|string',
            'translations.*.affiliate_url' => 'nullable|url',
            'translations.*.price'         => 'nullable|numeric|min:0',
            'translations.*.currency'      => 'nullable|string|max:10',
        ];

        $request->validate($rules);

        // التحقق الديناميكي من عدم تكرار الـ slug لكل لغة أثناء التعديل
        if ($request->has('translations')) {
            foreach ($request->translations as $localeCode => $transData) {
                if (!empty($transData['slug'])) {
                    $currentTransId = $book->translations()->where('locale', $localeCode)->value('id');
                    $request->validate([
                        "translations.{$localeCode}.slug" => "nullable|string|max:255|unique:book_translations,slug,{$currentTransId}",
                    ]);
                }
            }
        }

        // 1. تحديث البيانات العامة
        $book->update([
            'book_category_id' => (int) $request->book_category_id,
            'internal_title'   => $request->internal_title,
            'published_year'   => $request->published_year,
            'pages_count'      => (int) $request->pages_count,
        ]);
        // تحديث الوسوم
        $book->bookTags()->sync($request->tags ?? []);

        // 2. تحديث صورة الغلاف إن وُجدت
        if ($request->hasFile('cover_image')) {
            $book->clearMediaCollection('cover_imgs'); // مسح الغلاف القديم
            $book->addMediaFromRequest('cover_image')
                ->toMediaCollection('cover_imgs');
        }

        // 3. تحديث أو إنشاء أو حذف الترجمات بناءً على إدخال المشرف
        if ($request->has('translations')) {
            foreach ($request->translations as $localeCode => $transData) {
                if (!in_array($localeCode, $validLocales)) {
                    continue;
                }

                // إذا قام المشرف بإفريغ حقل العنوان (Title)، نقوم بحذف الترجمة
                if (empty($transData['title'])) {
                    $book->translations()->where('locale', $localeCode)->delete();
                    continue;
                }

                $slugSource = !empty($transData['slug']) ? $transData['slug'] : $transData['title'];
                $slug = (string) Str::of($slugSource)->slug('-');

                // استخدام updateOrCreate وتخزين الكائن الناتج في متغير ($translation)
                $translation = $book->translations()->updateOrCreate(
                    ['locale' => $localeCode],
                    [
                        'title'         => trim($transData['title']),
                        'author'        => trim($transData['author'] ?? ''),
                        'slug'          => trim($slug),
                        'summary'       => $transData['summary'] ?? null,
                        'affiliate_url' => $transData['affiliate_url'] ?? null,
                        'price'         => $transData['price'] ?? null,
                        'currency'      => $transData['currency'] ?? 'USD',
                    ]
                );

                // 🖼️ معالجة رفع أو تحديث صورة الغلاف الخاصة بهذه الترجمة أثناء التعديل
                if ($request->hasFile("translations.{$localeCode}.cover_image")) {
                    $translation->clearMediaCollection('cover_imgs'); // مسح الغلاف القديم للترجمة
                    $translation->addMediaFromRequest("translations.{$localeCode}.cover_image")
                        ->toMediaCollection('cover_imgs');
                }
            }
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
