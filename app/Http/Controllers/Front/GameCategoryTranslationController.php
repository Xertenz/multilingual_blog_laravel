<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\GameCategoryTranslation;
use App\Models\GameTranslation;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class GameCategoryTranslationController extends Controller
{
    public function show(int $id, ?string $slug = null)
    {
        $category_trans = GameCategoryTranslation::with([
            'category' => function ($query) {
                $query->where('is_active', true);
            },
            'category.translations',
        ])->find($id);

        if (!$category_trans || !$category_trans->category) {
            return to_route('games.index');
        }

        $currentLocale = LaravelLocalization::getCurrentLocale();

        // التحويل إلى ترجمة الفئة نفسها باللغة الحالية إن وُجدت
        if ($category_trans->locale !== $currentLocale) {
            $targetTrans = $category_trans->category->translations
                ? $category_trans->category->translations->firstWhere('locale', $currentLocale)
                : null;

            if ($targetTrans) {
                return redirect()->route('game-categories.show', [
                    'id'   => $targetTrans->id,
                    'slug' => $targetTrans->slug,
                ]);
            }
        }

        // تصحيح الـ slug في الرابط لدعم SEO
        if (!empty($category_trans->slug) && $slug !== $category_trans->slug) {
            return redirect()->route('game-categories.show', [
                'id'   => $category_trans->id,
                'slug' => $category_trans->slug,
            ]);
        }

        $categoryId = $category_trans->game_category_id ?? $category_trans->category->id;

        // 1. جلب ملخصات الألعاب المنشورة (الترجمة published + اللعبة الأساسية published) للغة الحالية
        $games = GameTranslation::with([
            'game.category.translations',
            'game.tags.translations',
            'game.media',
            'game.translations',
        ])
            ->where('locale', $currentLocale)
            ->where('status', 'published')
            ->whereHas('game', function ($query) use ($categoryId) {
                $query->where('status', 'published')
                    ->where('game_category_id', $categoryId);
            })
            ->latest()
            ->paginate(12);

        // 2. بديل في حال عدم توفر ترجمات منشورة للغة الحالية (مع بقاء شرط published على الطرفين)
        if ($games->isEmpty()) {
            $games = GameTranslation::with([
                'game.category.translations',
                'game.tags.translations',
                'game.media',
                'game.translations',
            ])
                ->where('status', 'published')
                ->whereHas('game', function ($query) use ($categoryId) {
                    $query->where('status', 'published')
                        ->where('game_category_id', $categoryId);
                })
                ->latest()
                ->paginate(12);
        }

        return view('games.category', compact('category_trans', 'games'));
    }
}
