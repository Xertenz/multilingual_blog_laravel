<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MediaSummaryCategory;

class MediaSummaryCategoryController extends Controller
{
    public function index()
    {
        $cats = MediaSummaryCategory::with(['media', 'mediaSummaries'])
            ->withCount('mediaSummaries')
            ->latest()
            ->paginate(10);

        return view('admin.media_summary_categories.index', compact('cats'));
    }
}
