<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookTag;
use App\Models\BookTagTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class BookTagController extends Controller
{
    public function index()
    {
        $tags = BookTag::with('translations')->paginate(10);
        return view('admin.book_tags.index', compact('tags'));
    }

    public function create()
    {
        $supportedLocales = LaravelLocalization::getSupportedLocales();
        return view('admin.book_tags.create', compact('supportedLocales'));
    }

    public function store(Request $request)
    {
        // 1. التحقق من البيانات مع إضافة قواعد الحقول الجديدة
        $request->validate([
            'label'                     => 'required|string|unique:book_tags,label|max:50',
            'translations.*.name'       => 'nullable|string|max:50',
            'translations.*.slug'       => 'nullable|string|max:50',
            'translations.*.description' => 'nullable|string|max:1000',
            'translations.*.meta_title'  => 'nullable|string|max:70',
            'translations.*.meta_description' => 'nullable|string|max:160',
        ]);

        $tag = BookTag::create([
            'user_id' => Auth::id(),
            'label'   => Str::slug($request->label, '-'),
        ]);

        // 2. حفظ الترجمات وحقول الـ SEO
        foreach ($request->translations as $localeCode => $data) {
            if (!empty($data['name'])) {
                BookTagTranslation::create([
                    'book_tag_id'      => $tag->id,
                    'locale'           => $localeCode,
                    'name'             => $data['name'],
                    'slug'             => !empty($data['slug']) ? Str::slug($data['slug'], '-') : Str::slug($data['name'], '-', null),
                    'description'      => $data['description'] ?? null,
                    'meta_title'       => $data['meta_title'] ?? null,
                    'meta_description' => $data['meta_description'] ?? null,
                ]);
            }
        }

        return to_route('admin.book-tags.index')->with('success', 'Book Tag added successfully.');
    }

    public function edit(BookTag $tag)
    {
        $supportedLocales = LaravelLocalization::getSupportedLocales();
        $tag->load('translations');
        return view('admin.book_tags.edit', compact('tag', 'supportedLocales'));
    }

    public function update(Request $request, BookTag $tag)
    {
        // 1. التحقق من البيانات أثناء التحديث
        $request->validate([
            'label'                     => 'required|string|max:50|unique:book_tags,label,' . $tag->id,
            'translations.*.name'       => 'nullable|string|max:50',
            'translations.*.slug'       => 'nullable|string|max:50',
            'translations.*.description' => 'nullable|string|max:1000',
            'translations.*.meta_title'  => 'nullable|string|max:70',
            'translations.*.meta_description' => 'nullable|string|max:160',
        ]);

        $tag->update([
            'label' => Str::of($request->label)->slug('-')
        ]);

        // 2. تحديث أو إنشاء الترجمات وحقول الـ SEO
        foreach ($request->translations as $localeCode => $data) {
            if (empty($data['name'])) {
                continue;
            }

            $finalSlug = !empty($data['slug'])
                ? (string) Str::of($data['slug'])->slug('-')
                : (string) Str::of($data['name'])->slug('-');

            BookTagTranslation::updateOrCreate(
                [
                    'book_tag_id' => $tag->id,
                    'locale'      => $localeCode,
                ],
                [
                    'name'             => $data['name'],
                    'slug'             => $finalSlug,
                    'description'      => $data['description'] ?? null,
                    'meta_title'       => $data['meta_title'] ?? null,
                    'meta_description' => $data['meta_description'] ?? null,
                ]
            );
        }

        return to_route('admin.book-tags.index')->with('success', 'Book Tag updated successfully!');
    }

    public function destroy(int $id)
    {
        $tag = BookTag::findOrFail($id);

        if (method_exists($tag, 'books')) {
            $tag->books()->detach();
        }

        if (method_exists($tag, 'translations')) {
            $tag->translations()->delete();
        }

        $tag->delete();

        return to_route('admin.book-tags.index')->with('success', 'Book Tag and its relations deleted successfully!');
    }
}
