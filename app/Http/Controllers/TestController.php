<?php

namespace App\Http\Controllers;

use App\Models\Test;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TestController extends Controller
{
    public function upload(Request $request) {
        $test = new Test();
        $test->title = $request->title;
        $test->save();

        $file = $request->file('image');
        $file_ext = $file->getClientOriginalExtension();

        $test
            ->addMediaFromRequest('image')
            //->withResponsiveImages()
            ->usingFileName(Str::random(32).'.'.$file_ext)
            ->toMediaCollection("content_imgs");
    }

    public function showimgs() {
        $img = Test::findOrFail(29)->getFirstMedia("content_imgs");
        //dd($img->getUrl('content_480w'));
        dd($img->getUrl());
       
        /*
        $img = Test::findOrFail(24)->getFirstMediaUrl("content_imgs", "content_desktop_medium");
        dd($img);
        */
        
        return view('showimgs', compact('img'));
    }
}
