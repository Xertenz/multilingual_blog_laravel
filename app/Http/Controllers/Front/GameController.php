<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\GameTranslation;

class GameController extends Controller
{
    public function index()
    {
        $currentLocale = app()->getLocale();

        $games = GameTranslation::with([
            'game.category.translations',
            'game.tags.translations',
            'game.media',
            'game.translations',
        ])
            ->where('locale', $currentLocale)
            ->where('status', 'published')
            ->whereHas('game', function ($query) {
                $query->where('status', 'published');
            })
            ->latest()
            ->paginate(12);

        if ($games->isEmpty()) {
            $games = GameTranslation::with([
                'game.category.translations',
                'game.tags.translations',
                'game.media',
                'game.translations',
            ])
                ->where('status', 'published')
                ->whereHas('game', function ($query) {
                    $query->where('status', 'published');
                })
                ->latest()
                ->paginate(12);
        }

        return view('games.index', compact('games'));
    }

    public function show(int $id, ?string $slug = null)
    {
        $currentLocale = app()->getLocale();

        $game_trans = GameTranslation::with([
            'game.category.translations',
            'game.tags.translations',
            'game.media',
            'game.translations',
        ])->find($id);

        if (!$game_trans || !$game_trans->game) {
            abort(404);
        }

        // منع عرض الترجمة أو اللعبة إذا لم يكن كلاهما published
        $transStatus = $game_trans->status?->value ?? $game_trans->status;
        $gameStatus  = $game_trans->game->status?->value ?? $game_trans->game->status;

        if ($transStatus !== 'published' || $gameStatus !== 'published') {
            abort(404);
        }

        // التحويل إلى ترجمة نفس اللعبة باللغة الحالية إن وُجدت وكانت published
        if ($game_trans->locale !== $currentLocale) {
            $localizedTrans = $game_trans->game->translations
                ->where('locale', $currentLocale)
                ->first(function ($item) {
                    $status = $item->status?->value ?? $item->status;
                    return $status === 'published';
                });

            if ($localizedTrans) {
                return redirect()->route('games.show', [
                    'id'   => $localizedTrans->id,
                    'slug' => $localizedTrans->slug ?? 'game',
                ]);
            }
        }

        if (!empty($game_trans->slug) && $slug !== $game_trans->slug) {
            return redirect()->route('games.show', [
                'id'   => $game_trans->id,
                'slug' => $game_trans->slug,
            ]);
        }

        $game_trans->increment('views');
        $game_trans->game->increment('views');

        if (!empty($game_trans->content)) {
            $doc = new \DOMDocument();
            @$doc->loadHTML(
                mb_convert_encoding($game_trans->content, 'HTML-ENTITIES', 'UTF-8'),
                LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
            );

            $imgs = $doc->getElementsByTagName('img');
            foreach ($imgs as $img) {
                $content_img_id = $img->getAttribute('data-image-id');
                if ($content_img_id) {
                    $media = \Spatie\MediaLibrary\MediaCollections\Models\Media::find($content_img_id);
                    if ($media) {
                        $img->setAttribute('srcset', $media->getSrcset());
                        $img->setAttribute('sizes', '(max-width: 768px) 100vw, 1200px');
                    }
                }
            }

            $game_trans->content = $doc->saveHTML();
        }

        return view('games.show', compact('game_trans'));
    }
}
