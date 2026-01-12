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
            ->usingFileName(Str::random(32).'.'.$file_ext)
            ->toMediaCollection("content_imgs");
    }
}
