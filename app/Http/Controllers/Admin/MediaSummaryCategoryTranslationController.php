<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MediaSummary;
use App\Models\MediaSummaryCategory;
use App\Models\MediaSummaryCategoryTranslation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class MediaSummaryCategoryTranslationController extends Controller
{
    public function create()
    {
        $supportedLocales = LaravelLocalization::getSupportedLocales();
        return view('admin.media_summary_categories.create', compact('supportedLocales'));
    }

    public function store(Request $request)
    {
        // 1. توليد الـ slug لكل لغة
        $translations = $request->input('translations', []);
        foreach ($translations as $locale => $data) {
            if (!empty($data['name'])) {
                $slugSource = !empty($data['slug']) ? $data['slug'] : $data['name'];
                $translations[$locale]['slug'] = (string) Str::of($slugSource)->slug('-');
            }
        }
        $request->merge(['translations' => $translations]);

        if ($request->has('cat_label')) {
            $request->merge([
                'cat_label' => (string) Str::of($request->cat_label)->slug('-'),
            ]);
        }

        // 2. التحقق
        $request->validate([
            'cat_label' => 'required|string|unique:media_summary_categories,label|max:100',
            'cat_featured_img' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
            'is_active' => 'nullable|boolean',  // ✅

            'translations.*.name' => 'nullable|string|max:150',
            'translations.*.slug' => [
                'nullable',
                'string',
                'max:150',
                Rule::unique('media_summary_category_translations', 'slug'),
            ],
            'translations.*.description' => 'nullable|string',
            'translations.*.meta_title' => 'nullable|string|max:150',
            'translations.*.meta_description' => 'nullable|string|max:255',
        ]);

        // 3. إنشاء الفئة
        $cat = MediaSummaryCategory::create([
            'user_id'   => Auth::id(),
            'label'     => $request->cat_label,
            'is_active' => $request->boolean('is_active'),  // ✅
        ]);

        // 4. رفع الصورة
        if ($request->hasFile('cat_featured_img')) {
            $file = $request->file('cat_featured_img');
            $fileName = uniqid() . '.' . strtolower($file->getClientOriginalExtension());
            $cat->addMedia($file)
                ->usingFileName($fileName)
                ->withResponsiveImages()
                ->toMediaCollection('featured_imgs');
        }

        // 5. الترجمات
        $validLocales = LaravelLocalization::getSupportedLanguagesKeys();
        foreach ($request->input('translations', []) as $localeCode => $data) {
            if (!in_array($localeCode, $validLocales) || empty($data['name'])) {
                continue;
            }
            $cat->translations()->create([
                'locale'           => $localeCode,
                'slug'             => trim($data['slug']),
                'name'             => trim($data['name']),
                'description'      => trim($data['description'] ?? ''),
                'meta_title'       => trim(!empty($data['meta_title']) ? $data['meta_title'] : $data['name']),
                'meta_description' => trim(!empty($data['meta_description']) ? $data['meta_description'] : Str::limit(strip_tags($data['description'] ?? ''), 150)),
                'created_at'       => Carbon::now(),
                'updated_at'       => Carbon::now(),
            ]);
        }

        return to_route('admin.media-summary-categories.index')
            ->with('success', 'Media Summary Category Created Successfully!');
    }

    public function show(int $category_trans_id, ?string $slug = null)
    {
        $category_trans = MediaSummaryCategoryTranslation::with(['category.translations'])->find($category_trans_id);

        if (!$category_trans) {
            return to_route('home.index');
        }

        $current_locale = LaravelLocalization::getCurrentLocale();

        if ($category_trans->locale !== $current_locale) {
            $category_trans = $category_trans->category->translations
                ->where('locale', $current_locale)
                ->first();

            if ($category_trans) {
                return redirect()->route('media-summary-categories.show', [
                    'id' => $category_trans->id,
                    'slug' => $category_trans->slug,
                ]);
            }

            return to_route('home.index')->with('info', __('This category is not available in your language.'));
        }

        if (empty($slug) || $category_trans->slug !== $slug) {
            return redirect()->route('media-summary-categories.show', [
                'id' => $category_trans->id,
                'slug' => $category_trans->slug,
            ]);
        }

        // ✅ الفئة معطلة → إعادة توجيه
        if (!$category_trans->category->is_active) {
            return to_route('home.index')->with('info', __('This category is not available at the moment.'));
        }

        $mediaSummaries = MediaSummary::with(['translations', 'media', 'user'])
            ->where('media_summary_category_id', $category_trans->category->id)
            ->where('status', 'published')
            ->latest()
            ->paginate(12);

        return view('media_summary_category', compact('category_trans', 'mediaSummaries'));
    }

    public function edit(int $id)
    {
        $cat = MediaSummaryCategory::with('translations')->findOrFail($id);
        $translations = $cat->translations->keyBy('locale');
        $supportedLocales = LaravelLocalization::getSupportedLocales();

        return view('admin.media_summary_categories.edit', compact('cat', 'translations', 'supportedLocales'));
    }

    public function update(Request $request, int $id)
    {
        $cat = MediaSummaryCategory::findOrFail($id);

        if ($request->has('cat_label')) {
            $request->merge([
                'cat_label' => (string) Str::of($request->cat_label)->slug('-'),
            ]);
        }

        $request->validate([
            'cat_label' => "required|string|max:100|unique:media_summary_categories,label,{$id}",
            'cat_featured_img' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
            'is_active' => 'nullable|boolean',  // ✅

            'translations.*.name' => 'nullable|string|max:150',
            'translations.*.description' => 'nullable|string',
            'translations.*.meta_title' => 'nullable|string|max:150',
            'translations.*.meta_description' => 'nullable|string|max:255',
        ]);

        // التحقق الديناميكي من slug
        foreach ($request->input('translations', []) as $localeCode => $data) {
            if (!empty($data['slug'])) {
                $currentTransId = $cat->translations()->where('locale', $localeCode)->value('id');
                $request->validate([
                    "translations.{$localeCode}.slug" => [
                        'nullable',
                        'string',
                        'max:150',
                        Rule::unique('media_summary_category_translations', 'slug')->ignore($currentTransId),
                    ],
                ]);
            }
        }

        // ✅ تحديث الأساسي + is_active (بدون user_id)
        $cat->update([
            'label'     => $request->cat_label,
            'is_active' => $request->boolean('is_active'),
        ]);

        // الصورة
        if ($request->hasFile('cat_featured_img')) {
            if ($cat->hasMedia('featured_imgs')) {
                $cat->clearMediaCollection('featured_imgs');
            }
            $file = $request->file('cat_featured_img');
            $fileName = uniqid() . '.' . strtolower($file->getClientOriginalExtension());
            $cat->addMedia($file)
                ->usingFileName($fileName)
                ->withResponsiveImages()
                ->toMediaCollection('featured_imgs');
        }

        // الترجمات
        $validLocales = LaravelLocalization::getSupportedLanguagesKeys();
        foreach ($request->input('translations', []) as $localeCode => $data) {
            if (!in_array($localeCode, $validLocales) || empty($data['name'])) {
                continue;
            }
            $finalSlug = !empty($data['slug'])
                ? (string) Str::of($data['slug'])->slug('-')
                : (string) Str::of($data['name'])->slug('-');

            $cat->translations()->updateOrCreate(
                ['locale' => $localeCode],
                [
                    'slug'             => trim($finalSlug),
                    'name'             => trim($data['name']),
                    'description'      => trim($data['description'] ?? ''),
                    'meta_title'       => trim(!empty($data['meta_title']) ? $data['meta_title'] : $data['name']),
                    'meta_description' => trim(!empty($data['meta_description']) ? $data['meta_description'] : Str::limit(strip_tags($data['description'] ?? ''), 150)),
                    'updated_at'       => Carbon::now(),
                ]
            );
        }

        return to_route('admin.media-summary-categories.index')
            ->with('success', 'Media Summary Category Updated Successfully!');
    }

    public function destroy(int $id)
    {
        $cat = MediaSummaryCategory::findOrFail($id);
        $cat->delete();

        return to_route('admin.media-summary-categories.index')
            ->with('success', 'Media Summary Category Deleted Successfully!');
    }
}
