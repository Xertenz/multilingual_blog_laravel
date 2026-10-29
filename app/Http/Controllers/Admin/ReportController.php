<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * عرض قائمة البلاغات الواردة في لوحة التحكم
     */
    public function index()
    {
        // جلب البلاغات مع معلومات المُبلغ والعنصر المبلغ عنه
        $reports = Report::with(['reporter', 'reportable'])
            ->latest()
            ->paginate(15);

        return view('admin.reports.index', compact('reports'));
    }

    /**
     * تحديث حالة البلاغ (محلول، مرفوض، قيد المراجعة)
     */
    public function updateStatus(Request $request, Report $report)
    {
        $request->validate([
            'status' => 'required|in:pending,resolved,dismissed',
        ]);

        $report->update([
            'status' => $request->status,
        ]);

        return back()->with('success', 'Report status updated successfully.');
    }

    /**
     * حذف العنصر المخالف بناءً على البلاغ (مثل حذف التعليق أو القضية)
     */
    public function destroyContent(Report $report)
    {
        // التحقق من وجود العنصر المبلغ عنه ثم حذفه
        if ($report->reportable) {
            $report->reportable->delete();
        }

        $report->update(['status' => 'resolved']);

        return back()->with('success', 'Reported content has been deleted and report resolved.');
    }
}
