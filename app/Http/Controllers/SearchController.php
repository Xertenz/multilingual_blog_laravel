<?php

// app/Http/Controllers/SearchController.php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __invoke(Request $request)
    {
        $query = trim($request->input('q'));

        // جلب المقالات المكتملة والمطبقة للبحث مع العلاقات المطلوبة
        $articles = Article::with([
            'translations' => function ($q) {
                $q->where('locale', app()->getLocale());
            },
            'media',
            'category.translations'
        ])
            ->where('status', 'published')
            ->search($query)
            ->latest()
            ->paginate(12)
            ->withQueryString(); // 👈 يضمن بقاء قيمة ?q= في روابط التنقل بين الصفحات (Pagination)

        return view('search', compact('articles', 'query'));
    }
}
