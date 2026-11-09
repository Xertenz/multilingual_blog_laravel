<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GameCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class GameCategoryController extends Controller
{
    public function index()
    {
        $cats = GameCategory::with(['translations', 'media', 'games'])->withCount('games')->paginate(10);
        return view('admin.game_categories.index', compact('cats'));
    }

    public function create()
    {
        $supportedLocales = LaravelLocalization::getSupportedLocales();
        return view('admin.game_categories.create', compact('supportedLocales'));
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
            'cat_label' => 'required|string|unique:game_categories,label|max:50',
            'cat_featured_img' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
            'translations.*.name' => 'nullable|string|max:100',
            'translations.*.description' => 'nullable|string',
            'translations.*.meta_title' => 'nullable|string|max:150',
            'translations.*.meta_description' => 'nullable|string|max:255',
        ]);

        foreach ($request->translations as $localeCode => $data) {
            if (!empty($data['slug'])) {
                $request->validate([
                    "translations.{$localeCode}.slug" => [
                        'nullable',
                        'string',
                        'max:100',
                        Rule::unique('game_category_translations', 'slug')->where('locale', $localeCode),
                    ],
                ]);
            }
        }

        $cat = GameCategory::create([
            'label'   => (string) Str::of($request->cat_label)->slug('-'),
            'user_id' => Auth::id()
        ]);

        if ($request->hasFile('cat_featured_img')) {
            $file = $request->file('cat_featured_img');
            $file_ext = strtolower($file->getClientOriginalExtension());
            $fileName = uniqid() . '.' . $file_ext;

            $cat->addMedia($file)
                ->usingFileName($fileName)
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
            ]);
        }

        return to_route('admin.game-categories.index')->with('success', 'Game Category Created Successfully!');
    }

    public function edit(int $id)
    {
        $supportedLocales = LaravelLocalization::getSupportedLocales();
        $cat = GameCategory::with('translations')->findOrFail($id);
        $translations = $cat->translations->keyBy('locale');
        return view('admin.game_categories.edit', compact('cat', 'translations', 'supportedLocales'));
    }

    public function update(Request $request, int $id)
    {
        $cat = GameCategory::findOrFail($id);

        if ($request->has('cat_label')) {
            $request->merge([
                'cat_label' => (string) Str::of($request->cat_label)->slug('-')
            ]);
        }

        $request->validate([
            'cat_label' => "required|string|max:50|unique:game_categories,label,{$id}",
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
                    "translations.{$localeCode}.slug" => [
                        'nullable',
                        'string',
                        'max:100',
                        Rule::unique('game_category_translations', 'slug')
                            ->where('locale', $localeCode)
                            ->ignore($currentTransId),
                    ],
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
                ]
            );
        }

        return to_route('admin.game-categories.index')->with('success', 'Game Category Updated Successfully!');
    }

    public function destroy(int $id)
    {
        $cat = GameCategory::findOrFail($id);
        $cat->delete();

        return to_route('admin.game-categories.index')->with('success', 'Game Category Deleted Successfully!');
    }
}
