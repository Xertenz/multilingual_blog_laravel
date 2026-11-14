<div class="container mx-auto px-4 py-12 max-w-5xl text-white">
    
    <!-- بطاقة معلومات الكاتب الرئيسية -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-6 md:p-8 shadow-2xl mb-12 flex flex-col md:flex-row items-center gap-6 md:gap-8">
        <div class="relative">
            @php
                $authorAvatar = $author->hasMedia('avatar_imgs') 
                    ? $author->getFirstMediaUrl('avatar_imgs', 'medium') 
                    : asset('images/default-avatar-300x300.jpg');
            @endphp
            <div class="w-28 h-28 md:w-32 md:h-32 rounded-full overflow-hidden border-2 border-red-600/60 shadow-lg shadow-red-600/10 bg-slate-800 flex items-center justify-center text-3xl font-bold text-red-500 uppercase">
                @if($author->hasMedia('avatar_imgs'))
                    <img src="{{ $authorAvatar }}" alt="{{ $author->name }}" class="w-full h-full object-cover">
                @else
                    <span class="text-2xl text-red-400 font-extrabold">{{ strtoupper(substr($author->name, 0, 2)) }}</span>
                @endif
            </div>
        </div>

        <div class="flex-1 text-center md:text-left rtl:md:text-right space-y-3">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl md:text-3xl font-black text-white tracking-tight">
                        {{ $author->name }}
                    </h1>
                    <p class="text-xs text-red-400 font-semibold tracking-wider uppercase mt-1">
                        🕵️‍♂️ Investigator / Author
                    </p>
                </div>

                @auth
                    @if(auth()->id() !== $author->id)
                        @php
                            $isFollowingAuthor = auth()->user()->followingAuthors->contains($author->id);
                        @endphp
                        <button type="button" 
                                id="follow-author-btn-{{ $author->id }}"
                                {{-- onclick="toggleFollowAuthor(event, {{ $author->id }})" --}}
                                wire:click="toggleFollowAuthor({{$author->id}})"
                                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 text-xs font-bold rounded-xl transition duration-200 border shadow-md
                                {{ $isFollowingAuthor 
                                    ? 'bg-rose-950/80 text-rose-400 border-rose-800/60 hover:bg-rose-900/60' 
                                    : 'bg-red-600 text-white border-red-500 hover:bg-red-700 shadow-red-600/20' 
                                }}">
                            <span id="follow-author-text-{{ $author->id }}">
                                {{ $isFollowingAuthor ? __('Following') : __('Follow Author') }}
                            </span>
                        </button>
                    @endif
                @endauth
            </div>

            <div class="flex items-center justify-center md:justify-start gap-6 pt-2 text-xs text-slate-400 border-t border-slate-800/80">
                <div>
                    <span class="font-bold text-white text-sm block" id="author-followers-count-{{ $author->id }}">{{ $author->author_followers_count }}</span>
                    <span>Followers</span>
                </div>
                <div class="h-6 w-px bg-slate-800"></div>
                <div>
                    <span class="font-bold text-white text-sm block">{{ $totalContributions }}</span>
                    <span>Total Contributions</span>
                </div>
            </div>
        </div>
    </div>

    <!-- أزرار التبديل (Tabs) -->
    <div class="flex items-center gap-3 border-b border-slate-800 pb-4 mb-8 overflow-x-auto">
        <button wire:click="setTab('articles')" class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $tab === 'articles' ? 'bg-red-600 text-white shadow-lg shadow-red-600/20' : 'bg-slate-900 text-slate-400 hover:text-white border border-slate-800' }}">
            📜 Articles <span class="bg-slate-950/50 px-2 py-0.5 rounded-md text-[10px]">{{ $articles->total() }}</span>
        </button>
        <button wire:click="setTab('books')" class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $tab === 'books' ? 'bg-red-600 text-white shadow-lg shadow-red-600/20' : 'bg-slate-900 text-slate-400 hover:text-white border border-slate-800' }}">
            📚 Book Summaries <span class="bg-slate-950/50 px-2 py-0.5 rounded-md text-[10px]">{{ $books->total() }}</span>
        </button>
        <button wire:click="setTab('games')" class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $tab === 'games' ? 'bg-red-600 text-white shadow-lg shadow-red-600/20' : 'bg-slate-900 text-slate-400 hover:text-white border border-slate-800' }}">
            🎮 Game Reviews <span class="bg-slate-950/50 px-2 py-0.5 rounded-md text-[10px]">{{ $games->total() }}</span>
        </button>
        <button wire:click="setTab('media')" class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $tab === 'media' ? 'bg-red-600 text-white shadow-lg shadow-red-600/20' : 'bg-slate-900 text-slate-400 hover:text-white border border-slate-800' }}">
            🎬 Films & Docs <span class="bg-slate-950/50 px-2 py-0.5 rounded-md text-[10px]">{{ $mediaItems->total() }}</span>
        </button>
    </div>

    <!-- 1. المقالات -->
    @if($tab === 'articles')
        <div class="space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                @forelse($articles as $artTrans)
                    @php
                        $article = $artTrans->article;
                        $catTrans = $article?->category?->translations->where('locale', app()->getLocale())->first();
                        $media = $article && $article->hasMedia('featured_imgs') ? $article->getFirstMedia('featured_imgs') : null;
                    @endphp
                    <a href="{{ route('articles.show', ['slug' => $artTrans->slug, 'id' => $artTrans->id]) }}" 
                       class="group relative bg-[#0f1526] border border-slate-800 hover:border-red-600/60 rounded-xl overflow-hidden shadow-lg transition-all duration-300 flex flex-col justify-between">
                        <div>
                            <div class="relative h-40 w-full overflow-hidden bg-slate-950">
                                @if($media)
                                    <img src="{{ $media->getUrl('medium') }}" srcset="{{ $media->srcset }}" alt="{{ $artTrans->title }}" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 group-hover:scale-105 transition-all duration-500 ease-in-out">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-slate-600 text-xs">No Image</div>
                                @endif
                                <div class="absolute inset-0 bg-gradient-to-t from-[#0f1526] via-transparent to-transparent"></div>
                                @if($catTrans)
                                    <span class="absolute top-3 left-3 bg-slate-950/80 backdrop-blur-md text-red-400 border border-slate-800 text-[10px] font-semibold px-2.5 py-1 rounded-lg pointer-events-none">
                                        {{ $catTrans->name }}
                                    </span>
                                @endif
                                <span class="absolute top-3 right-3 bg-slate-950/80 backdrop-blur-md border border-slate-800 text-gray-300 text-[10px] font-mono px-2.5 py-1 rounded-lg">
                                    {{ $artTrans->created_at->format('M d, Y') }}
                                </span>
                            </div>
                            <div class="p-4">
                                <h3 class="text-base font-bold text-gray-200 group-hover:text-red-500 transition-colors line-clamp-2">
                                    {{ $artTrans->title }}
                                </h3>
                                <p class="text-xs text-gray-400 mt-1.5 line-clamp-2 leading-relaxed">
                                    {!! strip_tags($artTrans->description) !!}
                                </p>
                            </div>
                        </div>
                        <div class="p-4 pt-3 border-t border-slate-850 flex items-center justify-between text-xs text-slate-400">
                            <span class="font-mono">{{ number_format($artTrans->views ?? 0) }} views</span>
                            <span class="text-red-500 font-semibold flex items-center gap-1 shrink-0">
                                <span>Read More</span>
                                <svg class="w-4 h-4 transform group-hover:translate-x-1 rtl:group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </span>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full py-16 text-center text-slate-500 text-sm italic">No articles published yet.</div>
                @endforelse
            </div>
            <div class="mt-8">{{ $articles->links() }}</div>
        </div>
    @endif

    <!-- 2. الكتب -->
    @if($tab === 'books')
        <div class="space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                @forelse($books as $bookTrans)
                    @php
                        $book = $bookTrans->book;
                        $media = null;
                        if ($bookTrans->hasMedia('cover_imgs')) {
                            $media = $bookTrans->getFirstMedia('cover_imgs');
                        } elseif ($book && $book->hasMedia('cover_imgs')) {
                            $media = $book->getFirstMedia('cover_imgs');
                        }
                        $catTrans = $book->bookCategory?->translations->where('locale', app()->getLocale())->first() ?? $book->bookCategory?->translations->first();
                    @endphp
                    <a href="{{ route('books.show', ['id' => $bookTrans->id, 'slug' => $bookTrans->slug]) }}" 
                       class="group relative bg-[#0f1526] border border-slate-800 hover:border-red-600/60 rounded-xl overflow-hidden shadow-lg transition-all duration-300 flex flex-col justify-between">
                        <div>
                            <div class="relative h-40 w-full overflow-hidden bg-slate-950">
                                @if($media)
                                    <img src="{{ $media->getUrl('medium') }}" alt="{{ $bookTrans->title }}" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 group-hover:scale-105 transition-all duration-500 ease-in-out" loading="lazy">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-slate-600 text-xs">No Cover</div>
                                @endif
                                <div class="absolute inset-0 bg-gradient-to-t from-[#0f1526] via-transparent to-transparent"></div>
                                @if($catTrans)
                                    <span class="absolute top-3 left-3 bg-slate-950/80 backdrop-blur-md text-indigo-400 border border-indigo-900/40 text-[10px] font-semibold px-2.5 py-1 rounded-lg pointer-events-none">
                                        {{ $catTrans->name ?? $book->bookCategory->label }}
                                    </span>
                                @endif
                                <span class="absolute top-3 right-3 bg-slate-950/80 backdrop-blur-md border border-slate-800 text-gray-300 text-[10px] font-mono px-2.5 py-1 rounded-lg">
                                    {{ $book->published_year ?? '-' }}
                                </span>
                            </div>
                            <div class="p-4">
                                <h3 class="text-base font-bold text-gray-200 group-hover:text-red-500 transition-colors line-clamp-1">
                                    {{ $bookTrans->title }}
                                </h3>
                                <p class="text-xs text-slate-400 mt-1">By <span class="text-slate-300 font-semibold">{{ $bookTrans->author->name ?? 'Unknown' }}</span></p>
                                <p class="text-xs text-gray-400 mt-1.5 line-clamp-2 leading-relaxed">
                                    {!! strip_tags($bookTrans->summary) !!}
                                </p>
                            </div>
                        </div>
                        <div class="p-4 pt-3 border-t border-slate-850 flex items-center justify-between text-xs">
                            <span class="text-emerald-400 font-bold font-mono">{{ $bookTrans->price ? $bookTrans->price . ' ' . ($bookTrans->currency ?? 'USD') : 'Free' }}</span>
                            <span class="text-red-500 font-semibold flex items-center gap-1 shrink-0">
                                <span>Read Summary</span>
                                <svg class="w-4 h-4 transform group-hover:translate-x-1 rtl:group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </span>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full py-16 text-center text-slate-500 text-sm italic">No book summaries published yet.</div>
                @endforelse
            </div>
            <div class="mt-8">{{ $books->links() }}</div>
        </div>
    @endif

    <!-- 3. الألعاب -->
    @if($tab === 'games')
        <div class="space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                @forelse($games as $gameTrans)
                    @php
                        $game = $gameTrans->game;
                        $media = $game->hasMedia('cover_imgs') ? $game->getFirstMedia('cover_imgs') : null;
                        $catTrans = $game->gameCategory?->translations->where('locale', app()->getLocale())->first() ?? $game->gameCategory?->translations->first();
                    @endphp
                    <a href="{{ route('games.show', ['id' => $game->id, 'slug' => $gameTrans->slug]) }}" 
                       class="group relative bg-[#0f1526] border border-slate-800 hover:border-red-600/60 rounded-xl overflow-hidden shadow-lg transition-all duration-300 flex flex-col justify-between">
                        <div>
                            <div class="relative h-40 w-full overflow-hidden bg-slate-950">
                                @if($media)
                                    <img src="{{ $media->getUrl('medium') }}" alt="{{ $gameTrans->title }}" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 group-hover:scale-105 transition-all duration-500 ease-in-out">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-slate-600 text-xs">No Cover</div>
                                @endif
                                <div class="absolute inset-0 bg-gradient-to-t from-[#0f1526] via-transparent to-transparent"></div>
                                @if($catTrans)
                                    <span class="absolute top-3 left-3 bg-slate-950/80 backdrop-blur-md text-indigo-400 border border-indigo-900/40 text-[10px] font-semibold px-2.5 py-1 rounded-lg pointer-events-none">
                                        {{ $catTrans->name ?? $game->gameCategory->label }}
                                    </span>
                                @endif
                                <span class="absolute top-3 right-3 bg-slate-950/80 backdrop-blur-md border border-slate-800 text-gray-300 text-[10px] font-mono px-2.5 py-1 rounded-lg">
                                    {{ $game->published_year ?? '-' }}
                                </span>
                            </div>
                            <div class="p-4">
                                <h3 class="text-base font-bold text-gray-200 group-hover:text-red-500 transition-colors line-clamp-1">
                                    {{ $gameTrans->title }}
                                </h3>
                                <p class="text-xs text-indigo-400 font-medium mt-1">{{ $game->developer ?? 'Studio' }}</p>
                                <p class="text-xs text-gray-400 mt-1.5 line-clamp-2 leading-relaxed">
                                    {!! strip_tags($gameTrans->content) !!}
                                </p>
                            </div>
                        </div>
                        <div class="p-4 pt-3 border-t border-slate-850 flex items-center justify-between text-xs">
                            <span class="text-emerald-400 font-bold font-mono">{{ $gameTrans->price ? $gameTrans->price . ' ' . ($gameTrans->currency ?? 'USD') : 'Free' }}</span>
                            <span class="text-red-500 font-semibold flex items-center gap-1 shrink-0">
                                <span>Read Review</span>
                                <svg class="w-4 h-4 transform group-hover:translate-x-1 rtl:group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </span>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full py-16 text-center text-slate-500 text-sm italic">No game reviews published yet.</div>
                @endforelse
            </div>
            <div class="mt-8">{{ $games->links() }}</div>
        </div>
    @endif

    <!-- 4. الميديا -->
    @if($tab === 'media')
        <div class="space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                @forelse($mediaItems as $item)
                    @php
                        $mediaObj = $item->mediaSummary;
                        $featuredImg = $mediaObj ? $mediaObj->getFirstMedia('featured_imgs') : null;
                    @endphp
                    <a href="{{ route('media-summaries.show', ['id' => $item->media_summary_id, 'slug' => $item->slug]) }}" 
                       class="group relative bg-[#0f1526] border border-slate-800 hover:border-red-600/60 rounded-xl overflow-hidden shadow-lg transition-all duration-300 flex flex-col justify-between">
                        <div>
                            <div class="relative h-40 w-full overflow-hidden bg-slate-950">
                                @if($featuredImg)
                                    <img src="{{ $featuredImg->getUrl() }}" srcset="{{ $featuredImg->getSrcset() }}" sizes="(max-width: 640px) 100vw, 300px" alt="{{ $item->title }}" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 group-hover:scale-105 transition-all duration-500 ease-in-out" loading="lazy">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-slate-600 text-xs font-medium">No Poster</div>
                                @endif
                                <div class="absolute inset-0 bg-gradient-to-t from-[#0f1526] via-transparent to-transparent"></div>
                                @if($mediaObj && $mediaObj->type)
                                    <span class="absolute top-3 left-3 bg-slate-950/80 backdrop-blur-md text-red-400 border border-red-900/40 text-[10px] font-semibold px-2.5 py-1 rounded-lg">
                                        {{ $mediaObj->type }}
                                    </span>
                                @endif
                                @if($mediaObj && $mediaObj->imdb_rating)
                                    <span class="absolute top-3 right-3 bg-slate-950/80 backdrop-blur-md text-amber-400 border border-amber-900/40 text-[10px] font-bold px-2 py-1 rounded-lg">
                                        ★ {{ $mediaObj->imdb_rating }}
                                    </span>
                                @endif
                            </div>
                            <div class="p-4">
                                <h3 class="text-base font-bold text-gray-200 group-hover:text-red-500 transition-colors line-clamp-2">
                                    {{ $item->title }}
                                </h3>
                                @if($item->synopsis)
                                    <p class="text-xs text-gray-400 mt-1.5 line-clamp-2 leading-relaxed">
                                        {!! strip_tags($item->synopsis) !!}
                                    </p>
                                @endif
                            </div>
                        </div>
                        <div class="p-4 pt-3 border-t border-slate-850 flex items-center justify-between text-xs text-slate-400">
                            <span class="font-mono">🎬 Analysis</span>
                            <span class="text-red-500 font-semibold flex items-center gap-1 shrink-0">
                                <span>Read Analysis</span>
                                <svg class="w-4 h-4 transform group-hover:translate-x-1 rtl:group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </span>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full py-16 text-center text-slate-500 text-sm italic">No films or documentaries published yet.</div>
                @endforelse
            </div>
            <div class="mt-8">{{ $mediaItems->links() }}</div>
        </div>
    @endif

</div>