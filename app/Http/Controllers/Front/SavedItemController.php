<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Book;
use App\Models\Game;
use App\Models\MediaSummary;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SavedItemController extends Controller
{
    private const MAP = [
        'article'       => Article::class,
        'book'          => Book::class,
        'game'          => Game::class,
        'media_summary' => MediaSummary::class,
    ];

    public function toggle(Request $request, string $type, int $id): JsonResponse
    {
        $modelClass = self::MAP[$type] ?? null;

        if (!$modelClass) {
            return response()->json([
                'message' => __('Invalid savable type.'),
            ], 422);
        }

        $model = $modelClass::find($id);

        if (!$model) {
            return response()->json([
                'message' => __('Item not found.'),
            ], 404);
        }

        $user = $request->user();

        $alreadySaved = $model->savedBy()->where('users.id', $user->id)->exists();

        if ($alreadySaved) {
            $model->savedBy()->detach($user->id);
            $saved = false;
        } else {
            $model->savedBy()->attach($user->id);
            $saved = true;
        }

        $savesCount = $model->savedBy()->count();

        return response()->json([
            'saved'       => $saved,
            'is_saved'    => $saved,
            'saves_count' => $savesCount,
        ]);
    }
}
