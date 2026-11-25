<x-app-layout>
    @php
        $userLocale = $userLocale ?? (auth()->user()->locale ?? app()->getLocale());
        $activeType = $activeType ?? 'all';
        $filters = [
            'all' => __('All'),
            'author' => __('Authors'),
            'article_category' => __('Article Categories'),
            'article_tag' => __('Article Tags'),
            'book_category' => __('Book Categories'),
            'book_tag' => __('Book Tags'),
            'game_category' => __('Game Categories'),
            'game_tag' => __('Game Tags'),
            'media_summary_category' => __('Media Categories'),
            'media_summary_tag' => __('Media Tags'),
        ];
    @endphp

    <div class="py-8 bg-[#0a0f1f] min-h-screen text-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @include('profile.partials.profile-menu')

            <div class="bg-slate-900/90 backdrop-blur-md shadow-xl rounded-2xl border border-slate-800 overflow-hidden">

                <!-- الرأس والفلترة -->
                <div class="p-6 sm:p-8 border-b border-slate-800 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-bold text-white">
                            {{ __('Following') }}
                        </h3>
                        <p class="mt-1 text-xs text-slate-400">
                            {{ __('Manage the authors, categories, and tags you follow across all sections.') }}
                        </p>
                    </div>

                    <div class="flex items-center gap-1.5 flex-wrap">
                        @foreach($filters as $filterKey => $filterLabel)
                            <a href="{{ route('profile.following', $filterKey === 'all' ? [] : ['type' => $filterKey]) }}"
                               class="px-3 py-1.5 rounded-lg text-xs font-semibold transition border
                               {{ $activeType === $filterKey
                                   ? 'bg-red-950/70 text-red-400 border-red-800/60'
                                   : 'bg-slate-800/70 text-slate-400 border-slate-700/70 hover:text-white hover:bg-slate-800'
                               }}">
                                {{ $filterLabel }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- شبكة المتابعات -->
                <div class="p-6 sm:p-8" id="following-items-wrapper">
                    @if($followingItems->isNotEmpty())
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5" id="following-items-grid">
                            @foreach($followingItems as $entry)
                                @php
                                    $type = $entry['type'];
                                    $model = $entry['model'];
                                    $followedAt = $entry['followed_at'];
                                @endphp

                                @continue(!$model)

                                @php
                                    $isAuthor = $type === 'author';
                                    $trans = (!$isAuthor && method_exists($model, 'translations'))
                                        ? ($model->translations->firstWhere('locale', $userLocale) ?? $model->translations->first())
                                        : null;

                                    $title = $isAuthor
                                        ? ($model->name ?? __('Author'))
                                        : ($trans->name ?? ($trans->title ?? ($model->label ?? __('Untitled'))));

                                    $subtitle = match($type) {
                                        'author' => __('Author'),
                                        'article_category' => __('Article Category'),
                                        'article_tag' => __('Article Tag'),
                                        'book_category' => __('Book Category'),
                                        'book_tag' => __('Book Tag'),
                                        'game_category' => __('Game Category'),
                                        'game_tag' => __('Game Tag'),
                                        'media_summary_category' => __('Media Category'),
                                        'media_summary_tag' => __('Media Tag'),
                                        default => $type,
                                    };

                                    $avatarOrImg = null;
                                    if ($isAuthor) {
                                        $avatarOrImg = method_exists($model, 'hasMedia') && $model->hasMedia('avatar_imgs')
                                            ? $model->getFirstMediaUrl('avatar_imgs', 'thumb')
                                            : null;
                                    } elseif (method_exists($model, 'hasMedia') && $model->hasMedia('featured_imgs')) {
                                        $avatarOrImg = $model->getFirstMedia('featured_imgs')->hasGeneratedConversion('thumb')
                                            ? $model->getFirstMediaUrl('featured_imgs', 'thumb')
                                            : $model->getFirstMediaUrl('featured_imgs');
                                    }

                                    $detailUrl = match($type) {
                                        'author' => route('authors.show', $model->id),
                                        'article_category' => $trans ? route('article-categories.show', ['id' => $trans->id, 'slug' => $trans->slug]) : '#',
                                        'article_tag' => $trans ? route('article-tags.show', ['id' => $trans->id, 'slug' => $trans->slug]) : '#',
                                        'book_category' => $trans ? route('book-categories.show', ['id' => $trans->id, 'slug' => $trans->slug]) : '#',
                                        'book_tag' => $trans ? route('book-tags.show', ['id' => $trans->id, 'slug' => $trans->slug]) : '#',
                                        'game_category' => $trans ? route('game-categories.show', ['id' => $trans->id, 'slug' => $trans->slug]) : '#',
                                        'game_tag' => $trans ? route('game-tags.show', ['id' => $trans->id, 'slug' => $trans->slug]) : '#',
                                        'media_summary_category' => $trans ? route('media-summary-categories.show', ['id' => $trans->id, 'slug' => $trans->slug]) : '#',
                                        'media_summary_tag' => $trans ? route('media-summary-tags.show', ['id' => $trans->id, 'slug' => $trans->slug]) : '#',
                                        default => '#',
                                    };

                                    $cardDomId = "follow-card-{$type}-{$model->id}";
                                @endphp

                                <div id="{{ $cardDomId }}"
                                     class="flex flex-col justify-between p-5 bg-[#0f1526] border border-slate-800 hover:border-red-600/60 rounded-xl shadow-lg transition-all duration-200 group">
                                    <div class="flex items-center justify-between gap-3">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div class="w-12 h-12 rounded-full overflow-hidden border border-slate-700 bg-slate-800 flex items-center justify-center text-red-400 font-bold text-sm uppercase shrink-0">
                                                @if($avatarOrImg)
                                                    <img src="{{ $avatarOrImg }}" alt="{{ $title }}" class="w-full h-full object-cover" loading="lazy">
                                                @elseif($isAuthor)
                                                    <span>{{ strtoupper(mb_substr($title, 0, 2)) }}</span>
                                                @else
                                                    <span>#</span>
                                                @endif
                                            </div>

                                            <div class="min-w-0">
                                                <span class="text-[10px] font-bold uppercase tracking-wider text-red-400 block">
                                                    {{ $subtitle }}
                                                </span>
                                                <a href="{{ $detailUrl }}" class="text-sm font-bold text-slate-200 group-hover:text-red-500 transition-colors truncate block">
                                                    {{ $title }}
                                                </a>
                                            </div>
                                        </div>

                                        <span class="text-[10px] font-mono text-slate-500 shrink-0">
                                            {{ optional($followedAt)->format('M d, Y') }}
                                        </span>
                                    </div>

                                    <div class="flex items-center justify-between border-t border-slate-800/80 pt-4 mt-4 text-xs">
                                        <a href="{{ $detailUrl }}" class="text-slate-400 hover:text-white font-semibold transition">
                                            {{ __('View') }} &rarr;
                                        </a>

                                        <button type="button"
                                                onclick="removeFollowItem(event, '{{ $type }}', {{ $model->id }})"
                                                class="text-xs text-rose-400 hover:text-rose-300 bg-rose-950/40 hover:bg-rose-900/40 border border-rose-900/50 font-semibold px-3 py-1.5 rounded-lg transition-colors duration-150">
                                            {{ __('Unfollow') }}
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{ $followingItems->links() }}
                        </div>
                    @else
                        <div class="text-center py-12 bg-slate-900/40 rounded-xl border border-dashed border-slate-800" id="empty-following-message">
                            <span class="text-3xl mb-2 block">✨</span>
                            <p class="text-sm font-medium text-slate-400">{{ __('You are not following anything yet.') }}</p>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        async function removeFollowItem(event, type, id) {
            event.preventDefault();

            const baseUrl = "{{ route('follows.toggle', ['type' => '__TYPE__', 'id' => '__ID__']) }}";
            const url = baseUrl.replace('__TYPE__', type).replace('__ID__', id);
            const card = document.getElementById(`follow-card-${type}-${id}`);

            try {
                const response = await fetch(url, {
                    method: "POST",
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) throw new Error('Network response was not ok');

                const data = await response.json();
                const isFollowing = Boolean(data.is_following ?? data.following ?? data.attached);

                if (!isFollowing && card) {
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.95)';
                    card.style.transition = 'all 0.3s ease';

                    setTimeout(() => {
                        card.remove();
                        const grid = document.getElementById('following-items-grid');
                        if (grid && grid.children.length === 0) {
                            grid.remove();
                            document.getElementById('following-items-wrapper').innerHTML = `
                                <div class="text-center py-12 bg-slate-900/40 rounded-xl border border-dashed border-slate-800" id="empty-following-message">
                                    <span class="text-3xl mb-2 block">✨</span>
                                    <p class="text-sm font-medium text-slate-400">{{ __('You are not following anything yet.') }}</p>
                                </div>
                            `;
                        }
                    }, 300);
                }

                if (typeof window.showToast === 'function') {
                    window.showToast({
                        icon: 'success',
                        title: @json(__('Success')),
                        text: @json(__('Unfollowed successfully.')),
                    });
                }
            } catch (error) {
                if (typeof window.showToast === 'function') {
                    window.showToast({
                        icon: 'error',
                        title: @json(__('Error')),
                        text: @json(__('Failed to unfollow item.')),
                    });
                }
            }
        }
    </script>
    @endpush
</x-app-layout>