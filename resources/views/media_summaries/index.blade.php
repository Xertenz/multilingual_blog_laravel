@extends('layouts.main')

@section('title', __('True Crime Media & Documentaries'))

@section('content')
<div class="container mx-auto px-4 py-10 max-w-7xl">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 pb-6 border-b border-slate-800 gap-4">
        <div>
            <span class="text-xs font-bold text-red-500 uppercase tracking-widest">Media Archives</span>
            <h1 class="text-3xl md:text-4xl font-extrabold text-white mt-1">
                True Crime Media Summaries
            </h1>
            <p class="text-sm text-slate-400 mt-2">
                Explore crime documentaries, movies, and series with detailed forensic reviews.
            </p>
        </div>

        <!-- Filter Buttons by Type -->
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('media-summaries.index') }}" 
               class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition border {{ !request('type') ? 'bg-red-600 border-red-600 text-white' : 'bg-slate-900 border-slate-800 text-slate-400 hover:text-white hover:border-slate-700' }}">
                All Types
            </a>
            <a href="{{ route('media-summaries.index', ['type' => 'documentary']) }}" 
               class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition border {{ request('type') == 'documentary' ? 'bg-red-600 border-red-600 text-white' : 'bg-slate-900 border-slate-800 text-slate-400 hover:text-white hover:border-slate-700' }}">
                Documentaries
            </a>
            <a href="{{ route('media-summaries.index', ['type' => 'movie']) }}" 
               class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition border {{ request('type') == 'movie' ? 'bg-red-600 border-red-600 text-white' : 'bg-slate-900 border-slate-800 text-slate-400 hover:text-white hover:border-slate-700' }}">
                Movies
            </a>
            <a href="{{ route('media-summaries.index', ['type' => 'series']) }}" 
               class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition border {{ request('type') == 'series' ? 'bg-red-600 border-red-600 text-white' : 'bg-slate-900 border-slate-800 text-slate-400 hover:text-white hover:border-slate-700' }}">
                Series
            </a>
        </div>
    </div>

    <!-- Media Cards Grid -->
    @if($summaries->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($summaries as $item)
                @php
                    $mediaObj = $item->mediaSummary;
                    $featuredImg = $mediaObj ? $mediaObj->getFirstMedia('featured_imgs') : null;
                @endphp
                <article class="bg-slate-900/60 border border-slate-800 rounded-2xl overflow-hidden shadow-lg hover:border-slate-700 transition duration-300 flex flex-col group">
                    
                    <!-- Poster Container -->
                    <a href="{{ route('media-summaries.show', ['id' => $item->id, 'slug' => $item->slug]) }}" class="relative block h-72 overflow-hidden bg-slate-950">
                        @if($featuredImg)
                            <img src="{{ $featuredImg->getUrl() }}" 
                                 srcset="{{ $featuredImg->getSrcset() }}" 
                                 sizes="(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 300px"
                                 alt="{{ $item->title }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-500" 
                                 loading="lazy">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-slate-600 text-xs font-medium">
                                No Poster Available
                            </div>
                        @endif

                        <!-- Type Badge -->
                        <span class="absolute top-3 left-3 bg-slate-950/80 backdrop-blur-md text-red-400 border border-red-900/40 px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider">
                            {{ $mediaObj->type }}
                        </span>

                        <!-- IMDb Rating -->
                        @if($mediaObj->imdb_rating)
                            <span class="absolute top-3 right-3 bg-slate-950/80 backdrop-blur-md text-amber-400 border border-amber-900/40 px-2 py-1 rounded-md text-[11px] font-bold">
                                ★ {{ $mediaObj->imdb_rating }}
                            </span>
                        @endif
                    </a>

                    <!-- Card Body -->
                    <div class="p-5 flex flex-col flex-1">
                        <div class="flex items-center gap-2 text-slate-400 text-xs mb-2 font-mono">
                            @if($mediaObj->release_year)
                                <span>{{ $mediaObj->release_year }}</span>
                            @endif
                            <span>•</span>
                            <span>{{ $item->views }} views</span>
                        </div>

                        <h2 class="text-base font-bold text-white group-hover:text-red-500 transition line-clamp-2 mb-2 leading-snug">
                            <a href="{{ route('media-summaries.show', ['id' => $item->id, 'slug' => $item->slug]) }}">
                                {{ $item->title }}
                            </a>
                        </h2>

                        @if($item->synopsis)
                            <p class="text-xs text-slate-400 line-clamp-3 mb-4 leading-relaxed">
                                {{ strip_tags($item->synopsis) }}
                            </p>
                        @endif

                        <div class="mt-auto pt-3 border-t border-slate-800/60 flex items-center justify-between">
                            <a href="{{ route('media-summaries.show', ['id' => $item->id, 'slug' => $item->slug]) }}" 
                               class="text-xs font-semibold text-red-500 hover:text-red-400 flex items-center gap-1">
                                Read Analysis &rarr;
                            </a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <!-- Pagination Links -->
        <div class="mt-10">
            {{ $summaries->links() }}
        </div>
    @else
        <div class="text-center py-16 bg-slate-900/30 border border-slate-800 rounded-2xl">
            <p class="text-slate-400 text-sm">No media summaries found in this section yet.</p>
        </div>
    @endif
</div>
@endsection