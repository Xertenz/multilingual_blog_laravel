<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\GameTagTranslation;
use App\Models\GameTranslation;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class GameTagTranslationController extends Controller
{
    public function show(int $id, ?string $slug = null)
    {
        $tag_trans = GameTagTranslation::with([
            'tag' => function ($query) {
                $query->where('is_active', true);
            }
        ])->find($id);

        if (!$tag_trans || !$tag_trans->tag) {
            return to_route('games.index');
        }

        $currentLocale = LaravelLocalization::getCurrentLocale();

        // التحويل إلى ترجمة الوسم نفسها باللغة الحالية إن وُجدت
        if ($tag_trans->locale !== $currentLocale) {
            $targetTrans = $tag_trans->tag->translations
                ? $tag_trans->tag->translations->firstWhere('locale', $currentLocale)
                : null;

            if ($targetTrans) {
                return redirect()->route('game-tags.show', [
                    'id'   => $targetTrans->id,
                    'slug' => $targetTrans->slug,
                ]);
            }
        }

        // تصحيح الـ slug في الرابط لدعم SEO
        if (!empty($tag_trans->slug) && $slug !== $tag_trans->slug) {
            return redirect()->route('game-tags.show', [
                'id'   => $tag_trans->id,
                'slug' => $tag_trans->slug,
            ]);
        }

        $tagId = $tag_trans->game_tag_id ?? $tag_trans->tag->id;

        // 1. جلب ملخصات الألعاب المنشورة (الترجمة published + اللعبة الأساسية published) للغة الحالية
        $games = GameTranslation::with([
            'game.category.translations',
            'game.tags.translations',
            'game.media',
            'game.translations',
        ])
            ->where('locale', $currentLocale)
            ->where('status', 'published')
            ->whereHas('game', function ($query) use ($tagId) {
                $query->where('status', 'published')
                    ->whereHas('tags', function ($q) use ($tagId) {
                        $q->where('game_tags.id', $tagId);
                    });
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
                ->whereHas('game', function ($query) use ($tagId) {
                    $query->where('status', 'published')
                        ->whereHas('tags', function ($q) use ($tagId) {
                            $q->where('game_tags.id', $tagId);
                        });
                })
                ->latest()
                ->paginate(12);
        }

        return view('games.tag', compact('tag_trans', 'games'));
    }
}
