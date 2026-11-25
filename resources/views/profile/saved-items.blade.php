<x-app-layout>
    @php
        $userLocale = $userLocale ?? (auth()->user()->locale ?? app()->getLocale());
        $activeType = $activeType ?? 'all';
        $filters = [
            'all' => __('All'),
            'article' => __('Articles'),
            'book' => __('Books'),
            'game' => __('Games'),
            'media_summary' => __('Media Summaries'),
        ];
    @endphp

    <div class="py-8 bg-[#0a0f1f] min-h-screen text-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @include('profile.partials.profile-menu')

            <div class="bg-slate-900/90 backdrop-blur-md shadow-xl rounded-2xl border border-slate-800 overflow-hidden">

                <!-- الرأس والفلترة حسب النوع -->
                <div class="p-6 sm:p-8 border-b border-slate-800 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-bold text-white">
                            {{ __('Saved Items') }}
                        </h3>
                        <p class="mt-1 text-xs text-slate-400">
                            {{ __('All articles, books, games, and media summaries you have bookmarked.') }}
                        </p>
                    </div>

                    <div class="flex items-center gap-1.5 flex-wrap">
                        @foreach($filters as $filterKey => $filterLabel)
                            <a href="{{ route('profile.saved', $filterKey === 'all' ? [] : ['type' => $filterKey]) }}"
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

                <!-- شبكة العناصر المحفوظة -->
                <div class="p-6 sm:p-8" id="saved-items-wrapper">
                    @if($savedItems->isNotEmpty())
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6" id="saved-items-grid">
                            @foreach($savedItems as $entry)
                                @php
                                    $type = $entry['type'];
                                    $model = $entry['model'];
                                    $savedAt = $entry['saved_at'];
                                @endphp

                                @continue(!$model)

                                @php
                                    $trans = $model->translations->firstWhere('locale', $userLocale)
                                        ?? $model->translations->first();

                                    $title = $trans->title ?? ($model->title ?? ($model->name ?? __('Untitled')));
                                    $excerpt = $trans->description
                                        ?? ($trans->summary
                                        ?? ($trans->content
                                        ?? ($trans->synopsis ?? '')));

                                    $mediaCollection = $type === 'game' ? 'cover_imgs' : 'featured_imgs';
                                    $media = $model->hasMedia($mediaCollection)
                                        ? $model->getFirstMedia($mediaCollection)
                                        : ($model->hasMedia('featured_imgs')
                                            ? $model->getFirstMedia('featured_imgs')
                                            : ($model->hasMedia('cover_imgs') ? $model->getFirstMedia('cover_imgs') : null));

                                    $imageUrl = $media
                                        ? ($media->hasGeneratedConversion('medium') ? $media->getUrl('medium') : $media->getUrl())
                                        : asset('images/default-article.jpg');

                                    $detailUrl = match($type) {
                                        'article' => $trans ? route('articles.show', ['id' => $trans->id, 'slug' => $trans->slug]) : '#',
                                        'book' => $trans ? route('books.show', ['id' => $trans->id, 'slug' => $trans->slug]) : '#',
                                        'game' => $trans ? route('games.show', ['id' => $trans->id, 'slug' => $trans->slug]) : '#',
                                        'media_summary' => $trans ? route('media-summaries.show', ['id' => $trans->id, 'slug' => $trans->slug]) : '#',
                                        default => '#',
                                    };

                                    $typeBadge = match($type) {
                                        'article' => __('Article'),
                                        'book' => __('Book'),
                                        'game' => __('Game'),
                                        'media_summary' => __('Media'),
                                        default => $type,
                                    };

                                    $cardDomId = "saved-card-{$type}-{$model->id}";
                                @endphp

                                <div id="{{ $cardDomId }}"
                                     class="group relative bg-[#0f1526] border border-slate-800 hover:border-red-600/60 rounded-xl overflow-hidden shadow-lg transition-all duration-300 flex flex-col justify-between">

                                    <div>
                                        <div class="relative h-40 w-full overflow-hidden bg-slate-950">
                                            <a href="{{ $detailUrl }}" class="block w-full h-full">
                                                <img src="{{ $imageUrl }}"
                                                     alt="{{ $title }}"
                                                     class="w-full h-full object-cover opacity-80 group-hover:opacity-100 group-hover:scale-105 transition-all duration-500 ease-in-out"
                                                     loading="lazy">
                                            </a>
                                            <div class="absolute inset-0 bg-gradient-to-t from-[#0f1526] via-transparent to-transparent pointer-events-none"></div>

                                            <span class="absolute top-3 left-3 bg-slate-950/85 backdrop-blur-md text-red-400 border border-slate-800 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider">
                                                {{ $typeBadge }}
                                            </span>

                                            <span class="absolute top-3 right-3 bg-slate-950/85 backdrop-blur-md border border-slate-800 text-gray-300 text-[10px] font-mono px-2.5 py-1 rounded-lg">
                                                {{ optional($savedAt)->format('M d, Y') }}
                                            </span>
                                        </div>

                                        <div class="p-4">
                                            <h2 class="text-base font-bold text-gray-200 group-hover:text-red-500 transition-colors line-clamp-2">
                                                <a href="{{ $detailUrl }}">{{ $title }}</a>
                                            </h2>

                                            @if($excerpt)
                                                <p class="text-xs text-gray-400 mt-1.5 line-clamp-2 leading-relaxed">
                                                    {{ Str::limit(strip_tags($excerpt), 100) }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="px-4 pb-4 pt-3 border-t border-slate-800/80 flex items-center justify-between text-xs">
                                        <a href="{{ $detailUrl }}" class="text-red-500 hover:text-red-400 font-semibold flex items-center gap-1">
                                            <span>{{ __('Open') }}</span>
                                            <span>&rarr;</span>
                                        </a>

                                        <button type="button"
                                                onclick="removeSavedItem(event, '{{ $type }}', {{ $model->id }})"
                                                class="text-xs text-rose-400 hover:text-rose-300 bg-rose-950/40 hover:bg-rose-900/40 border border-rose-900/50 font-semibold px-3 py-1.5 rounded-lg transition-colors duration-150">
                                            {{ __('Remove') }}
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{ $savedItems->links() }}
                        </div>
                    @else
                        <div class="text-center py-12 bg-slate-900/40 rounded-xl border border-dashed border-slate-800" id="empty-saved-message">
                            <span class="text-3xl mb-2 block">🔖</span>
                            <p class="text-sm font-medium text-slate-400">{{ __('No saved items found.') }}</p>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        async function removeSavedItem(event, type, id) {
            event.preventDefault();

            const baseUrl = "{{ route('saved.toggle', ['type' => '__TYPE__', 'id' => '__ID__']) }}";
            const url = baseUrl.replace('__TYPE__', type).replace('__ID__', id);
            const card = document.getElementById(`saved-card-${type}-${id}`);

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
                const isSaved = Boolean(data.is_saved ?? data.saved ?? data.attached);

                if (!isSaved && card) {
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.95)';
                    card.style.transition = 'all 0.3s ease';

                    setTimeout(() => {
                        card.remove();
                        const grid = document.getElementById('saved-items-grid');
                        if (grid && grid.children.length === 0) {
                            grid.remove();
                            document.getElementById('saved-items-wrapper').innerHTML = `
                                <div class="text-center py-12 bg-slate-900/40 rounded-xl border border-dashed border-slate-800" id="empty-saved-message">
                                    <span class="text-3xl mb-2 block">🔖</span>
                                    <p class="text-sm font-medium text-slate-400">{{ __('No saved items found.') }}</p>
                                </div>
                            `;
                        }
                    }, 300);
                }

                if (typeof window.showToast === 'function') {
                    window.showToast({
                        icon: 'success',
                        title: @json(__('Success')),
                        text: @json(__('Removed from saved items.')),
                    });
                }
            } catch (error) {
                if (typeof window.showToast === 'function') {
                    window.showToast({
                        icon: 'error',
                        title: @json(__('Error')),
                        text: @json(__('Failed to remove item.')),
                    });
                }
            }
        }
    </script>
    @endpush
</x-app-layout>