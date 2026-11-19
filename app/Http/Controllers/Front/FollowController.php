<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\ArticleCategory;
use App\Models\ArticleTag;
use App\Models\BookCategory;
use App\Models\BookTag;
use App\Models\Follow;
use App\Models\GameCategory;
use App\Models\GameTag;
use App\Models\MediaSummaryCategory;
use App\Models\MediaSummaryTag;
use App\Models\User;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    public function toggle(Request $request, string $type, int $id)
    {
        $map = [
            'author'                  => User::class,
            'article_category'        => ArticleCategory::class,
            'article_tag'             => ArticleTag::class,
            'book_category'           => BookCategory::class,
            'book_tag'                => BookTag::class,
            'game_category'           => GameCategory::class,
            'game_tag'                => GameTag::class,
            'media_summary_category'  => MediaSummaryCategory::class,
            'media_summary_tag'       => MediaSummaryTag::class,
        ];

        abort_unless(isset($map[$type]), 404);


        $modelClass = $map[$type];
        $target = $modelClass::query()->findOrFail($id);
        $user = $request->user();

        if ($modelClass === User::class && $user->id === $target->id) {
            return response()->json([
                'message' => __('You cannot follow yourself.'),
            ], 422);
        }

        $morphType = $target->getMorphClass();
        $morphId = $target->getKey();

        $existing = Follow::query()
            ->where('user_id', $user->id)
            ->where('followable_type', $morphType)
            ->where('followable_id', $morphId)
            ->first();

        if ($existing) {
            $existing->delete();
            $followed = false;
        } else {
            Follow::query()->create([
                'user_id'         => $user->id,
                'followable_type' => $morphType,
                'followable_id'   => $morphId,
            ]);
            $followed = true;
        }

        //return response()->json();

        return response()->json([
            'followed'        => $followed,
            'followers_count' => $target->followers()->count(),
        ]);
    }
}
