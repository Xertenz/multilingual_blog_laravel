<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\ArticleTagTranslation;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class ArticleTagTranslationController extends Controller
{
    /**
     * عرض صفحة الوسم للزوار (Frontend)
     * Route: /tags/{id}/{slug?}
     */
    public function show(int $tag_trans_id, ?string $slug = null)
    {
        // 1. جلب ترجمة الوسم مع الوسم الأساسي وعلاقاته
        $tag_trans = ArticleTagTranslation::with('tag')->find($tag_trans_id);

        if (!$tag_trans) {
            return to_route('home.index');
        }

        $currentLocale = LaravelLocalization::getCurrentLocale();

        // 2. التحقق من تطابق لغة الوسم مع لغة الموقع الحالية
        if ($tag_trans->locale !== $currentLocale) {
            $target_trans = $tag_trans->tag->translations
                ->where('locale', $currentLocale)
                ->first();

            if ($target_trans) {
                return redirect()->route('article-tags.show', [
                    'id'   => $target_trans->id,
                    'slug' => $target_trans->slug,
                ]);
            }

            return to_route('home.index')->with('info', __('This tag is not available in your language.'));
        }

        // 3. تصحيح الـ Slug في الـ URL لدعم SEO
        if (empty($slug) || $tag_trans->slug !== $slug) {
            return redirect()->route('article-tags.show', [
                'id'   => $tag_trans->id,
                'slug' => $tag_trans->slug,
            ]);
        }

        // 4. جلب المقالات المنشورة المرتبطة بهذا الوسم
        $articles = $tag_trans->tag->articles()
            ->with(['translations', 'media', 'author'])
            ->where('status', 'published')
            ->latest()
            ->paginate(12);

        return view('tag', compact('tag_trans', 'articles'));
    }
}
