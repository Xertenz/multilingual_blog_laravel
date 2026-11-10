<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\GameTranslation;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function index()
    {
        $games = Game::with(['translations', 'gameCategory.translations', 'media'])
            ->latest()
            ->paginate(12);

        return view('games.index', compact('games'));
    }

    /*
    public function show(int $id, ?string $slug = null)
    {
        $currentLocale = app()->getLocale();

        $game_trans = GameTranslation::with([
            'game.gameCategory.translations',
            'game.gameTags.translations',
            'game.media'
        ])->where('game_id', $id)->where('locale', $currentLocale)->first();

        if (!$game_trans) {
            $game_trans = GameTranslation::with(['game'])->where('game_id', $id)->first();
            if (!$game_trans) {
                abort(404);
            }
        }

        // زيادة العداد للترجمة الحالية وللعبة الأساسية معاً
        $game_trans->increment('views');
        $game_trans->game->increment('views');

        return view('games.show', compact('game_trans'));
    }
    */

    public function show(int $id, ?string $slug = null)
    {
        $currentLocale = app()->getLocale();

        $game_trans = GameTranslation::with([
            'game.gameCategory.translations',
            'game.gameTags.translations',
            'game.media' // لجلب الوسائط المرتبطة باللعبة
        ])->where('game_id', $id)->where('locale', $currentLocale)->first();

        if (!$game_trans) {
            $game_trans = GameTranslation::with(['game'])->where('game_id', $id)->first();
            if (!$game_trans) {
                abort(404);
            }
        }

        // زيادة العداد للترجمة الحالية وللعبة الأساسية معاً
        $game_trans->increment('views');
        $game_trans->game->increment('views');

        // 🖼️ معالجة صور المحتوى الداخلي عبر DOMDocument وإضافة srcset
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

        return view('games.show', compact('game_trans'));
    }
}
