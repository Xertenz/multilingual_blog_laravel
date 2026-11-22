<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommunityCase;
use App\Models\CommunityCaseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class CommunityCaseController extends Controller
{
    public function index(Request $request)
    {
        $query = CommunityCase::with(['author', 'category.translations', 'tags.translations'])
            ->latest();

        // فلترة حسب الحالة (اختياري)
        if ($request->filled('status')) {
            $query->where('moderation_status', $request->status);
        }

        // فلترة حسب الفئة (اختياري)
        if ($request->filled('category')) {
            $query->where('community_case_category_id', $request->category);
        }

        // بحث بالعنوان (اختياري)
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $cases = $query->paginate(15);

        $categories = CommunityCaseCategory::with('translations')->get();
        $supportedLocales = LaravelLocalization::getSupportedLocales();

        return view('admin.community_cases.index', compact('cases', 'categories', 'supportedLocales'));
    }

    public function approve(Request $request, int $id)
    {
        /*
        if (!$request->user()->hasRole(['super-admin', 'editor'])) {
            abort(403);
        }
        */

        $case = CommunityCase::findOrFail($id);
        $case->update(['moderation_status' => 'approved']);

        return back()->with('success', 'Case approved successfully.');
    }

    public function reject(Request $request, int $id)
    {
        /*
        if (!$request->user()->hasRole(['super-admin', 'editor'])) {
            abort(403);
        }
        */

        $case = CommunityCase::findOrFail($id);
        $case->update(['moderation_status' => 'rejected']);

        return back()->with('success', 'Case rejected successfully.');
    }

    public function destroy(Request $request, int $id)
    {
        /*
        if (!$request->user()->hasRole(['super-admin', 'editor'])) {
            abort(403);
        }
        */

        $case = CommunityCase::with('locations.translations')->findOrFail($id);

        DB::transaction(function () use ($case) {
            foreach ($case->locations as $location) {
                $location->translations()->delete();
                $location->delete();
            }

            $case->votes()->delete();
            $case->delete();
        });

        return back()->with('success', 'Case deleted successfully.');
    }
}
