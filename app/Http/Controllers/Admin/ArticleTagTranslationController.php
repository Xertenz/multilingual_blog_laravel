<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleTag;
use App\Models\ArticleTagTranslation;
use App\Models\Tag;
use App\Models\TagTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class ArticleTagTranslationController extends Controller
{
    //
    public function create()
    {
        return view('admin.tags.create');
    }

    public function store(Request $request)
    {
        // 1. التحقق من البيانات مع إضافة قواعد الحقول الجديدة
        $request->validate([
            'label' => 'required|string|unique:article_tags,label|max:50',
            'translations.*.name' => 'nullable|string|max:50',
            'translations.*.slug' => 'nullable|string|max:50',
            'translations.*.description' => 'nullable|string|max:1000',
            'translations.*.meta_title' => 'nullable|string|max:70',
            'translations.*.meta_description' => 'nullable|string|max:160',
        ]);

        // 2. إضافة الـ Label في جدول article_tags الأساسي
        $tag = ArticleTag::create([
            'label' => Str::slug($request->label, '-')
        ]);

        // 3. تخزين الترجمات والحقول الجديدة
        foreach ($request->translations as $localeCode => $data) {
            if (!empty($data['name'])) {
                ArticleTagTranslation::create([
                    'article_tag_id'   => $tag->id,
                    'locale'           => $localeCode,
                    'name'             => $data['name'],
                    'slug'             => !empty($data['slug']) ? Str::slug($data['slug'], '-') : Str::slug($data['name'], '-'),
                    'description'      => $data['description'] ?? null,
                    'meta_title'       => $data['meta_title'] ?? null,
                    'meta_description' => $data['meta_description'] ?? null,
                ]);
            }
        }

        return to_route('admin.article-tags.index')->with('success', 'Tag added successfully.');
    }

    public function show(int $tag_trans_id, ?string $slug = null)
    {

        // 1. جلب ترجمة التاك المطلوبة مع التاك الأساسي وعلاقاته
        $tag_trans = ArticleTagTranslation::with('tag')->find($tag_trans_id);
        if (!$tag_trans) {
            return to_route('home.index');
        }

        $currentLocale = LaravelLocalization::getCurrentLocale();

        // 2. التحقق من تطابق لغة التاك مع لغة الموقع الحالية (Fallback الذكي لمنع الـ Infinite Loop)
        if ($tag_trans->locale !== $currentLocale) {
            $target_trans = $tag_trans->tag->translations
                ->where('locale', $currentLocale)
                ->first();

            if ($target_trans) {
                return redirect()->route('article-tags.show', [
                    'id'   => $target_trans->id,
                    'slug' => $target_trans->slug
                ]);
            }

            return to_route('home.index')->with('info', __('This tag is not available in your language.'));
        }

        // 3. تصحيح الـ Slug في الـ URL لدعم SEO
        if (empty($slug) || $tag_trans->slug !== $slug) {
            return redirect()->route('article-tags.show', [
                'id'   => $tag_trans->id,
                'slug' => $tag_trans->slug
            ]);
        }

        // 5. جلب المقالات المكتملة مع الصور (media) والترجمات والمؤلف
        $articles = $tag_trans->tag->articles()
            ->with(['translations', 'media', 'author'])
            ->where('status', 'published')
            ->latest()
            ->paginate(12);

        return view('tag', compact('tag_trans', 'articles'));
    }

    public function edit(ArticleTag $tag)
    {
        return view('admin.tags.edit', compact('tag'));
    }

    public function update(Request $request, ArticleTag $tag)
    {
        // 1. التحقق من البيانات للتحديث
        $request->validate([
            'label' => 'required|string|max:50|unique:article_tags,label,' . $tag->id,
            'translations.*.name' => 'nullable|string|max:50',
            'translations.*.slug' => 'nullable|string|max:50',
            'translations.*.description' => 'nullable|string|max:1000',
            'translations.*.meta_title' => 'nullable|string|max:70',
            'translations.*.meta_description' => 'nullable|string|max:160',
        ]);

        // 2. تحديث الـ Label الأساسي
        $tag->update([
            'label' => Str::of($request->label)->slug('-')
        ]);

        // 3. التحديث أو الإنشاء عبر updateOrCreate للترجمات والحقول الجديدة
        foreach ($request->translations as $localeCode => $data) {
            if (empty($data['name'])) {
                continue;
            }

            $finalSlug = !empty($data['slug'])
                ? (string) Str::of($data['slug'])->slug('-')
                : (string) Str::of($data['name'])->slug('-');

            ArticleTagTranslation::updateOrCreate(
                [
                    'article_tag_id' => $tag->id,
                    'locale'         => $localeCode,
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

        return redirect()->route('admin.article-tags.index')->with('success', 'Tag updated successfully!');
    }

    //public function destroy(Tag $Tag) {}
    public function destroy(int $id)
    {
        // 1. جلب الوسم أو إرجاع خطأ 404 في حال عدم وجوده لضمان أمان العملية
        $tag = ArticleTag::findOrFail($id);

        // 2. فك ارتباط الوسم بجميع المقالات المرتبطة به في الجدول الوسيط (إن وُجدت)
        // هذا يمنع حدوث مشاكل في قاعدة البيانات (Foreign Key Constraints)
        if (method_exists($tag, 'articles')) {
            $tag->articles()->detach();
        }

        // 3. إذا كنت تستخدم جدول ترجمات منفصل للوسوم (مثل الفئات)، قم بحذف الترجمات أولاً
        if (method_exists($tag, 'translations')) {
            $tag->translations()->delete();
        }

        // 4. حذف السجل الأساسي للوسم
        $tag->delete();

        // 5. إعادة التوجيه إلى الصفحة الرئيسية للوسوم مع رسالة نجاح (Session Flash Message)
        return to_route('admin.article-tags.index')->with('success', 'Tag and its relations deleted successfully!');
    }
}
