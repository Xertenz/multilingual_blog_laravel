<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

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

    /*
    public function store(Request $request)
    {
        $validated = $request->validate([
            "cat_sign" => 'required|string|unique:categories,sign',
            'cat_featured_img' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',

            'english_slug' => [
                'required',
                'string',
                //'unique:category_translations,slug',
                Rule::unique('category_translations', 'slug')->where(function ($query) use ($request) {
                    return $query->where('language_code', 'en');
                })
            ],
            'english_name' => 'required|string',
            'english_description' => 'required|string',
            'english_meta_title' => 'required|string',
            'english_meta_description' => 'required|string',
        ]);

        $english_slug = $request->english_slug;
        $english_name = $request->english_name;
        $english_description = $request->english_description;
        $english_meta_title = $request->english_meta_title;
        $english_meta_description = $request->english_meta_description;



        //inserting main article record (without translation)
        $cat = Category::create([
            'sign' => trim($request->cat_sign),
            'user_id' => Auth::id()
        ]);


        $file = $request->file('cat_featured_img');
        $file_ext = strtolower($file->getClientOriginalExtension());
        $fileName = uniqid() . '.' . $file_ext;
        //$fileName = Str::random(8) . '_' . time() . '.' . $file_ext;


        $cat
            ->addMedia($file) // $article->addMediaFromRequest('content_img)
            //->usingFileName(Str::random(32).'.'.$file_ext)
            ->usingFileName($fileName)
            ->withResponsiveImages()
            ->toMediaCollection("featured_imgs");


        //inserting English article translation
        $cat->translations()->create([
            'language_code' => 'en',
            'slug' => trim($english_slug),
            'name' => trim($english_name),
            'description' => trim($english_description),
            'meta_title' => trim($english_meta_title),
            'meta_description' => trim($english_meta_description),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        //inserting other optional category translations
        $this->store_cat_trans($cat, $request, 'arabic', 'ar');
        $this->store_cat_trans($cat, $request, 'german', 'de');
        $this->store_cat_trans($cat, $request, 'spanish', 'es');
        $this->store_cat_trans($cat, $request, 'french', 'fr');
        $this->store_cat_trans($cat, $request, 'russian', 'ru');
        $this->store_cat_trans($cat, $request, 'portuguese', 'pt');
        $this->store_cat_trans($cat, $request, 'dutch', 'nl');
        $this->store_cat_trans($cat, $request, 'italian', 'it');
        $this->store_cat_trans($cat, $request, 'swedish', 'sv');
        $this->store_cat_trans($cat, $request, 'danish', 'da');
        $this->store_cat_trans($cat, $request, 'norwegian', 'nb');

        return to_route('admin.categories.index');
    }

    public function store_cat_trans(Category $cat, Request $request, string $lang, string $lang_code)
    {

        $lang_slug = $request->input("{$lang}_slug");
        $lang_name = $request->input("{$lang}_name");
        $lang_description = $request->input("{$lang}_description");
        $lang_meta_title = $request->input("{$lang}_meta_title");
        $lang_meta_description = $request->input("{$lang}_meta_description");
        if (
            $lang_slug != null &&
            $lang_name != null &&
            $lang_description != null &&
            $lang_meta_title != null &&
            $lang_meta_description != null
        ) {

            $cat->translations()->create([
                'language_code' => $lang_code,
                'slug' => trim($lang_slug),
                'name' => trim($lang_name),
                'description' => trim($lang_description),
                'meta_title' => trim($lang_meta_title),
                'meta_description' => trim($lang_meta_description),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
    */

    public function edit($id)
    {
        $cat = Category::with('translations')->find($id);
        $translations = $cat->translations->keyBy('language_code');
        return view('admin.categories.edit', compact('cat', 'translations'));
    }

    /*
    public function update(Request $request, int $id)
    {
        $cat = Category::find($id);

        $validated = $request->validate([
            'cat_sign' => "required|string|unique:categories,sign,$id",
            'cat_featured_img' => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',

            'english_slug' => "required|string|unique:category_translations,slug,{$cat->translations()->where('language_code', 'en')->value('id')}",
            'english_name' => 'required|string',
            'english_description' => 'required|string',
            'english_meta_title' => 'required|string',
            'english_meta_description' => 'required|string',

            'arabic_slug' => "unique:category_translations,slug,{$cat->translations()->where('language_code', 'ar')->value('id')}",
            'german_slug' => "unique:category_translations,slug,{$cat->translations()->where('language_code', 'de')->value('id')}",
            'spanish_slug' => "unique:category_translations,slug,{$cat->translations()->where('language_code', 'es')->value('id')}",
            'french_slug' => "unique:category_translations,slug,{$cat->translations()->where('language_code', 'fr')->value('id')}",
            'russian_slug' => "unique:category_translations,slug,{$cat->translations()->where('language_code', 'ru')->value('id')}",
            'portuguese_slug' => "unique:category_translations,slug,{$cat->translations()->where('language_code', 'pt')->value('id')}",
            'dutch_slug' => "unique:category_translations,slug,{$cat->translations()->where('language_code', 'nl')->value('id')}",
            'italian_slug' => "unique:category_translations,slug,{$cat->translations()->where('language_code', 'it')->value('id')}",
            'swedish_slug' => "unique:category_translations,slug,{$cat->translations()->where('language_code', 'sv')->value('id')}",
            'danish_slug' => "unique:category_translations,slug,{$cat->translations()->where('language_code', 'da')->value('id')}",
            'norwegian_slug' => "unique:category_translations,slug,{$cat->translations()->where('language_code', 'nb')->value('id')}",
        ]);

        $english_slug = trim($request->english_slug);
        $english_name = trim($request->english_name);
        $english_description = trim($request->english_description);
        $english_meta_title = trim($request->english_meta_title);
        $english_meta_description = trim($request->english_meta_description);

        //Updating main category record (without translation)
        $cat->update([
            'sign' => trim($request->cat_sign),
            'user_id' => Auth::id()
        ]);

        if ($request->hasFile('cat_featured_img')) {
            if ($cat->hasMedia('featured_imgs')) {
                $cat->clearMediaCollection('featured_imgs');
            }
            $file = $request->file('cat_featured_img');
            $file_ext = strtolower($file->getClientOriginalExtension());
            $fileName = Str::random(8) . '_' . time() . '.' . $file_ext;

            $cat
                ->addMedia($file) // $article->addMediaFromRequest('content_img)
                //->usingFileName(Str::random(32).'.'.$file_ext)
                ->usingFileName($fileName)
                //->withResponsiveImages()
                ->toMediaCollection("featured_imgs");
        }

        //Updating English category translation
        $cat->translations()->where('language_code', 'en')->update([
            //'language_code' => 'en',
            'slug' => $english_slug,
            'name' => $english_name,
            'description' => $english_description,
            'meta_title' => $english_meta_title,
            'meta_description' => $english_meta_description,
            'updated_at' => Carbon::now()
        ]);

        $this->update_cat_trans($cat, $request, 'arabic', 'ar');
        $this->update_cat_trans($cat, $request, 'german', 'de');
        $this->update_cat_trans($cat, $request, 'spanish', 'es');
        $this->update_cat_trans($cat, $request, 'french', 'fr');
        $this->update_cat_trans($cat, $request, 'russian', 'ru');
        $this->update_cat_trans($cat, $request, 'portuguese', 'pt');
        $this->update_cat_trans($cat, $request, 'dutch', 'nl');
        $this->update_cat_trans($cat, $request, 'italian', 'it');
        $this->update_cat_trans($cat, $request, 'swedish', 'sv');
        $this->update_cat_trans($cat, $request, 'danish', 'da');
        $this->update_cat_trans($cat, $request, 'norwegian', 'nb');

        return to_route('admin.categories.index');
    }

    public function update_cat_trans(Category $cat, Request $request, string $lang, string $lang_code)
    {

        $lang_slug = $request->input("{$lang}_slug");
        $lang_name = $request->input("{$lang}_name");
        $lang_description = $request->input("{$lang}_description");
        $lang_meta_title = $request->input("{$lang}_meta_title");
        $lang_meta_description = $request->input("{$lang}_meta_description");
        if (
            $lang_slug != null &&
            $lang_name != null &&
            $lang_description != null &&
            $lang_meta_title != null &&
            $lang_meta_description != null
        ) {

            $cat_trans = $cat->translations()->where('language_code', $lang_code);
            if ($cat_trans->exists()) {
                $cat_trans->update([
                    'language_code' => $lang_code,
                    'slug' => trim($lang_slug),
                    'name' => trim($lang_name),
                    'description' => trim($lang_description),
                    'meta_title' => trim($lang_meta_title),
                    'meta_description' => trim($lang_meta_description),
                    'updated_at' => Carbon::now()
                ]);
            } else {
                //$this->store_cat_trans($cat, $request, $lang, $lang_code);
            }
        } else {
        }
    }
    */

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

    public function destroy($cat_id)
    {
        $cat = Category::findOrFail($cat_id);
        $cat->delete();
        return to_route('admin.categories.index');
    }
}
