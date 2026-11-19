@props([
    'type', // 'article_category' | 'article_tag'
    'model',
    'followText' => __('Follow'),
    'followingText' => __('Following'),
])

@php
    $user = auth()->user();
    $isFollowed = $user
        ? $model->followers()->where('users.id', $user->id)->exists()
        : false;
    $followersCount = $model->followers()->count();
@endphp

<div x-data="{
        followed: @js($isFollowed),
        count: @js($followersCount),
        loading: false,
        async toggle() {
            if (this.loading) return;

            @if(!$user)
                window.location.href = '{{ route('login') }}';
                return;
            @endif

            this.loading = true;

            try {
                const res = await fetch('{{ route('follows.toggle', ['type' => $type, 'id' => $model->id]) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.getAttribute('content') ?? '{{ csrf_token() }}'
                    }
                });

                if (res.status === 401) {
                    window.location.href = '{{ route('login') }}';
                    return;
                }

                const data = await res.json();

                if (!res.ok) {
                    console.error('Follow toggle error:', data.message ?? res.status);
                    return;
                }

                this.followed = Boolean(data.followed);
                this.count = Number(data.followers_count ?? this.count);
            } catch (err) {
                console.error(err);
            } finally {
                this.loading = false;
            }
        }
    }"
    class="inline-flex items-center">

    <button type="button"
            @click="toggle()"
            :disabled="loading"
            :aria-pressed="followed"
            :class="followed
                ? 'bg-slate-900 hover:bg-red-950/40 text-red-400 border-red-600/50 hover:border-red-500'
                : 'bg-red-600 hover:bg-red-700 text-white border-red-600'"
            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-[11px] font-semibold border shadow-sm transition-all duration-200 cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed select-none">

        {{-- أيقونة علامة الصح عند المتابعة --}}
        <svg x-show="followed" x-cloak class="w-3.5 h-3.5 text-red-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7"></path>
        </svg>

        {{-- أيقونة الإضافة عند عدم المتابعة --}}
        <svg x-show="!followed" x-cloak class="w-3.5 h-3.5 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"></path>
        </svg>

        <span x-text="followed ? @js($followingText) : @js($followText)"></span>

        <span x-text="count"
              :class="followed
                  ? 'bg-red-600/15 text-red-300 border-red-600/30'
                  : 'bg-black/25 text-white/95 border-white/15'"
              class="px-1.5 py-0.5 rounded font-mono text-[10px] border leading-none"></span>
    </button>
</div>