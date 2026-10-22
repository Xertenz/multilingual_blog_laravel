<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = $request->user();
        // === تحديث لغة المستخدم لتطابق اللغة الحالية للموقع عند تسجيل الدخول ===
        if ($user) {
            $user->update([
                'locale' => App::getLocale()
            ]);
        }

        if ($user->hasRole(['super-admin', 'editor', 'writer'])) {
            //return redirect()->intended(route('dashboard', absolute: false));
            return redirect()->intended(route('admin.articles.index', absolute: false));
        }

        return redirect()->intended(route('home.index'));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
