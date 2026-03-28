<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Mews\Purifier\Facades\Purifier;

class CommentController extends Controller
{
    //

    public function store(Request $request) {
        //return response()->json(['articleId' => $article_id]);
        $request->validate([
            'article_id' => 'required|exists:articles,id',
            'user_id' => 'required|exists:users,id',
            'comment_body' => 'required|string|min:3,max:1000',
            'locale' => Rule::in(LaravelLocalization::getSupportedLanguagesKeys())
        ]);

        if(Auth::id() !== (int) $request->user_id) {
            return response()->json(["html"=> 'This comment is not yours']);
        }

        $comment_item = Comment::create([
            'user_id' => Auth::id(),
            'article_id' => $request->article_id,
            'body' => $request->comment_body,
            'locale' => $request->locale,
        ]);
        //return response()->json($request->all());

        if($request->ajax()) {
            $html = view('partials.comment_item', compact('comment_item'))->render();
            return response()->json(['html' => $html]);
        }
        return back();
    }

    public function update(Request $request) {
        //return response()->json($request->all());


        $request->validate([
            'id' => "required|exists:comments,id",
            'body' => 'required|string|max:1000',
        ]);

        $comment_item = Comment::find($request->id);
        if(Auth::id() !== $comment_item->user_id) {
            return response()->json(['error' => 'This comment is not yours']);
        }

        $comment_item->update([
            'body' => strip_tags($request->body),
        ]);

        return response()->json([
            'message' => 'Comment Updated Successfully',
            'new_body' => $comment_item->body
        ]);

    }

    public function delete(Request $request) {
        $request->validate([
            'id' => "required|exists:comments,id",
        ]);
        
        $comment_item = Comment::find($request->id);

        if(Auth::id() !== $comment_item->user_id) {
            return response()->json(['error' => 'This comment is not yours']);
        }

        $comment_item->delete();

        return response()->json([
            'message' => 'Comment Deleted Successfully',
        ]);
    }
}
