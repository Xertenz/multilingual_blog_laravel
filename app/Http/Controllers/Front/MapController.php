<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;

class MapController extends Controller
{
    public function index()
    {
        $currentLocale = app()->getLocale();

        // جلب المقالات المنشورة فقط والتي تحتوي على مواقع جغرافية، مع جلب الترجمة المناسبة للغة الحالية
        $articles = Article::with([
            'translations' => function ($q) use ($currentLocale) {
                $q->where('locale', $currentLocale);
            },
            'locations.translations' => function ($q) use ($currentLocale) {
                $q->where('locale', $currentLocale);
            }
        ])
            ->whereHas('locations')
            ->where('status', 'published') // افترضنا وجود حالة النشر published
            ->get();

        // إعادة تشكيل البيانات لسهولة استهلاكها في JavaScript
        $mapData = [];

        foreach ($articles as $article) {
            $articleTranslation = $article->translations->first();
            $articleTitle = $articleTranslation->title ?? $article->internal_title;

            foreach ($article->locations as $location) {
                $locationTranslation = $location->translations->first();

                $mapData[] = [
                    'id' => $article->id,
                    'article_title' => $articleTitle,
                    'location_title' => $locationTranslation->title ?? $articleTitle,
                    'description' => $locationTranslation->description ?? '',
                    'latitude' => (float) $location->latitude,
                    'longitude' => (float) $location->longitude,
                    'url' => route('articles.show', $article->id), // افترضنا اسم مسار عرض المقال
                ];
            }
        }

        return view('map', compact('mapData'));
    }
}
