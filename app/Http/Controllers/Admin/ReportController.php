<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ReportStatus;
use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReportController extends Controller
{
    /**
     * Display incoming reports in the admin dashboard.
     */
    public function index()
    {
        $reports = Report::with(['reporter', 'reportable'])
            ->latest()
            ->paginate(15);

        return view('admin.reports.index', compact('reports'));
    }

    /**
     * Update the status of a report (pending, resolved, dismissed).
     */
    public function updateStatus(Request $request, Report $report)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::enum(ReportStatus::class)],
        ]);

        $report->update([
            'status' => $validated['status'],
        ]);

        return back()->with('success', 'Report status updated successfully.');
    }

    /**
     * Delete the reported content and mark the report(s) as resolved.
     */
    public function destroyContent(Report $report)
    {
        $target = $report->reportable;

        if ($target) {
            Report::where('reportable_type', $report->reportable_type)
                ->where('reportable_id', $report->reportable_id)
                ->where('status', ReportStatus::PENDING->value)
                ->update([
                    'status' => ReportStatus::RESOLVED->value,
                ]);

            $target->delete();
        }

        $report->update([
            'status' => ReportStatus::RESOLVED->value,
        ]);

        return back()->with('success', 'Reported content has been deleted and report resolved.');
    }
}
