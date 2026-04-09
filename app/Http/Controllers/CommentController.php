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
        //return response()->json(['all' => $request->all()]);

        $request->validate([
            'article_id' => 'required|exists:articles,id',
            'user_id' => 'required|exists:users,id',
            'parent_id' => 'nullable|exists:comments,id',
            'comment_body' => 'required|string|max:1000',
            'locale' => Rule::in(LaravelLocalization::getSupportedLanguagesKeys())
        ]);

        if(Auth::id() !== (int) $request->user_id) {
            return response()->json(["error"=> 'Not allowed']);
            //return response()->json(["html"=> $request->all()]);
        }

        $comment_item = Comment::create([
            'user_id' => Auth::id(),
            'article_id' => $request->article_id,
            'parent_id' => $request->parent_id,
            'body' => trim(strip_tags($request->comment_body)),
            'locale' => $request->locale,
        ]);
        //return response()->json($request->all());

        if($request->ajax()) {
            if($request->parent_id == null) 
                $html = view('partials.comment_item', compact('comment_item'))->render();
            else
                $html = view('partials.comment_reply', ['reply' => $comment_item])->render();
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
            'body' => trim(strip_tags($request->body)),
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
