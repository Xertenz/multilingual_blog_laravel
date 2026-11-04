<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthorController extends Controller
{
    public function toggleFollow(User $author)
    {

        $user = Auth::user();
        if ($user->id === $author->id) {
            return response()->json(['error' => 'You can\'t follow yourself.'], 422);
        }

        if ($user->followingAuthors()->where('author_id', $author->id)->exists()) {
            $user->followingAuthors()->detach($author->id);
            $isFollowing = false;
        } else {
            $user->followingAuthors()->attach($author->id);
            $isFollowing = true;
        }

        return response()->json([
            'is_following' => $isFollowing,
            'followers_count' => $author->authorFollowers()->count(),
        ]);
    }
}
