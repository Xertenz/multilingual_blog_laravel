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
    x-data="window.createSaveButtonState({
        saved: @js((bool) $initialSaved),
        count: @js((int) $initialCount),
        isAuth: @js((bool) $user),
        toggleUrl: @js($toggleUrl),
        loginUrl: @js($loginUrl),
        savedLabel: @js(__('Saved')),
        saveLabel: @js(__('Save')),
        toastSavedTitle: @js(__('Saved')),
        toastSavedText: @js(__('Article saved successfully.')),
        toastRemovedTitle: @js(__('Removed')),
        toastRemovedText: @js(__('Article removed from saved items.')),
        toastErrorTitle: @js(__('Error')),
        toastErrorText: @js(__('Failed to update saved status.')),
    })"
    class="inline-flex items-center"
>
    <button
        type="button"
        @click.prevent="toggleSave()"
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

        <span x-text="saved ? savedLabel : saveLabel"></span>

        <span
            x-text="count"
            class="px-1.5 py-0.5 text-[10px] font-mono rounded bg-slate-900 border border-slate-800 text-gray-300"
        ></span>
    </button>
</div>

@once
    @push('scripts')
    <script>
        window.createSaveButtonState = function (config) {
            return {
                saved: Boolean(config.saved),
                count: Number(config.count || 0),
                loading: false,
                isAuth: Boolean(config.isAuth),
                toggleUrl: config.toggleUrl,
                loginUrl: config.loginUrl,
                savedLabel: config.savedLabel,
                saveLabel: config.saveLabel,
                toastSavedTitle: config.toastSavedTitle,
                toastSavedText: config.toastSavedText,
                toastRemovedTitle: config.toastRemovedTitle,
                toastRemovedText: config.toastRemovedText,
                toastErrorTitle: config.toastErrorTitle,
                toastErrorText: config.toastErrorText,

                async toggleSave() {
                    if (!this.isAuth) {
                        window.location.href = this.loginUrl;
                        return;
                    }

                    if (this.loading) return;
                    this.loading = true;

                    try {
                        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                        const response = await fetch(this.toggleUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': token,
                            },
                        });

                        if (response.status === 401) {
                            window.location.href = this.loginUrl;
                            return;
                        }

                        const data = await response.json();

                        if (!response.ok) {
                            throw new Error(data.message ?? 'Failed to toggle save status');
                        }

                        this.saved = Boolean(data.saved ?? data.is_saved);
                        if (typeof data.saves_count !== 'undefined') {
                            this.count = Number(data.saves_count);
                        }

                        if (typeof window.showToast === 'function') {
                            if (this.saved) {
                                window.showToast({
                                    icon: 'success',
                                    title: this.toastSavedTitle,
                                    text: this.toastSavedText,
                                });
                            } else {
                                window.showToast({
                                    icon: 'info',
                                    title: this.toastRemovedTitle,
                                    text: this.toastRemovedText,
                                });
                            }
                        }
                    } catch (error) {
                        console.error('Save toggle error:', error);
                        if (typeof window.showToast === 'function') {
                            window.showToast({
                                icon: 'error',
                                title: this.toastErrorTitle,
                                text: this.toastErrorText,
                            });
                        }
                    } finally {
                        this.loading = false;
                    }
                },
            };
        };
    </script>
    @endpush
@endonce