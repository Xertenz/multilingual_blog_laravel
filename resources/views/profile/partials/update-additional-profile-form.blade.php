@php
    /** @var \App\Models\User $user */
    $user = $user ?? auth()->user();
    /** @var \App\Models\UserProfile $profile */
    $profile = $profile ?? $user->profile()->firstOrCreate(['user_id' => $user->id]);
    $socialLinks = is_array($profile->social_links ?? null) ? $profile->social_links : [];
@endphp

<section>
    <header>
        <div class="flex flex-wrap items-center justify-between gap-2">
            <div>
                <h2 class="text-lg font-bold text-white">
                    {{ __('Additional Profile Information') }}
                </h2>
                <p class="mt-1 text-xs text-slate-400">
                    {{ __('Add your bio, location, website, and social links to enrich your public profile.') }}
                </p>
            </div>

            {{-- شارة الرتبة والنقاط (للعرض فقط) --}}
            <div class="flex items-center gap-2 text-xs">
                <span class="px-2.5 py-1 rounded-lg bg-red-950/60 text-red-300 border border-red-800/50 font-semibold">
                    🏅 {{ $user->rank }}
                </span>
                <span class="px-2.5 py-1 rounded-lg bg-slate-800/80 text-slate-300 border border-slate-700/70 font-mono">
                    ⭐ {{ $user->reputation ?? 0 }}
                </span>
                <span class="px-2.5 py-1 rounded-lg bg-slate-800/80 text-slate-300 border border-slate-700/70 font-mono">
                    🕵️ {{ $profile->solved_cases_count ?? 0 }}
                </span>
            </div>
        </div>
    </header>

    {{-- ملاحظة: في edit.blade.php يفضّل أن يكون الفورم شاملاً أو مستقلاً؛ إذا كان لكل بطاقة فورم مستقل فاستخدم form action="{{ route('profile.update') }}" method="POST" مع @csrf و @method('patch') --}}
    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-5">
        @csrf
        @method('patch')

        {{-- نبذة تعريفية (Bio) --}}
        <div>
            <label for="bio" class="block text-xs font-semibold text-slate-300 mb-1.5">
                {{ __('Bio') }}
            </label>
            <textarea
                id="bio"
                name="bio"
                rows="4"
                placeholder="{{ __('Write a short bio about yourself or your interest in mysteries and investigations...') }}"
                class="w-full rounded-xl bg-[#0a0f1f] border border-slate-800 focus:border-red-600/70 focus:ring-1 focus:ring-red-600/60 text-sm text-slate-200 placeholder-slate-500 px-3.5 py-2.5 transition"
            >{{ old('bio', $profile->bio) }}</textarea>
            @error('bio')
                <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>
            @enderror
        </div>

        {{-- الموقع الجغرافي والموقع الإلكتروني --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="location" class="block text-xs font-semibold text-slate-300 mb-1.5">
                    {{ __('Location') }}
                </label>
                <input
                    id="location"
                    name="location"
                    type="text"
                    value="{{ old('location', $profile->location) }}"
                    placeholder="{{ __('e.g., Cairo, Egypt / Berlin, Germany') }}"
                    class="w-full rounded-xl bg-[#0a0f1f] border border-slate-800 focus:border-red-600/70 focus:ring-1 focus:ring-red-600/60 text-sm text-slate-200 placeholder-slate-500 px-3.5 py-2 transition"
                />
                @error('location')
                    <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="website" class="block text-xs font-semibold text-slate-300 mb-1.5">
                    {{ __('Website') }}
                </label>
                <input
                    id="website"
                    name="website"
                    type="url"
                    value="{{ old('website', $profile->website) }}"
                    placeholder="https://example.com"
                    class="w-full rounded-xl bg-[#0a0f1f] border border-slate-800 focus:border-red-600/70 focus:ring-1 focus:ring-red-600/60 text-sm text-slate-200 placeholder-slate-500 px-3.5 py-2 transition"
                />
                @error('website')
                    <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- روابط مواقع التواصل الاجتماعي (Social Links) --}}
        <div class="pt-2 border-t border-slate-800/80">
            <h3 class="text-xs font-bold text-slate-300 uppercase tracking-wider mb-3">
                {{ __('Social Links') }}
            </h3>

            @php
                $platforms = [
                    'x' => ['label' => 'X (Twitter)', 'placeholder' => 'https://x.com/username'],
                    'github' => ['label' => 'GitHub', 'placeholder' => 'https://github.com/username'],
                    'instagram' => ['label' => 'Instagram', 'placeholder' => 'https://instagram.com/username'],
                    'linkedin' => ['label' => 'LinkedIn', 'placeholder' => 'https://linkedin.com/in/username'],
                    'youtube' => ['label' => 'YouTube', 'placeholder' => 'https://youtube.com/@username'],
                ];
            @endphp

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach($platforms as $key => $meta)
                    <div>
                        <label for="social_{{ $key }}" class="block text-xs font-medium text-slate-400 mb-1">
                            {{ $meta['label'] }}
                        </label>
                        <input
                            id="social_{{ $key }}"
                            name="social_links[{{ $key }}]"
                            type="url"
                            value="{{ old("social_links.$key", $socialLinks[$key] ?? '') }}"
                            placeholder="{{ $meta['placeholder'] }}"
                            class="w-full rounded-xl bg-[#0a0f1f] border border-slate-800 focus:border-red-600/70 focus:ring-1 focus:ring-red-600/60 text-sm text-slate-200 placeholder-slate-500 px-3.5 py-2 transition"
                        />
                        @error("social_links.$key")
                            <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>
                @endforeach
            </div>
        </div>

        {{-- زر الحفظ وحالة التحديث --}}
        <div class="flex items-center gap-4 pt-2">
            <button
                type="submit"
                class="px-5 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs font-bold shadow-lg shadow-red-950/40 transition cursor-pointer"
            >
                {{ __('Save Additional Info') }}
            </button>

            @if (session('status') === 'profile-updated')
                <p class="text-xs font-medium text-emerald-400">
                    {{ __('Saved.') }}
                </p>
            @endif
        </div>
    </form>
</section>