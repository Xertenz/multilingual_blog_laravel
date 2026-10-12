<section>

    <header>
        <h2 class="text-lg font-bold text-white">
            {{ __('Update Password') }}
        </h2>

        <p class="mt-1 text-xs text-slate-400">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" :value="__('Current Password')" class="text-xs font-semibold text-slate-300 mb-1" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full bg-slate-950 border-slate-800 text-slate-200 placeholder-slate-500 focus:border-red-600 focus:ring-red-600/50 rounded-lg text-sm" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2 text-rose-400 text-xs" />
        </div>

        <div>
            <x-input-label for="update_password_password" :value="__('New Password')" class="text-xs font-semibold text-slate-300 mb-1" />
            <x-text-input id="update_password_password" name="password" type="password" class="mt-1 block w-full bg-slate-950 border-slate-800 text-slate-200 placeholder-slate-500 focus:border-red-600 focus:ring-red-600/50 rounded-lg text-sm" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2 text-rose-400 text-xs" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')" class="text-xs font-semibold text-slate-300 mb-1" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full bg-slate-950 border-slate-800 text-slate-200 placeholder-slate-500 focus:border-red-600 focus:ring-red-600/50 rounded-lg text-sm" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2 text-rose-400 text-xs" />
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold text-xs px-4 py-2 rounded-lg transition duration-200 shadow-sm shadow-red-600/20">
                {{ __('Save') }}
            </button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-xs text-emerald-400 font-medium"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>

</section>