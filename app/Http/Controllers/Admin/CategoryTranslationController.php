<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use Carbon\Carbon;

class CategoryTranslationController extends Controller
{

    public function create()
    {
        return view('admin.categories.create');
    }


    public function store(Request $request)
    {
        // 1. التحقق من البيانات (المصفوفة الجديدة والـ Sign العام)
        $request->validate([
            'cat_sign' => 'required|string|unique:categories,sign|max:50',
            'cat_featured_img' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120', // حتى 5 ميجا كما بالفورم

            // التحقق من الحقول داخل مصفوفة اللغات
            'translations.*.name' => 'nullable|string|max:100',
            'translations.*.slug' => 'nullable|string|max:100',
            'translations.*.description' => 'nullable|string',
            'translations.*.meta_title' => 'nullable|string|max:150',
            'translations.*.meta_description' => 'nullable|string|max:255',
        ]);

        // 2. إنشاء السجل الأساسي للقسم (تجميل الـ Sign كـ slug آمن)
        $cat = Category::create([
            'sign'    => (string) Str::of($request->cat_sign)->slug('-'),
            'user_id' => Auth::id()
        ]);

        // 3. رفع ومعالجة الصورة باستخدام حزمة Spatie Media Library
        if ($request->hasFile('cat_featured_img')) {
            $file = $request->file('cat_featured_img');
            $file_ext = strtolower($file->getClientOriginalExtension());
            $fileName = uniqid() . '.' . $file_ext;

            $cat->addMedia($file)
                ->usingFileName($fileName)
                ->withResponsiveImages()
                ->toMediaCollection("featured_imgs");
        }

        // 4. الدوران السحري حول اللغات الـ 12 الممررة من الفورم المختصر
        foreach ($request->translations as $localeCode => $data) {

            // قاعدة عامة: إذا كان الاسم فارغاً، نتخطى هذه اللغة تماماً لتوفير الوقت للمشرف
            if (empty($data['name'])) {
                continue;
            }

            // تطبيق الفلسفة الذكية للـ Slug: إن كُتب يدوياً نأخذه، وإن تُرِك فارغاً نولده من الاسم
            $finalSlug = !empty($data['slug'])
                ? (string) Str::of($data['slug'])->slug('-')
                : (string) Str::of($data['name'])->slug('-');

            // إدخال الترجمة الحالية في قاعدة البيانات
            $cat->translations()->create([
                'language_code'    => $localeCode,
                'slug'             => trim($finalSlug),
                'name'             => trim($data['name']),
                'description'      => trim($data['description'] ?? ''),
                'meta_title'       => trim($data['meta_title'] ?? $data['name']), // إن ترك الميتا فارغاً نضع الاسم كعنوان
                'meta_description' => trim($data['meta_description'] ?? Str::limit(strip_tags($data['description'] ?? ''), 150)), // ماليء تلقائي للميتا وصف
                'created_at'       => Carbon::now(),
                'updated_at'       => Carbon::now(),
            ]);
        }

        return to_route('admin.categories.index')->with('success', 'تم إنشاء القسم والترجمات بنجاح!');
    }

    public function show(int $id)
    {
        // 1. جلب الفئة مع الترجمات
        $category = Category::with(['translations'])->findOrFail($id);

        // الحصول على الترجمة باللغة الحالية أو أول ترجمة متاح كخيار احتياطي (Fallback)
        $categoryTrans = $category->translations->where('language_code', app()->getLocale())->first()
            ?? $category->translations->first();

        // 2. جلب المقالات المكتملة مع الصور (media) والترجمات والمؤلف
        $articles = Article::with([
            'translations',
            'media',
            'user' // أو 'user' حسب اسم العلاقة المسجلة في موديل Article
        ])
            ->where('category_id', $category->id)
            ->where('status', 'published')
            ->latest()
            ->paginate(12);

        return view('category', compact('category', 'categoryTrans', 'articles'));
    }

    public function edit($id)
    {
        $cat = Category::with('translations')->find($id);
        $translations = $cat->translations->keyBy('language_code');
        return view('admin.categories.edit', compact('cat', 'translations'));
    }


    public function update(Request $request, int $id)
    {
        $cat = Category::findOrFail($id);

        // 1. تجميل الـ sign القادم من الفورم ليكون بصيغة slug آمنة قبل الفحص
        if ($request->has('cat_sign')) {
            $request->merge([
                'cat_sign' => (string) Str::of($request->cat_sign)->slug('-')
            ]);
        }

        // 2. التحقق من البيانات العامة للمجموعة والأقسام
        $request->validate([
            'cat_sign' => "required|string|max:50|unique:categories,sign,{$id}",
            'cat_featured_img' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',

            // التحقق من الحقول داخل مصفوفة اللغات الفرعية
            'translations.*.name' => 'nullable|string|max:100',
            'translations.*.description' => 'nullable|string',
            'translations.*.meta_title' => 'nullable|string|max:150',
            'translations.*.meta_description' => 'nullable|string|max:255',
        ]);

        // 3. التحقق الديناميكي من الـ unique لـ slug كل لغة على حدة باستثناء السجل الحالي للغة نفسها
        foreach ($request->translations as $localeCode => $data) {
            if (!empty($data['slug'])) {
                $currentTransId = $cat->translations()->where('language_code', $localeCode)->value('id');

                $request->validate([
                    "translations.{$localeCode}.slug" => "nullable|string|max:100|unique:category_translations,slug,{$currentTransId}",
                ]);
            }
        }

        // 4. تحديث البيانات الأساسية للقسم
        $cat->update([
            'sign'    => $request->cat_sign,
            'user_id' => Auth::id()
        ]);

        // 5. معالجة تحديث الصورة (حذف القديمة ورفع الجديدة إن وجدت)
        if ($request->hasFile('cat_featured_img')) {
            if ($cat->hasMedia('featured_imgs')) {
                $cat->clearMediaCollection('featured_imgs');
            }

            $file = $request->file('cat_featured_img');
            $file_ext = strtolower($file->getClientOriginalExtension());
            $fileName = Str::random(8) . '_' . time() . '.' . $file_ext;

            $cat->addMedia($file)
                ->usingFileName($fileName)
                ->toMediaCollection("featured_imgs");
        }

        // 6. الدوران حول اللغات الـ 12 للتحديث أو الإنشاء التلقائي
        foreach ($request->translations as $localeCode => $data) {

            // إذا كان حقل الاسم فارغاً، نتخطى تحديث أو إضافة هذه اللغة
            if (empty($data['name'])) {
                continue;
            }

            // تطبيق الفلسفة الذكية للـ Slug: إن كُتب يدوياً نأخذه، وإن تُرِك فارغاً نولده من الاسم
            $finalSlug = !empty($data['slug'])
                ? (string) Str::of($data['slug'])->slug('-')
                : (string) Str::of($data['name'])->slug('-');

            // استخدام updateOrCreate الذكية للتعامل مع الإضافة والتعديل تلقائياً في سطر واحد
            $cat->translations()->updateOrCreate(
                [
                    'language_code' => $localeCode, // شرط البحث والمطابقة
                ],
                [
                    'slug'             => trim($finalSlug),
                    'name'             => trim($data['name']),
                    'description'      => trim($data['description'] ?? ''),
                    'meta_title'       => trim($data['meta_title'] ?? $data['name']),
                    'meta_description' => trim($data['meta_description'] ?? Str::limit(strip_tags($data['description'] ?? ''), 150)),
                    'updated_at'       => Carbon::now()
                ]
            );
        }

        return to_route('admin.categories.index')->with('success', 'Category updated successfully!');
    }

    public function destroy(int $cat_id)
    {
        $cat = Category::findOrFail($cat_id);
        $cat->delete();
        return to_route('admin.categories.index');
    }
}
