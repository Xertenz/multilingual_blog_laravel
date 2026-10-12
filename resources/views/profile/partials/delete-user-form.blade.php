<section class="space-y-6">

    <header>
        <h2 class="text-lg font-bold text-white">
            {{ __('Delete Account') }}
        </h2>

        <p class="mt-1 text-xs text-slate-400">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    <button
        type="button"
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="bg-rose-600 hover:bg-rose-700 text-white font-semibold text-xs px-4 py-2 rounded-lg transition duration-200 shadow-sm shadow-rose-600/20"
    >
        {{ __('Delete Account') }}
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 bg-[#0f1526] border border-slate-800 rounded-xl">
            @csrf
            @method('delete')

            <h2 class="text-lg font-bold text-white">
                {{ __('Are you sure you want to delete your account?') }}
            </h2>

            <p class="mt-1 text-xs text-slate-400">
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-3/4 bg-slate-950 border-slate-800 text-slate-200 placeholder-slate-500 focus:border-red-600 focus:ring-red-600/50 rounded-lg text-sm"
                    placeholder="{{ __('Password') }}"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2 text-rose-400 text-xs" />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button 
                    type="button" 
                    x-on:click="$dispatch('close')" 
                    class="bg-slate-900 border border-slate-800 text-slate-300 hover:bg-slate-800 text-xs font-semibold px-4 py-2 rounded-lg transition duration-150"
                >
                    {{ __('Cancel') }}
                </button>

                <button 
                    type="submit" 
                    class="bg-rose-600 hover:bg-rose-700 text-white font-semibold text-xs px-4 py-2 rounded-lg transition duration-150"
                >
                    {{ __('Delete Account') }}
                </button>
            </div>
        </form>
    </x-modal>
</section>