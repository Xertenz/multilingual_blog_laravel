@props([
    'type', // 'author' | 'article_category' | 'article_tag' | ...
    'model',
    'followText' => __('Follow'),
    'followingText' => __('Following'),
])

@php
    $user = auth()->user();

    $isFollowed = $user
        ? \App\Models\Follow::query()
            ->where('user_id', $user->id)
            ->where('followable_type', $model->getMorphClass())
            ->where('followable_id', $model->getKey())
            ->exists()
        : false;

    $followersCount = \App\Models\Follow::query()
        ->where('followable_type', $model->getMorphClass())
        ->where('followable_id', $model->getKey())
        ->count();

    $toggleUrl = route('follows.toggle', ['type' => $type, 'id' => $model->id]);
    $loginUrl = route('login');
@endphp

<div
    x-data="window.createFollowButtonState({
        followed: @js((bool) $isFollowed),
        count: @js((int) $followersCount),
        isAuth: @js((bool) $user),
        type: @js($type),
        modelId: @js($model->id),
        toggleUrl: @js($toggleUrl),
        loginUrl: @js($loginUrl),
        followText: @js($followText),
        followingText: @js($followingText),
        toastFollowedTitle: @js(__('Followed')),
        toastFollowedText: @js($type === 'author' ? __('You are now following this author.') : __('You are now following this item.')),
        toastUnfollowedTitle: @js(__('Unfollowed')),
        toastUnfollowedText: @js($type === 'author' ? __('You unfollowed this author.') : __('You unfollowed this item.')),
        toastErrorTitle: @js(__('Error')),
        toastErrorText: @js(__('Failed to update follow status.')),
    })"
    class="inline-flex items-center"
>
    <button
        type="button"
        @click.prevent="toggle()"
        :disabled="loading"
        :aria-pressed="followed"
        :class="followed
            ? 'bg-slate-900 hover:bg-red-950/40 text-red-400 border-red-600/50 hover:border-red-500'
            : 'bg-red-600 hover:bg-red-700 text-white border-red-600'"
        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-[11px] font-semibold border shadow-sm transition-all duration-200 cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed select-none"
    >
        {{-- أيقونة علامة الصح عند المتابعة --}}
        <svg x-show="followed" x-cloak class="w-3.5 h-3.5 text-red-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7"></path>
        </svg>

        {{-- أيقونة الإضافة عند عدم المتابعة --}}
        <svg x-show="!followed" x-cloak class="w-3.5 h-3.5 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"></path>
        </svg>

        <span x-text="followed ? followingText : followText"></span>

        <span
            x-text="count"
            :class="followed
                ? 'bg-red-600/15 text-red-300 border-red-600/30'
                : 'bg-black/25 text-white/95 border-white/15'"
            class="px-1.5 py-0.5 rounded font-mono text-[10px] border leading-none"
        ></span>
    </button>
</div>

@once
    @push('scripts')
    <script>
        window.createFollowButtonState = function (config) {
            return {
                followed: Boolean(config.followed),
                count: Number(config.count || 0),
                loading: false,
                isAuth: Boolean(config.isAuth),
                type: config.type,
                modelId: config.modelId,
                toggleUrl: config.toggleUrl,
                loginUrl: config.loginUrl,
                followText: config.followText,
                followingText: config.followingText,
                toastFollowedTitle: config.toastFollowedTitle,
                toastFollowedText: config.toastFollowedText,
                toastUnfollowedTitle: config.toastUnfollowedTitle,
                toastUnfollowedText: config.toastUnfollowedText,
                toastErrorTitle: config.toastErrorTitle,
                toastErrorText: config.toastErrorText,

                async toggle() {
                    if (!this.isAuth) {
                        window.location.href = this.loginUrl;
                        return;
                    }

                    if (this.loading) return;
                    this.loading = true;

                    try {
                        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                        const res = await fetch(this.toggleUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': token,
                            },
                        });

                        if (res.status === 401) {
                            window.location.href = this.loginUrl;
                            return;
                        }

                        const data = await res.json();

                        if (!res.ok) {
                            console.error('Follow toggle error:', data.message ?? res.status);
                            if (typeof window.showToast === 'function') {
                                window.showToast({
                                    icon: 'error',
                                    title: this.toastErrorTitle,
                                    text: data.message ?? this.toastErrorText,
                                });
                            }
                            return;
                        }

                        if (typeof data.followed !== 'undefined') {
                            this.followed = Boolean(data.followed);
                        } else if (typeof data.is_following !== 'undefined') {
                            this.followed = Boolean(data.is_following);
                        } else {
                            this.followed = !this.followed;
                        }

                        if (typeof data.followers_count !== 'undefined') {
                            this.count = Number(data.followers_count);
                        }

                        if (this.type === 'author') {
                            const externalCountEl = document.getElementById(`author-followers-count-${this.modelId}`);
                            if (externalCountEl) {
                                externalCountEl.textContent = this.count;
                            }
                        }

                        if (typeof window.showToast === 'function') {
                            if (this.followed) {
                                window.showToast({
                                    icon: 'success',
                                    title: this.toastFollowedTitle,
                                    text: this.toastFollowedText,
                                });
                            } else {
                                window.showToast({
                                    icon: 'info',
                                    title: this.toastUnfollowedTitle,
                                    text: this.toastUnfollowedText,
                                });
                            }
                        }
                    } catch (err) {
                        console.error(err);
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