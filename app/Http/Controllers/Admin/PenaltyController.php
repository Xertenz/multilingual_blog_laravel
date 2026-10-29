<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Penalty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PenaltyController extends Controller
{
    /**
     * تطبيق عقوبة بدرجاتها الـ 5 على مستخدم
     */
    public function store(Request $request, User $user)
    {
        $request->validate([
            'level'  => 'required|integer|between:1,5',
            'reason' => 'required|string|max:1000',
        ]);

        // إلغاء تفعيل أي عقوبة سابقة للبدء بالعقوبة الجديدة
        $user->penalties()->where('is_active', true)->update(['is_active' => false]);

        $expiresAt = match ((int) $request->level) {
            1 => null,                       // تنبيه ودي فقط (بدون حظر زمن)
            2 => Carbon::now()->addHours(24), // حظر 24 ساعة
            3 => Carbon::now()->addDays(7),  // حظر أسبوع
            4 => Carbon::now()->addDays(30), // حظر شهر
            5 => null,                       // حظر نهائي دائم
            default => null,
        };

        Penalty::create([
            'user_id'    => $user->id,
            'admin_id'   => Auth::id(),
            'level'      => $request->level,
            'reason'     => $request->reason,
            'expires_at' => $expiresAt,
            'is_active'  => true,
        ]);

        return back()->with('success', "Penalty level {$request->level} applied successfully.");
    }

    /**
     * إلغاء العقوبة/رفع الحظر عن المستخدم
     */
    public function revoke(Penalty $penalty)
    {
        $penalty->update(['is_active' => false]);
        return back()->with('success', 'Penalty has been revoked.');
    }
}
