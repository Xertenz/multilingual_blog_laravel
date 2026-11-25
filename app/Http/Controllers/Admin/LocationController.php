<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
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

        return view('admin.article_locations.index', compact('articles', 'selectedArticle', 'locales'));
    }

    public function store(Request $request, Article $article)
    {
        $request->validate([
            'latitude'               => 'required|numeric|between:-90,90',
            'longitude'              => 'required|numeric|between:-180,180',
            'translations'           => 'required|array',
            'translations.*.title'   => 'nullable|string|max:255',
            'translations.*.description' => 'nullable|string',
        ]);

        // إنشاء الموقع الأساسي (الإحداثيات) عبر العلاقة الـ polymorphic
        $location = $article->locations()->create([
            'latitude'  => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        // حفظ الترجمات للغات التي تم تعبئتها فقط
        foreach ($request->translations as $locale => $transData) {
            if (!empty(trim($transData['title'] ?? ''))) {
                $location->translations()->create([
                    'locale'      => $locale,
                    'title'       => trim($transData['title']),
                    'description' => !empty($transData['description']) ? trim($transData['description']) : null,
                ]);
            }
        }

        return back()->with('success', 'Location and translations saved successfully.');
    }

    public function update(Request $request, Location $location)
    {
        $request->validate([
            'latitude'               => 'required|numeric|between:-90,90',
            'longitude'              => 'required|numeric|between:-180,180',
            'translations'           => 'nullable|array',
            'translations.*.title'   => 'nullable|string|max:255',
            'translations.*.description' => 'nullable|string',
        ]);

        // 1. تحديث الإحداثيات
        $location->update([
            'latitude'  => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        // 2. تحديث أو إنشاء الترجمات المدخلة
        if ($request->has('translations') && is_array($request->translations)) {
            foreach ($request->translations as $locale => $transData) {
                $title = trim($transData['title'] ?? '');
                $description = !empty($transData['description']) ? trim($transData['description']) : null;

                if (!empty($title)) {
                    $location->translations()->updateOrCreate(
                        ['locale' => $locale],
                        ['title' => $title, 'description' => $description]
                    );
                } else {
                    $location->translations()->where('locale', $locale)->delete();
                }
            }
        }

        return back()->with('success', 'Location and translations updated successfully.');
    }

    public function destroy(Location $location)
    {
        $location->translations()->delete();
        $location->delete();

        return back()->with('success', 'Location deleted successfully.');
    }
}
