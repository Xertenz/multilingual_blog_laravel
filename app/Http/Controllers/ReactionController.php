<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ReactionController extends Controller
{
    public function toggleArticleReaction(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'type' => 'required|in:like,dislike'
        ]);

        $target = Article::findOrFail($request->id);
        $userId = Auth::id();

        $existing = $target->reactions()->where('user_id', $userId)->first();

        if ($existing) {
            if ($existing->type === $request->type) {
                $existing->delete();
                $status = 'removed';
            } else {
                $existing->update(['type' => $request->type]);
                $status = 'updated';
            }
        } else {
            $target->reactions()->create([
                'user_id' => $userId,
                'type' => $request->type
            ]);
            $status = 'added';
        }

        // 🔥 مسح كاش جميع ترجمات هذا المقال بضمان تام
        foreach ($target->translations as $translation) {
            //Cache::forget("article_show_{$translation->id}_lang_{$translation->locale}");
        }

        return response()->json([
            'status'   => $status,
            'likes'    => $target->reactions()->where('type', 'like')->count(),
            'dislikes' => $target->reactions()->where('type', 'dislike')->count(),
        ]);
    }

    public function toggleCommentReaction(Request $request)
    {
        $request->validate([
            'id' => 'required|integer', // comment id
            'type' => 'required|in:like,dislike,shocked,confused,sad,angry,mind_blown'
        ]);

        $comment = Comment::findOrFail($request->id);
        $userId = Auth::id();

        $existing = $comment->reactions()->where('user_id', $userId)->first();
        $reaction = null;
        $status = '';

        if ($existing) {
            if ($existing->type === $request->type) {
                $existing->delete();
                $status = 'removed';
            } else {
                $existing->update(['type' => $request->type]);
                $reaction = $existing; // التفاعل بعد التحديث
                $status = 'updated';
            }
        } else {
            $reaction = $comment->reactions()->create([
                'user_id' => $userId,
                'type' => $request->type
            ]);
            $status = 'added';
        }

        // 🔔 إرسال إشعار لصاحب التعليق إذا لم يكن هو نفس المستخدم المتفاعل، وتمت الإضافة أو التحديث
        if ($status !== 'removed' && $reaction && $comment->user_id !== $userId) {
            $comment->user->notify(new \App\Notifications\NewCommentReactionNotification($reaction));
        }

        // إرجاع أعداد التفاعلات المحدثة لكل نوع لهذا التعليق
        $reactionsCounts = $comment->reactions()
            ->selectRaw('type, count(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type');

        return response()->json([
            'status' => $status,
            'counts' => $reactionsCounts, // مثال: ['like' => 3, 'shocked' => 1]
        ]);
    }
}
