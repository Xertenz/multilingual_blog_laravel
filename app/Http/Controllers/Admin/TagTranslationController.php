<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Tag;
use App\Models\TagTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class TagTranslationController extends Controller
{
    //
    public function create()
    {
        return view('admin.tags.create');
    }

    public function store(Request $request)
    {
        // 1. التحقق من البيانات (Label مطلوب والترجمات مصفوفة)
        $request->validate([
            'label' => 'required|string|unique:tags,label|max:50',
            'translations.*.name' => 'nullable|string|max:50', // جعلناها nullable لو أراد إضافة لغات معينة فقط وتأجيل الباقي
        ]);

        // 2. إضافة الـ Label في جدول tags الأساسي
        $tag = Tag::create([
            'label' => Str::slug($request->label, '-') // تجميل الـ Label ليكون متناسقاً كرمز
        ]);

        // 3. الدوران حول لغات الـ 12 الممررة من الحقول
        foreach ($request->translations as $localeCode => $data) {

            // التحقق من أن الحقل ليس فارغاً لهذه اللغة
            if (!empty($data['name'])) {

                TagTranslation::create([
                    'tag_id'        => $tag->id,
                    'locale' => $localeCode, // يُحفظ تلقائياً من الـ Key الخاص بالمصفوفة (ar, en, ja...)
                    'name'          => $data['name'],
                    // توليد السلوج تلقائياً بناءً على الاسم المكتوب باللغة نفسها
                    'slug'          => Str::slug($data['name'], '-', null)
                ]);
            }
        }

        return to_route('admin.tags.index')->with('success', 'Tag added successfully.');
    }

    public function show(int $tag_trans_id, ?string $slug = null)
    {

        // 1. جلب ترجمة التاك المطلوبة مع التاك الأساسي وعلاقاته
        $tag_trans = TagTranslation::with('tag')->find($tag_trans_id);
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
                return redirect()->route('tags.show', [
                    'id'   => $target_trans->id,
                    'slug' => $target_trans->slug
                ]);
            }

            return to_route('home.index')->with('info', __('This tag is not available in your language.'));
        }

        // 3. تصحيح الـ Slug في الـ URL لدعم SEO
        if (empty($slug) || $tag_trans->slug !== $slug) {
            return redirect()->route('tags.show', [
                'id'   => $tag_trans->id,
                'slug' => $tag_trans->slug
            ]);
        }

        // 5. جلب المقالات المكتملة مع الصور (media) والترجمات والمؤلف
        $articles = $tag_trans->tag->articles()
            ->with(['translations', 'media', 'user'])
            ->where('status', 'published')
            ->latest()
            ->paginate(12);

        return view('tag', compact('tag_trans', 'articles'));
    }

    public function edit(Tag $tag)
    {
        return view('admin.tags.edit', compact('tag'));
    }

    public function update(Request $request, Tag $tag)
    {
        // 1. الآن نقوم بعمل الـ Validation على القيمة النظيفة والنهائية
        $request->validate([
            'label' => 'required|string|max:50|unique:tags,label,' . $tag->id,
            'translations.*.name' => 'nullable|string|max:50',
            'translations.*.slug' => 'nullable|string|max:50',
        ]);

        // 2. تحديث الـ Label الأساسي بأمان تام وبدون تكرار الدالة
        $tag->update([
            'label' => Str::of($request->label)->slug('-')
        ]);

        // 3. الدوران حول اللغات الـ 12
        foreach ($request->translations as $localeCode => $data) {
            if (empty($data['name'])) {
                continue;
            }

            $finalSlug = !empty($data['slug'])
                ? (string) Str::of($data['slug'])->slug('-')
                : (string) Str::of($data['name'])->slug('-');

            // دالة updateOrCreate تبحث عن السطر بناءً على الشرط الأول، وتحدثه أو تنشئه بناءً على مصفوفة البيانات الثانية
            TagTranslation::updateOrCreate(
                [
                    'tag_id'        => $tag->id,
                    'locale' => $localeCode,
                ],
                [
                    'name'          => $data['name'],
                    'slug'          => $finalSlug,
                ]
            );
        }

        return redirect()->route('admin.tags.index')->with('success', 'Tag updated successfully!');
    }

    //public function destroy(Tag $Tag) {}
    public function destroy(int $id)
    {
        // 1. جلب الوسم أو إرجاع خطأ 404 في حال عدم وجوده لضمان أمان العملية
        $tag = Tag::findOrFail($id);

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
        return to_route('admin.tags.index')->with('success', 'Tag and its relations deleted successfully!');
    }
}
