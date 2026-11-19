@props([
    'type',
    'model',
])

@php
    $user = auth()->user();

    $initialSaved = $user
        ? $model->savedBy()->where('users.id', $user->id)->exists()
        : false;

    $initialCount = $model->saved_by_count ?? $model->savedBy()->count();
    $toggleUrl = route('saved.toggle', ['type' => $type, 'id' => $model->id]);
    $loginUrl = route('login');
@endphp

<div
    x-data="{
        saved: @js($initialSaved),
        count: @js((int) $initialCount),
        loading: false,
        async toggleSave() {
            @if(!$user)
                window.location.href = @js($loginUrl);
                return;
            @endif

            if (this.loading) return;
            this.loading = true;

            try {
                const response = await fetch(@js($toggleUrl), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']')?.getAttribute('content') || '',
                    },
                });

                if (response.status === 401) {
                    window.location.href = @js($loginUrl);
                    return;
                }

                if (!response.ok) {
                    throw new Error('Failed to toggle save status');
                }

                const data = await response.json();
                this.saved = Boolean(data.saved ?? data.is_saved);
                if (typeof data.saves_count !== 'undefined') {
                    this.count = Number(data.saves_count);
                }
            } catch (error) {
                console.error('Save toggle error:', error);
            } finally {
                this.loading = false;
            }
        }
    }"
    class="inline-flex items-center"
>
    <button
        type="button"
        @click="toggleSave()"
        :disabled="loading"
        :class="saved
            ? 'bg-red-600/15 border-red-600/60 text-red-400 hover:bg-red-600/25'
            : 'bg-[#0f1526] border-slate-800 text-gray-300 hover:border-slate-700 hover:text-white'"
        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border text-xs font-semibold transition-all duration-200 disabled:opacity-60 cursor-pointer"
    >
        <svg
            class="w-4 h-4 shrink-0 transition-transform duration-200"
            :class="saved ? 'scale-110 fill-current text-red-500' : 'fill-none'"
            stroke="currentColor"
            viewBox="0 0 24 24"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"
            />
        </svg>

        <span x-text="saved ? @js(__('Saved')) : @js(__('Save'))"></span>

        <span
            x-text="count"
            class="px-1.5 py-0.5 text-[10px] font-mono rounded bg-slate-900 border border-slate-800 text-gray-300"
        ></span>
    </button>
</div>