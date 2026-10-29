<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class SocialController extends Controller
{
    /**
     * توجيه المستخدم إلى صفحة تسجيل الدخول الخاصة بـ Google أو Facebook
     */
    public function redirect(string $provider)
    {
        // التحقق من أن المزود مدعوم (google أو facebook)
        if (!in_array($provider, ['google', 'facebook'])) {
            return redirect()->route('login')->withErrors(['msg' => 'Unsupported login provider.']);
        }

        return Socialite::driver($provider)->redirect();
    }

    /**
     * استقبال البيانات من المنصة بعد موافقة المستخدم
     */
    public function callback(string $provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();

            // البحث عما إذا كان المستخدم مسجلاً مسبقاً بنفس الـ Provider والـ Provider ID
            $user = User::where('provider', $provider)
                ->where('provider_id', $socialUser->getId())
                ->first();

            if (!$user) {
                // البحث عما إذا كان الإيميل موجوداً مسبقاً في النظام بحساب عادي
                $user = User::where('email', $socialUser->getEmail())->first();

                if ($user) {
                    // ربط الحساب الحالي بمزود الخدمة الاجتماعي
                    $user->update([
                        'provider' => $provider,
                        'provider_id' => $socialUser->getId(),
                        'provider_avatar' => $socialUser->getAvatar(),
                    ]);
                } else {
                    // إنشاء حساب جديد كلياً للمستخدم
                    $user = User::create([
                        'name' => $socialUser->getName() ?? $socialUser->getNickname(),
                        'email' => $socialUser->getEmail(),
                        'password' => null, // بدون كلمة مرور
                        'provider' => $provider,
                        'provider_id' => $socialUser->getId(),
                        'provider_avatar' => $socialUser->getAvatar(),
                        'email_verified_at' => now(), // اعتباره مفعلاً لأن الإيميل موثق من جوجل/فيسبوك
                    ]);
                }
            }

            // تسجيل دخول المستخدم في النظام
            Auth::login($user, true);

            // التوجيه إلى الصفحة الرئيسية لوحة التحكم أو الموقع
            //return redirect()->intended('/dashboard');
            return redirect()->intended('/');
        } catch (Exception $e) {
            return redirect()->route('login')->withErrors(['msg' => 'Something went wrong during ' . ucfirst($provider) . ' login. Please try again.']);
        }
    }
}
