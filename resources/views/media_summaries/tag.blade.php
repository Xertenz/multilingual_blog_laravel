@extends('layouts.main')

@section('title', '# ' . ($tag_trans->meta_title ?? $tag_trans->name ?? $tag_trans->tag?->label) . ' - ' . __('True Crime Media Summaries'))

@section('content')
<div class="container mx-auto px-4 py-8">

    <!-- Header -->
    <div class="bg-[#0f1526] border border-slate-800 rounded-xl px-5 py-4 mb-6 shadow-md flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="space-y-1">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 bg-red-600 rounded-sm shrink-0"></span>
                <h1 class="text-lg md:text-xl font-bold text-white tracking-wide">
                    # {{ $tag_trans->name ?? $tag_trans->tag?->label }}
                </h1>
            </div>
            <p class="text-gray-400 text-xs max-w-2xl leading-relaxed">
                {{ $tag_trans->description ?? $tag_trans->meta_description ?? __('Explore crime documentaries, movies, and series tagged with this topic.') }}
            </p>
        </div>

        <div class="flex items-center gap-2 shrink-0 self-start sm:self-center">
            <span class="inline-block px-2.5 py-1 bg-slate-900 text-gray-300 text-[11px] font-mono rounded-md border border-slate-800">
                {{ $summaries->total() }} {{ __('Items') }}
            </span>
            <a href="{{ route('media-summaries.index') }}"
               class="inline-block px-2.5 py-1 bg-slate-900 hover:bg-slate-800 text-gray-300 text-[11px] font-medium rounded-md border border-slate-800 transition-colors">
                {{ __('All Media') }}
            </a>
        </div>
    </div>

    <!-- Media Cards Grid -->
    @if($summaries->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @foreach($summaries as $item)
            @php
                $mediaObj = $item->mediaSummary;
                $featuredImg = $mediaObj?->getFirstMedia('featured_imgs');
                $imageUrl = $featuredImg
                    ? ($featuredImg->hasGeneratedConversion('medium')
                        ? $featuredImg->getUrl('medium')
                        : $featuredImg->getUrl())
                    : null;
            @endphp

            <a href="{{ route('media-summaries.show', ['id' => $item->id, 'slug' => $item->slug]) }}"
               class="group relative bg-[#0f1526] border border-slate-800 hover:border-red-600/60 rounded-xl overflow-hidden shadow-lg transition-all duration-300 flex flex-col justify-between">

                <div>
                    <div class="relative h-72 w-full overflow-hidden bg-slate-950">
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

                        @if($mediaObj?->type)
                            <span class="absolute top-3 left-3 bg-slate-950/80 backdrop-blur-md border border-slate-800 text-red-400 text-[10px] font-mono px-2.5 py-1 rounded-lg uppercase">
                                {{ $mediaObj->type }}
                            </span>
                        @endif

                        @if($mediaObj?->imdb_rating)
                            <span class="absolute top-3 right-3 bg-slate-950/80 backdrop-blur-md border border-slate-800 text-amber-400 text-[10px] font-mono px-2.5 py-1 rounded-lg">
                                ★ {{ $mediaObj->imdb_rating }}
                            </span>
                        @endif
                    </div>

                    <div class="p-4">
                        <h2 class="text-base font-bold text-gray-200 group-hover:text-red-500 transition-colors line-clamp-2">
                            {{ $item->title }}
                        </h2>
                        @if($item->synopsis)
                            <p class="text-xs text-gray-400 mt-1.5 line-clamp-2 leading-relaxed">
                                {{ Str::limit(strip_tags($item->synopsis), 120) }}
                            </p>
                        @endif
                    </div>
                </div>

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

        <div class="mt-10">
            {{ $summaries->links() }}
        </div>
    @else
        <div class="col-span-full py-12 text-center bg-[#0f1526] border border-slate-800 rounded-xl">
            <p class="text-sm text-gray-400">{{ __('No media summaries found under this tag yet.') }}</p>
        </div>
    @endif
</div>
@endsection