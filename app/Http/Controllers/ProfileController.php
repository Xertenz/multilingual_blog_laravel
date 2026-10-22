<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */

    public function favoriteCategories(Request $request)
    {
        $currentLocale = LaravelLocalization::getCurrentLocale();

        // جلب المستخدم الحالي مع فئاته المفضلة وتراجمها باللغة الحالية لمنع N+1
        $user = $request->user()->load([
            'favoriteCategories.translations' => function ($query) use ($currentLocale) {
                $query->where('locale', $currentLocale);
            }
        ]);


        return view('profile.favorite-categories', [
            'favoriteCategories' => $user->favoriteCategories,
        ]);
    }

    public function savedArticles(Request $request)
    {
        $currentLocale = LaravelLocalization::getCurrentLocale();
        $user = $request->user()->load([
            'savedArticles.translations' => function ($query) use ($currentLocale) {
                $query->where('locale', $currentLocale);
            }
        ]);
        return view('profile.saved-articles', [
            'savedArticles' => $user->savedArticles,
        ]);
    }

    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
