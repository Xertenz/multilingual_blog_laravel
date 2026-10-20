<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Series;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SeriesController extends Controller
{
    /**
     * عرض قائمة السلاسل في لوحة التحكم
     */
    public function index()
    {
        // جلب السلاسل مع ترجماتها وصاحبها وعدد المقالات فيها
        $series = Series::with(['translations', 'user'])
            ->withCount('articles')
            ->latest()
            ->paginate(15);

        return view('admin.series.index', compact('series'));
    }

    /**
     * عرض صفحة إنشاء سلسلة جديدة
     */
    public function create()
    {
        // جلب المقالات المتاحة لربطها اختيارياً أثناء الإنشاء أو لاحقاً
        $articles = Article::with('translations')->latest()->get();
        return view('admin.series.create', compact('articles'));
    }

    /**
     * تخزين السلسلة الجديدة وترجماتها ومقالاتها في قاعدة البيانات
     */
    public function store(Request $request)
    {
        $request->validate([
            'internal_title' => 'required|string|unique:series,internal_title',
            'status' => 'required|in:draft,pending,published',

            // التحقق من الحقول المترجمة (مثلاً للغة العربية ar والإنجليزية en)
            'translations.*.title' => 'required|string|max:255',
            'translations.*.description' => 'nullable|string',
            'translations.*.slug' => 'required|string|max:255|unique:series_translations,slug',
            'articles' => 'nullable|array',
            'articles.*' => 'exists:articles,id',
        ]);

        DB::transaction(function () use ($request) {
            // 1. إنشاء السلسلة الأساسية
            $series = Series::create([
                'user_id' => Auth::id(),
                'internal_title' => $request->internal_title,
                'status' => $request->status,
            ]);

            // 2. إدخال الترجمات لكل لغة
            foreach ($request->translations as $locale => $data) {
                $series->translations()->create([
                    'locale' => $locale,
                    'title' => $data['title'],
                    'description' => $data['description'] ?? null,
                    'slug' => $data['slug'] ?? Str::slug($data['title']),
                ]);
            }

            // 3. ربط المقالات إن وجدت مع تعيين ترتيب ابتدائي
            if ($request->has('articles')) {
                $syncData = [];
                foreach ($request->articles as $index => $articleId) {
                    $syncData[$articleId] = ['sort_order' => $index + 1];
                }
                $series->articles()->sync($syncData);
            }
        });

        return redirect()->route('admin.series.index')->with('success', 'Series Created Successfully.');
    }

    /**
     * عرض صفحة تعديل السلسلة
     */
    public function edit(Series $series)
    {
        $series->load(['translations', 'articles']);
        $articles = Article::with('translations')->latest()->get();

        return view('admin.series.edit', compact('series', 'articles'));
    }

    /**
     * تحديث السلسلة
     */
    public function update(Request $request, Series $series)
    {
        $request->validate([
            'internal_title' => 'required|string|unique:series,internal_title,' . ($series->id ?? ''),
            'status' => 'required|in:draft,pending,published',

            'translations.*.title' => 'required|string|max:255',
            'translations.*.description' => 'nullable|string',
            'translations.*.slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('series_translations', 'slug')->ignore($series->id, 'series_id')
            ],
            'articles' => 'nullable|array',
            'articles.*' => 'exists:articles,id',
        ]);

        DB::transaction(function () use ($request, $series) {
            // 1. تحديث الجدول الأساسي
            $series->update([
                'internal_title' => $request->internal_title,
                'status' => $request->status,
            ]);

            // 2. تحديث أو إنشـاء الترجمات لكل لغة
            foreach ($request->translations as $locale => $data) {
                $series->translations()->updateOrCreate(
                    ['locale' => $locale],
                    [
                        'title' => $data['title'],
                        'description' => $data['description'] ?? null,
                        'slug' => $data['slug'] ?? Str::slug($data['title']),
                    ]
                );
            }

            // 3. تحديث المقالات المرتبطة وترتيبها
            if ($request->has('articles')) {
                $syncData = [];
                foreach ($request->articles as $index => $articleId) {
                    $syncData[$articleId] = ['sort_order' => $index + 1];
                }
                $series->articles()->sync($syncData);
            } else {
                $series->articles()->detach();
            }
        });

        return redirect()->route('admin.series.index')->with('success', 'Series Updated Successfully.');
    }

    /**
     * حذف السلسلة
     */
    public function destroy(Series $series)
    {
        // بفضل cascadeOnDelete في قاعدة البيانات، سيتم حذف الترجمات وارتباطات المقالات تلقائياً
        $series->delete();

        return redirect()->route('admin.series.index')->with('success', 'Series Deleted Successfully.');
    }
}
