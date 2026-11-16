<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommunityCaseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class CommunityCaseCategoryController extends Controller
{
    /**
     * عرض قائمة فئات قضايا المجتمع في لوحة التحكم
     */
    public function index()
    {
        $categories = CommunityCaseCategory::with('translations')->latest()->paginate(10);
        return view('admin.community_case_categories.index', compact('categories'));
    }

    /**
     * عرض صفحة إنشاء فئة جديدة
     */
    public function create()
    {
        $locales = LaravelLocalization::getSupportedLocales();
        return view('admin.community_case_categories.create', compact('locales'));
    }

    /**
     * حفظ الفئة الجديدة مع ترجماتها في قاعدة البيانات
     */
    public function store(Request $request)
    {
        $validLocales = LaravelLocalization::getSupportedLanguagesKeys();

        $request->validate([
            'label' => 'required|string|max:50|unique:community_case_categories,label',
            'is_active' => 'boolean',

            'translations.*.name'             => 'nullable|string|max:100',
            'translations.*.slug'             => 'nullable|string|max:100|unique:community_case_category_translations,slug',
            'translations.*.description'      => 'nullable|string',
            'translations.*.meta_title'       => 'nullable|string|max:255',
            'translations.*.meta_description' => 'nullable|string',
        ]);

        $category = CommunityCaseCategory::create([
            'user_id'   => Auth::id(),  // ✅ إضافة
            'label'     => (string) Str::of($request->label)->slug('-'),
            'is_active' => $request->has('is_active'),
        ]);

        if ($request->has('translations')) {
            foreach ($request->translations as $localeCode => $transData) {
                if (!in_array($localeCode, $validLocales) || empty($transData['name'])) {
                    continue;
                }

                $slugSource = !empty($transData['slug']) ? $transData['slug'] : $transData['name'];
                $slug = (string) Str::of($slugSource)->slug('-');

                $category->translations()->create([
                    'locale'           => $localeCode,
                    'name'             => trim($transData['name']),
                    'slug'             => trim($slug),
                    'description'      => trim($transData['description'] ?? ''),
                    'meta_title'       => trim($transData['meta_title'] ?? ''),       // ✅ إضافة
                    'meta_description' => trim($transData['meta_description'] ?? ''), // ✅ إضافة
                ]);
            }
        }

        return redirect()->route('admin.community-case-categories.index')
            ->with('success', 'Community Case Category created successfully.');
    }

    /**
     * عرض صفحة تعديل الفئة
     */
    public function edit(CommunityCaseCategory $communityCaseCategory)
    {
        $communityCaseCategory->load('translations');
        $locales = LaravelLocalization::getSupportedLocales();

        // تجهيز الترجمات بمفتاح اللغة لتسهيل عرضها في الفورم
        $translations = $communityCaseCategory->translations->keyBy('locale');

        return view('admin.community_case_categories.edit', compact('communityCaseCategory', 'translations', 'locales'));
    }

    /**
     * تحديث بيانات الفئة والترجمات
     */
    public function update(Request $request, CommunityCaseCategory $communityCaseCategory)
    {
        $validLocales = LaravelLocalization::getSupportedLanguagesKeys();

        $request->validate([
            'label' => ['required', 'string', 'max:50', Rule::unique('community_case_categories', 'label')->ignore($communityCaseCategory->id)],
            'is_active' => 'boolean',

            'translations.*.name'             => 'nullable|string|max:100',
            'translations.*.description'      => 'nullable|string',
            'translations.*.meta_title'       => 'nullable|string|max:255',   // ✅ إضافة
            'translations.*.meta_description' => 'nullable|string',           // ✅ إضافة
        ]);

        // التحقق الديناميكي من الـ slug
        if ($request->has('translations')) {
            foreach ($request->translations as $localeCode => $transData) {
                if (!empty($transData['slug'])) {
                    $currentTransId = $communityCaseCategory->translations()->where('locale', $localeCode)->value('id');
                    $request->validate([
                        "translations.{$localeCode}.slug" => "nullable|string|max:100|unique:community_case_category_translations,slug,{$currentTransId}",
                    ]);
                }
            }
        }

        $communityCaseCategory->update([
            'label'     => (string) Str::of($request->label)->slug('-'),
            'is_active' => $request->has('is_active'),
        ]);

        if ($request->has('translations')) {
            foreach ($request->translations as $localeCode => $transData) {
                if (!in_array($localeCode, $validLocales)) {
                    continue;
                }

                if (empty($transData['name'])) {
                    $communityCaseCategory->translations()->where('locale', $localeCode)->delete();
                    continue;
                }

                $slugSource = !empty($transData['slug']) ? $transData['slug'] : $transData['name'];
                $slug = (string) Str::of($slugSource)->slug('-');

                $communityCaseCategory->translations()->updateOrCreate(
                    ['locale' => $localeCode],
                    [
                        'name'             => trim($transData['name']),
                        'slug'             => trim($slug),
                        'description'      => trim($transData['description'] ?? ''),
                        'meta_title'       => trim($transData['meta_title'] ?? ''),       // ✅ إضافة
                        'meta_description' => trim($transData['meta_description'] ?? ''), // ✅ إضافة
                    ]
                );
            }
        }

        return redirect()->route('admin.community-case-categories.index')
            ->with('success', 'Community Case Category updated successfully.');
    }

    /**
     * حذف الفئة مع ترجماتها
     */
    public function destroy(CommunityCaseCategory $communityCaseCategory)
    {
        $communityCaseCategory->delete(); // سيتم حذف الترجمات تلقائياً بفضل الـ cascadeOnDelete

        return redirect()->route('admin.community-case-categories.index')
            ->with('success', 'Community Case Category deleted successfully.');
    }
}
