<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookCategory;
use App\Models\BookCategoryTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Carbon\Carbon;

class BookCategoryController extends Controller
{
    public function index()
    {
        $cats = BookCategory::with('media', 'books')->withCount('books')->paginate(10);
        return view('admin.book_categories.index', compact('cats'));
    }

    public function create()
    {
        $supportedLocales = \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getSupportedLocales();
        return view('admin.book_categories.create', compact('supportedLocales'));
    }

    public function store(Request $request)
    {
        $translations = $request->input('translations', []);

        foreach ($translations as $locale => $data) {
            if (!empty($data['name'])) {
                $slugSource = !empty($data['slug']) ? $data['slug'] : $data['name'];
                $translations[$locale]['slug'] = (string) Str::of($slugSource)->slug('-');
            }
        }

        $request->merge(['translations' => $translations]);

        $request->validate([
            'cat_label' => 'required|string|unique:book_categories,label|max:50',
            'cat_featured_img' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',

            'translations.*.name' => 'nullable|string|max:100',
            'translations.*.slug' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('book_category_translations', 'slug')
            ],
            'translations.*.description' => 'nullable|string',
            'translations.*.meta_title' => 'nullable|string|max:150',
            'translations.*.meta_description' => 'nullable|string|max:255',
        ]);

        $cat = BookCategory::create([
            'label'   => (string) Str::of($request->cat_label)->slug('-'),
            'user_id' => Auth::id()
        ]);

        if ($request->hasFile('cat_featured_img')) {
            $file = $request->file('cat_featured_img');
            $file_ext = strtolower($file->getClientOriginalExtension());
            $fileName = uniqid() . '.' . $file_ext;

            $cat->addMedia($file)
                ->usingFileName($fileName)
                ->withResponsiveImages()
                ->toMediaCollection("featured_imgs");
        }

        $validLocales = LaravelLocalization::getSupportedLanguagesKeys();

        foreach ($request->translations as $localeCode => $data) {
            if (!in_array($localeCode, $validLocales) || empty($data['name'])) {
                continue;
            }

            $cat->translations()->create([
                'locale'           => $localeCode,
                'slug'             => trim($data['slug']),
                'name'             => trim($data['name']),
                'description'      => trim($data['description'] ?? ''),
                'meta_title'       => trim($data['meta_title'] ?? $data['name']),
                'meta_description' => trim($data['meta_description'] ?? Str::limit(strip_tags($data['description'] ?? ''), 150)),
                'created_at'       => Carbon::now(),
                'updated_at'       => Carbon::now(),
            ]);
        }

        return to_route('admin.book-categories.index')->with('success', 'Book Category Created Successfully!');
    }

    public function edit(int $id)
    {
        $supportedLocales = \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getSupportedLocales();
        $cat = BookCategory::with('translations')->findOrFail($id);
        $translations = $cat->translations->keyBy('locale');
        return view('admin.book_categories.edit', compact('cat', 'translations', 'supportedLocales'));
    }

    public function update(Request $request, int $id)
    {
        $cat = BookCategory::findOrFail($id);

        if ($request->has('cat_label')) {
            $request->merge([
                'cat_label' => (string) Str::of($request->cat_label)->slug('-')
            ]);
        }

        $request->validate([
            'cat_label' => "required|string|max:50|unique:book_categories,label,{$id}",
            'cat_featured_img' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',

            'translations.*.name' => 'nullable|string|max:100',
            'translations.*.description' => 'nullable|string',
            'translations.*.meta_title' => 'nullable|string|max:150',
            'translations.*.meta_description' => 'nullable|string|max:255',
        ]);

        foreach ($request->translations as $localeCode => $data) {
            if (!empty($data['slug'])) {
                $currentTransId = $cat->translations()->where('locale', $localeCode)->value('id');

                $request->validate([
                    "translations.{$localeCode}.slug" => "nullable|string|max:100|unique:book_category_translations,slug,{$currentTransId}",
                ]);
            }
        }

        $cat->update([
            'label'   => $request->cat_label,
            'user_id' => Auth::id()
        ]);

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

        foreach ($request->translations as $localeCode => $data) {
            if (empty($data['name'])) {
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
                    'meta_title'       => trim($data['meta_title'] ?? $data['name']),
                    'meta_description' => trim($data['meta_description'] ?? Str::limit(strip_tags($data['description'] ?? ''), 150)),
                    'updated_at'       => Carbon::now()
                ]
            );
        }

        return to_route('admin.book-categories.index')->with('success', 'Book Category Updated Successfully!');
    }

    public function destroy(int $id)
    {
        $cat = BookCategory::findOrFail($id);
        $cat->delete();

        return to_route('admin.book-categories.index')->with('success', 'Book Category Deleted Successfully!');
    }
}
