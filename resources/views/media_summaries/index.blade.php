{{--
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
--}}


@extends('layouts.main')

@section('title', __('True Crime Media & Documentaries'))

@section('content')
<div class="container mx-auto px-4 py-8">

    <!-- Header — موحّد مع بقية الصفحات -->
    <div class="bg-[#0f1526] border border-slate-800 rounded-xl px-5 py-4 mb-6 shadow-md flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="space-y-1">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 bg-red-600 rounded-sm shrink-0"></span>
                <h1 class="text-lg md:text-xl font-bold text-white tracking-wide">
                    {{ __('True Crime Media Summaries') }}
                </h1>
            </div>
            <p class="text-gray-400 text-xs max-w-2xl leading-relaxed">
                {{ __('Explore crime documentaries, movies, and series with detailed forensic reviews.') }}
            </p>
        </div>

        <div class="shrink-0 self-start sm:self-center">
            <span class="inline-block px-2.5 py-1 bg-slate-900 text-gray-300 text-[11px] font-mono rounded-md border border-slate-800">
                {{ $summaries->total() }} {{ __('Items') }}
            </span>
        </div>
    </div>

    <!-- شريط الفلترة حسب النوع — منفصل تحت الرأس -->
    <div class="flex flex-col sm:flex-row sm:flex-wrap sm:items-center sm:justify-between gap-3 mb-6">
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('media-summaries.index') }}"
               class="px-3 py-1.5 rounded-lg text-xs font-semibold transition border {{ !request('type') ? 'bg-red-950/80 text-red-400 border-red-800/60' : 'bg-slate-900 text-gray-400 hover:text-white border border-slate-800' }}">
                {{ __('All Types') }}
            </a>
            <a href="{{ route('media-summaries.index', ['type' => 'documentary']) }}"
               class="px-3 py-1.5 rounded-lg text-xs font-semibold transition border {{ request('type') == 'documentary' ? 'bg-red-950/80 text-red-400 border-red-800/60' : 'bg-slate-900 text-gray-400 hover:text-white border border-slate-800' }}">
                {{ __('Documentaries') }}
            </a>
            <a href="{{ route('media-summaries.index', ['type' => 'movie']) }}"
               class="px-3 py-1.5 rounded-lg text-xs font-semibold transition border {{ request('type') == 'movie' ? 'bg-red-950/80 text-red-400 border-red-800/60' : 'bg-slate-900 text-gray-400 hover:text-white border border-slate-800' }}">
                {{ __('Movies') }}
            </a>
            <a href="{{ route('media-summaries.index', ['type' => 'series']) }}"
               class="px-3 py-1.5 rounded-lg text-xs font-semibold transition border {{ request('type') == 'series' ? 'bg-red-950/80 text-red-400 border-red-800/60' : 'bg-slate-900 text-gray-400 hover:text-white border border-slate-800' }}">
                {{ __('Series') }}
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
                <a href="{{ route('media-summaries.show', ['id' => $item->id, 'slug' => $item->slug]) }}"
                   class="group relative bg-[#0f1526] border border-slate-800 hover:border-red-600/60 rounded-xl overflow-hidden shadow-lg transition-all duration-300 flex flex-col justify-between">

                    <div>
                        <!-- صورة الملصق -->
                        <div class="relative h-72 w-full overflow-hidden bg-slate-950">
                            {{--
                            @if($featuredImg)
                                <img src="{{ $featuredImg->getUrl() }}"
                                     srcset="{{ $featuredImg->getSrcset() }}"
                                     sizes="(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 300px"
                                     alt="{{ $item->title }}"
                                     loading="lazy"
                                     class="w-full h-full object-cover opacity-80 group-hover:opacity-100 group-hover:scale-105 transition-all duration-500 ease-in-out">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-600 text-xs font-medium">
                                    No Poster Available
                                </div>
                            @endif
                            --}}
@php
    $mediaObj = $item->mediaSummary;
    $featuredImg = $mediaObj ? $mediaObj->getFirstMedia('featured_imgs') : null;

    // صورة واضحة بتحويل محدد (نفس نمط صفحة الألعاب)
    $imageUrl = $featuredImg
        ? ($featuredImg->hasGeneratedConversion('medium')
            ? $featuredImg->getUrl('medium')
            : $featuredImg->getUrl())
        : null;
@endphp

@if($imageUrl)
    <img src="{{ $imageUrl }}"
         alt="{{ $item->title }}"
         loading="lazy"
         class="w-full h-full object-cover opacity-80 group-hover:opacity-100 group-hover:scale-105 transition-all duration-500 ease-in-out">
@else
    <div class="w-full h-full flex items-center justify-center text-slate-600 text-xs font-medium">
        No Poster Available
    </div>
@endif

                            <div class="absolute inset-0 bg-gradient-to-t from-[#0f1526] via-transparent to-transparent"></div>

                            <!-- شارة النوع أعلى اليسار -->
                            @if($mediaObj?->type)
                                <span class="absolute top-3 left-3 bg-slate-950/80 backdrop-blur-md border border-slate-800 text-red-400 text-[10px] font-mono px-2.5 py-1 rounded-lg uppercase">
                                    {{ $mediaObj->type }}
                                </span>
                            @endif

                            <!-- شارة IMDb أعلى اليمين -->
                            @if($mediaObj?->imdb_rating)
                                <span class="absolute top-3 right-3 bg-slate-950/80 backdrop-blur-md border border-slate-800 text-amber-400 text-[10px] font-mono px-2.5 py-1 rounded-lg">
                                    ★ {{ $mediaObj->imdb_rating }}
                                </span>
                            @endif
                        </div>

                        <!-- محتوى البطاقة -->
                        <div class="p-4">
                            <h2 class="text-base font-bold text-gray-200 group-hover:text-red-500 transition-colors line-clamp-2">
                                {{ $item->title }}
                            </h2>
                            @if($item->synopsis)
                                <p class="text-xs text-gray-400 mt-1.5 line-clamp-2 leading-relaxed">
                                    {{ strip_tags($item->synopsis) }}
                                </p>
                            @endif
                        </div>
                    </div>

                    <!-- الشريط السفلي — مطابق لبقية البطاقات -->
                    <div class="p-4 pt-3 border-t border-slate-850 flex items-center justify-between text-xs text-slate-400">
                        <span class="font-mono truncate max-w-[120px]">
                            {{ $mediaObj?->release_year ?? '—' }}
                            <span class="text-slate-600">•</span>
                            👁️ {{ $item->views }}
                        </span>

                        <span class="text-red-500 font-semibold flex items-center gap-1 shrink-0">
                            <span>{{ __('Read Analysis') }}</span>
                            <svg class="w-4 h-4 transform group-hover:translate-x-1 rtl:group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </span>
                    </div>
                </a>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-10">
            {{ $summaries->links() }}
        </div>
    @else
        <div class="col-span-full py-12 text-center bg-[#0f1526] border border-slate-800 rounded-xl">
            <p class="text-sm text-gray-400">{{ __('No media summaries found in this section yet.') }}</p>
        </div>
    @endif
</div>
@endsection