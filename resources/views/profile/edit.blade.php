<x-app-layout>
    <div class="py-8 bg-[#0a0f1f] min-h-screen text-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            {{-- شريط التنقل الخاص بالملف الشخصي --}}
            @include('profile.partials.profile-menu')

            {{-- 1. بطاقة تحديث المعلومات الشخصية --}}
            <div class="p-6 sm:p-8 bg-slate-900/90 backdrop-blur-md shadow-xl rounded-2xl border border-slate-800">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            {{-- 2. بطاقة تغيير كلمة المرور --}}
            <div class="p-6 sm:p-8 bg-slate-900/90 backdrop-blur-md shadow-xl rounded-2xl border border-slate-800">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            {{-- 3. بطاقة حذف الحساب --}}
            <div class="p-6 sm:p-8 bg-slate-900/90 backdrop-blur-md shadow-xl rounded-2xl border border-slate-800">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>