<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReactionController extends Controller
{
    //
    public function toggleArticleReaction(Request $request)
    {
        $request->validate([
            'id' => 'required|integer', // article id
            'type' => 'required|in:like,dislike'
        ]);

        $target = Article::findOrFail($request->id); // article

        // البحث عن تفاعل سابق لهذا المستخدم على هذا الشيء
        $existing = $target->reactions()->where('user_id', Auth::id())->first();

        if ($existing) {
            if ($existing->type === $request->type) {
                $existing->delete(); // إذا ضغط نفس الزر مرتين يُحذف التفاعل
                return response()->json(
                    [
                        'status' => 'removed',
                        'likes' => $target->reactions()->where('type', 'like')->count(),
                        'dislikes' => $target->reactions()->where('type', 'dislike')->count()
                    ]
                );
            }
            $existing->update(['type' => $request->type]); // تغيير نوع التفاعل (مثلاً من أعجبني إلى أحزنني)
            return response()->json(
                [
                    'status' => 'updated',
                    'likes' => $target->reactions()->where('type', 'like')->count(),
                    'dislikes' => $target->reactions()->where('type', 'dislike')->count()
                ]
            );
        }

        $target->reactions()->create([
            'user_id' => Auth::id(),
            'type' => $request->type
        ]);

        return response()->json(
            [
                'status' => 'added',
                'likes' => $target->reactions()->where('type', 'like')->count(),
                'dislikes' => $target->reactions()->where('type', 'dislike')->count()
            ]
        );
    }
}
