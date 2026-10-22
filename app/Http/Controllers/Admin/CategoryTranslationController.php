<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Article;
use App\Models\ArticleTranslation;
use App\Models\Category;
use App\Models\CategoryTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class CategoryTranslationController extends Controller
{

    public function create()
    {
        return view('admin.categories.create');
    }


    public function store(Request $request)
    {
        // 1. معالجة وتوليد الـ Slug لكل لغة في المصفوفة قبل التمرير للـ Validation
        $translations = $request->input('translations', []);

        foreach ($translations as $locale => $data) {
            if (!empty($data['name'])) {
                $slugSource = !empty($data['slug']) ? $data['slug'] : $data['name'];
                $translations[$locale]['slug'] = (string) Str::of($slugSource)->slug('-');
            }
        }

        // دمج المصفوفة المعدلة التي تحتوي على الـ slugs الموالدة داخل الـ Request
        $request->merge(['translations' => $translations]);

        // جلب اللغات المدعومة ديناميكياً من مكتبة mcamara
        $supportedLocales = implode(',', LaravelLocalization::getSupportedLanguagesKeys());

        // 1. التحقق من البيانات (المصفوفة الجديدة والـ Sign العام)
        $request->validate([
            'cat_label' => 'required|string|unique:categories,label|max:50',
            'cat_featured_img' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120', // حتى 5 ميجا كما بالفورم

            // التحقق من الحقول داخل مصفوفة اللغات
            'translations.*.name' => 'nullable|string|max:100',
            'translations.*.slug' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('category_translations', 'slug')
            ],
            'translations.*.description' => 'nullable|string',
            'translations.*.meta_title' => 'nullable|string|max:150',
            'translations.*.meta_description' => 'nullable|string|max:255',
        ]);

        // 2. إنشاء السجل الأساسي للقسم (تجميل الـ Sign كـ slug آمن)
        $cat = Category::create([
            'label'    => (string) Str::of($request->cat_label)->slug('-'),
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

        // 5. حفظ الترجمات في قاعدة البيانات
        $validLocales = LaravelLocalization::getSupportedLanguagesKeys();

        // 4. الدوران السحري حول اللغات الـ 12 الممررة من الفورم المختصر
        foreach ($request->translations as $localeCode => $data) {

            // قاعدة عامة: إذا كان الاسم فارغاً، نتخطى هذه اللغة تماماً لتوفير الوقت للمشرف
            if (!in_array($localeCode, $validLocales) || empty($data['name'])) {
                continue;
            }


            // تطبيق الفلسفة الذكية للـ Slug: إن كُتب يدوياً نأخذه، وإن تُرِك فارغاً نولده من الاسم
            /*
            $finalSlug = !empty($data['slug'])
                ? (string) Str::of($data['slug'])->slug('-')
                : (string) Str::of($data['name'])->slug('-');
            */

            // إدخال الترجمة الحالية في قاعدة البيانات
            $cat->translations()->create([
                'locale'    => $localeCode,
                //'slug'             => trim($finalSlug),
                'slug'             => trim($data['slug']),
                'name'             => trim($data['name']),
                'description'      => trim($data['description'] ?? ''),
                'meta_title'       => trim($data['meta_title'] ?? $data['name']), // إن ترك الميتا فارغاً نضع الاسم كعنوان
                'meta_description' => trim($data['meta_description'] ?? Str::limit(strip_tags($data['description'] ?? ''), 150)), // ماليء تلقائي للميتا وصف
                'created_at'       => Carbon::now(),
                'updated_at'       => Carbon::now(),
            ]);
        }

        // إبطال كاش الـ Navbar لكل اللغات المتاحة
        foreach (LaravelLocalization::getSupportedLanguagesKeys() as $lang) {
            Cache::forget("navbar_categories_{$lang}");
            Cache::forget("all_categories_page_{$lang}");
        }

        return to_route('admin.categories.index')->with('success', 'Category Created Successfully!');
    }

    public function show(int $category_trans_id, ?string $slug = null)
    {
        // 1. جلب الفئة مع الترجمات
        $category_trans = CategoryTranslation::with(['category.translations'])->find($category_trans_id);

        if (!$category_trans) {
            return to_route('home.index');
        }

        $current_locale = LaravelLocalization::getCurrentLocale();

        if ($category_trans->locale !== $current_locale) {
            $category_trans = $category_trans->category->translations->where('locale', $current_locale)
                ->first();
            if ($category_trans) {
                return redirect()->route(
                    'categories.show',
                    [
                        'id' => $category_trans->id,
                        'slug' => $category_trans->slug
                    ]
                );
            }
            return to_route('home.index')->with('info', __('This category is not available in your language.'));
        }

        if (empty($slug) || $category_trans->slug !== $slug) {
            return redirect()->route(
                'categories.show',
                [
                    'id' => $category_trans->id,
                    'slug' => $category_trans->slug
                ]
            );
        }


        // 2. جلب المقالات المكتملة مع الصور (media) والترجمات والمؤلف
        $page = request()->get('page', 1);
        $cacheKey = "category_page_{$category_trans_id}_{$current_locale}_p{$page}";
        $articles = Cache::remember($cacheKey, 3600, function () use ($category_trans) {
            return  Article::with([
                'translations',
                'media',
                'user' // أو 'user' حسب اسم العلاقة المسجلة في موديل Article
            ])
                ->where('category_id', $category_trans->category->id)
                ->where('status', 'published')
                ->latest()
                ->paginate(12);
        });


        //return view('category', compact('category', 'categoryTrans', 'articles'));
        return view('category', compact('category_trans', 'articles'));
    }

    public function edit(int $id)
    {
        $cat = Category::with('translations')->find($id);
        $translations = $cat->translations->keyBy('locale');
        return view('admin.categories.edit', compact('cat', 'translations'));
    }


    public function update(Request $request, int $id)
    {
        $cat = Category::findOrFail($id);

        // 1. تجميل الـ sign القادم من الفورم ليكون بصيغة slug آمنة قبل الفحص
        if ($request->has('cat_label')) {
            $request->merge([
                'cat_label' => (string) Str::of($request->cat_label)->slug('-')
            ]);
        }

        // 2. التحقق من البيانات العامة للمجموعة والأقسام
        $request->validate([
            'cat_label' => "required|string|max:50|unique:categories,label,{$id}",
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
                $currentTransId = $cat->translations()->where('locale', $localeCode)->value('id');

                $request->validate([
                    "translations.{$localeCode}.slug" => "nullable|string|max:100|unique:category_translations,slug,{$currentTransId}",
                ]);
            }
        }

        // 4. تحديث البيانات الأساسية للقسم
        $cat->update([
            'label'    => $request->cat_label,
            'user_id' => Auth::id()
        ]);

        // 5. معالجة تحديث الصورة (حذف القديمة ورفع الجديدة إن وجدت)
        if ($request->hasFile('cat_featured_img')) {
            if ($cat->hasMedia('featured_imgs')) {
                $cat->clearMediaCollection('featured_imgs');
            }

            $file = $request->file('cat_featured_img');
            $file_ext = strtolower($file->getClientOriginalExtension());
            $fileName = uniqid() . '.' . $file_ext;

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
                    'locale' => $localeCode, // شرط البحث والمطابقة
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

        // إبطال كاش الـ Navbar لكل اللغات المتاحة
        foreach (LaravelLocalization::getSupportedLanguagesKeys() as $lang) {
            Cache::forget("navbar_categories_{$lang}");
            Cache::forget("all_categories_page_{$lang}");
        }

        return to_route('admin.categories.index')->with('success', 'Category Updated Successfully!');
    }

    public function destroy(int $cat_id)
    {
        $cat = Category::findOrFail($cat_id);
        $cat->delete();

        // إبطال كاش الـ Navbar لكل اللغات المتاحة
        foreach (LaravelLocalization::getSupportedLanguagesKeys() as $lang) {
            Cache::forget("navbar_categories_{$lang}");
            Cache::forget("all_categories_page_{$lang}");
        }

        return to_route('admin.categories.index')->with('success', 'Category Deleted Successfully!');
    }
}
