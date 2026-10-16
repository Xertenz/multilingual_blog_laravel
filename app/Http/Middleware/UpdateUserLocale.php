<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Symfony\Component\HttpFoundation\Response;

class UpdateUserLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $currentLocale = LaravelLocalization::getCurrentLocale();
            //$user = Auth::user();
            $user_id = Auth::id();
            $user  = User::find($user_id);

            if ($user->locale !== $currentLocale) {
                $user->update(['locale' => $currentLocale]);
            }
        }

        return $next($request);
    }
}
