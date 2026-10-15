<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Series;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SeriesController extends Controller
{
    public function show(int $id, ?string $slug = null)
    {
        $currentLocale = App::getLocale();

        // جلب السلسلة مع ترجماتها ومقالاتها المترتبة
        $series = Series::with([
            'translations',
            'user',
            'articles' => function ($query) use ($currentLocale) {
                // جلب تراجم المقالات للغة الحالية إن أمكن
                $query->with(['translations' => function ($q) use ($currentLocale) {
                    $q->where('language_code', $currentLocale);
                }]);
            }
        ])->where('status', 'published')->findOrFail($id);

        // جلب ترجمة السلسلة الخاصة باللغة الحالية
        $translation = $series->translations->where('language_code', $currentLocale)->first()
            ?? $series->translations->first();

        return view('front.series.show', compact('series', 'translation'));
    }
}
