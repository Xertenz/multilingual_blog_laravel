<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckBanned
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $penalty = Auth::user()->activePenalty;

            if ($penalty) {
                // إذا كان ينتمي للدرجات التقييدية (2 إلى 5)
                if ($penalty->level > 1) {
                    $message = match ($penalty->level) {
                        2, 3, 4 => "حسابك مقيد مؤقتاً حتى {$penalty->expires_at->format('Y-m-d H:i')}. السبب: {$penalty->reason}",
                        5       => "تم حظر حسابك نهائياً بسب: {$penalty->reason}",
                        default => "حسابك مقيد حالياً.",
                    };

                    if ($request->expectsJson()) {
                        return response()->json(['message' => $message], 403);
                    }

                    return redirect()->route('home.index')->with('error', $message);
                }
            }
        }

        return $next($request);
    }
}
