<?php

namespace App\Http\Controllers;

use App\Enums\ReportReason;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Enum;

class UserReportController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'reportable_type' => 'required|string',
            'reportable_id'   => 'required|integer',
            //'reason_type'     => 'required|string|max:100',
            'reason_type' => ['required', new Enum(ReportReason::class)],
            'details'         => 'nullable|string|max:500',
        ]);

        // تنظيف وتصحيح صيغة الـ morph type لضمان وجود الشرطات المائلة العكسية بشكل صحيح
        $reportableType = $request->reportable_type;

        // إذا كانت مكتوبة بدون شرطات (مثل AppModelsComment)، نقوم بإصلاحها
        if (!str_contains($reportableType, '\\')) {
            // تحويلها إلى الصيغة القياسية مع الشرطات
            // مثلاً تحويل AppModelsComment إلى App\Models\Comment
            $reportableType = str_replace(['AppModels', 'AppModelsCommunityCase'], ['App\\Models\\', 'App\\Models\\CommunityCase'], $reportableType);
        }

        // طريقة أكثر شمولاً ودقة لتصحيح أي Namespace مفقود:
        if ($reportableType === 'AppModelsComment') {
            $reportableType = \App\Models\Comment::class;
        } elseif ($reportableType === 'AppModelsCommunityCase') {
            $reportableType = \App\Models\CommunityCase::class;
        }

        // التحقق من عدم التكرار
        $exists = Report::where('reporter_id', Auth::id())
            ->where('reportable_type', $reportableType)
            ->where('reportable_id', $request->reportable_id)
            ->where('status', 'pending')
            ->exists();

        if ($exists) {
            return back()->with('error', 'لقد قمت بالإبلاغ عن هذا العنصر مسبقاً وهو قيد المراجعة.');
        }

        Report::create([
            'reporter_id'     => Auth::id(),
            'reportable_type' => $reportableType, // القيمة الصحيحة والمصححة
            'reportable_id'   => $request->reportable_id,
            'reason_type'     => $request->reason_type,
            'details'         => $request->details,
            'status'          => 'pending',
        ]);

        return back()->with('success', 'تم إرسال البلاغ بنجاح، شكراً لمساعدتنا في الحفاظ على أمان المنصة.');
    }
}
