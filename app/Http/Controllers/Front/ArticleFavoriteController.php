<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArticleFavoriteController extends Controller
{
    //
    public function toggle(Article $article)
    {
        if (Auth::check()) {
            $user = User::findOrFail(Auth::id()); // يمكنك استخدام auth()->user() مباشرة دون الحاجة لـ findOrFail

            // عملية الـ toggle تُرجع مصفوفة تحتوي على العناصر التي أُضيفت (attached) أو أُزيلت (detached)
            $result = $user->favoriteArticles()->toggle($article->id);

            // إذا كانت مصفوفة attached ليست فارغة، فهذا يعني أن المقالة أُضيفت للمفضلة للتو
            $isSaved = count($result['attached']) > 0;

            return response()->json([
                'success' => true,
                'is_saved' => $isSaved,
                'message' => $isSaved ? __('Saved to favorites') : __('Removed from favorites')
            ]);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
