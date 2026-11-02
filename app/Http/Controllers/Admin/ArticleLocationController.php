<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleLocation;
use Illuminate\Http\Request;

class ArticleLocationController extends Controller
{
    /*
    public function index(Request $request)
    {
        // جلب المقالات مع مواقعه وترجماتها
        $articles = Article::with(['translations', 'locations.translations'])->latest()->paginate(15);

        // المقال المختار لتعيين الموقع (إن وجد)
        $selectedArticle = $request->has('article_id')
            ? Article::with('locations.translations')->find($request->article_id)
            : null;

        return view('admin.article_locations.index', compact('articles', 'selectedArticle'));
    }
        */



    public function index(Request $request)
    {
        // جلب المقالات مع مواقعه وترجماتها
        $articles = Article::with(['translations', 'locations.translations'])->latest()->paginate(15);

        // المقال المختار لتعيين الموقع (إن وجد)
        $selectedArticle = $request->has('article_id')
            ? Article::with('locations.translations')->find($request->article_id)
            : null;

        // جلب اللغات المدعومة ديناميكياً عبر حزمة mcamara
        $locales = [];
        if (class_exists(\Mcamara\LaravelLocalization\Facades\LaravelLocalization::class)) {
            foreach (\Mcamara\LaravelLocalization\Facades\LaravelLocalization::getSupportedLocales() as $localeCode => $properties) {
                $locales[$localeCode] = $properties['native'] ?? $properties['name'];
            }
        } else {
            $locales = ['ar' => 'العربية', 'en' => 'English'];
        }

        // تمرير المتغير $locales إلى الصفحة
        return view('admin.article_locations.index', compact('articles', 'selectedArticle', 'locales'));
    }

    public function store(Request $request, Article $article)
    {
        // 1. التحقق من صحة المدخلات دون فرض لغة إجبارية معينة
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'translations' => 'required|array',
            'translations.*.title' => 'nullable|string|max:255',
            'translations.*.description' => 'nullable|string',
        ]);

        /*
        // التأكد من أن المستخدم أدخل على الأقل عنواناً واحداً بأي لغة كانت
        $hasAtLeastOneTitle = collect($request->translations)->contains(function ($trans) {
            return !empty(trim($trans['title'] ?? ''));
        });

        if (!$hasAtLeastOneTitle) {
            return back()->withErrors(['translations' => 'يجب إدخال مسمى الموقع بلغة واحدة على الأقل.'])->withInput();
        }
        */

        // 2. إنشاء الموقع الأساسي (الإحداثيات)
        $location = $article->locations()->create([
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        // 3. حفظ الترجمات للغات التي تم تعبئتها فقط
        foreach ($request->translations as $locale => $transData) {
            if (!empty(trim($transData['title'] ?? ''))) {
                $location->translations()->create([
                    'locale' => $locale,
                    'title' => trim($transData['title']),
                    'description' => !empty($transData['description']) ? trim($transData['description']) : null,
                ]);
            }
        }

        return back()->with('success', 'تم حفظ الموقع وترجماته بنجاح.');
    }

    public function update(Request $request, ArticleLocation $location)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'translations' => 'nullable|array',
            'translations.*.title' => 'nullable|string|max:255',
            'translations.*.description' => 'nullable|string',
        ]);

        // 1. تحديث الإحداثيات
        $location->update([
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        // 2. تحديث أو إنشاء الترجمات المدخلة
        if ($request->has('translations') && is_array($request->translations)) {
            foreach ($request->translations as $locale => $transData) {
                $title = trim($transData['title'] ?? '');
                $description = !empty($transData['description']) ? trim($transData['description']) : null;

                if (!empty($title)) {
                    // تحديث الترجمة إن وجدت أو إنشاؤها
                    $location->translations()->updateOrCreate(
                        ['locale' => $locale],
                        ['title' => $title, 'description' => $description]
                    );
                } else {
                    // إذا مسح المستخدم العنوان بالكامل لهذه اللغة، نُحذف السجل الخاص بها
                    $location->translations()->where('locale', $locale)->delete();
                }
            }
        }

        return back()->with('success', 'تم تحديث بيانات الموقع والترجمات بنجاح.');
    }

    public function destroy(ArticleLocation $location)
    {
        $location->translations()->delete();
        $location->delete();

        return back()->with('success', 'تم حذف الموقع بنجاح.');
    }
}
