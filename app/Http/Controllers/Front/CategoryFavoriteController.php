<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryFavoriteController extends Controller
{
    public function toggle(Category $category)
    {
        if (Auth::check()) {
            $user = User::findOrFail(Auth::id());

            // عملية التبديل ترجع مصفوفة للتأكد من حالة الإضافة أو الحذف
            $result = $user->favoriteCategories()->toggle($category->id);
            $isFav = count($result['attached']) > 0;

            return response()->json([
                'success' => true,
                'is_favorite' => $isFav,
                'message' => $isFav ? __('Added to favorites successfully!') : __('Removed from favorites successfully!')
            ]);
        }

        return response()->json(['error' => __('You must be logged in.')], 401);
    }
}
