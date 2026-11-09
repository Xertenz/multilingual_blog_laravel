@extends('layouts.main') {{-- استبدل layouts.app بتخطيط الموقع الأمامي لديك --}}

@section('title', __('Games & Reviews') . ' - Shadow Files')

@section('content')
<div class="container mx-auto px-4 py-8">
    
    <!-- Page Header -->
    <div class="mb-8 text-center md:text-left">
        <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white">
            {{ __('True Crime & Mystery Games') }}
        </h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-2">
            {{ __('Explore our curated collection of investigative, detective, and narrative game reviews and summaries.') }}
        </p>
    </div>

    <!-- Games Grid -->
    @if($games->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($games as $game)
                @php
                    // جلب الترجمة الخاصة باللغة الحالية للموقع
                    $trans = $game->translate(app()->getLocale()) ?? $game->translations->first();
                @endphp

                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden shadow-sm hover:shadow-md transition-all duration-300 flex flex-col">
                    
                    <!-- Game Cover Image -->
                    <div class="relative h-64 overflow-hidden bg-slate-100 dark:bg-slate-900">
                        @if($game->hasMedia('cover_imgs'))
                            <img src="{{ $game->getFirstMediaUrl('cover_imgs', 'medium') }}" 
                                 alt="{{ optional($trans)->title ?? $game->internal_title }}" 
                                 class="w-full h-full object-cover hover:scale-105 transition-transform duration-500" />
                        @else
                            <div class="w-full h-full flex items-center justify-center text-slate-400">
                                <span class="text-xs">No Cover Image</span>
                            </div>
                        @endif

                        <!-- Category Badge -->
                        @if($game->gameCategory)
                            <span class="absolute top-3 left-3 bg-slate-900/70 backdrop-blur-md text-white text-[11px] font-semibold px-2.5 py-1 rounded-lg">
                                {{ optional($game->gameCategory->translations->where('locale', app()->getLocale())->first())->name ?? $game->gameCategory->label }}
                            </span>
                        @endif
                    </div>

                    <!-- Card Body -->
                    <div class="p-5 flex-1 flex flex-col justify-between">
                        <div>
                            <!-- Developer & Year -->
                            <div class="text-xs text-indigo-600 dark:text-indigo-400 font-medium mb-1">
                                {{ $game->developer ?? 'Unknown Studio' }} @if($game->published_year) ({{ $game->published_year }}) @endif
                            </div>

                            <!-- Game Title -->
                            <h2 class="text-lg font-bold text-slate-800 dark:text-white line-clamp-1 mb-2">
                                {{ optional($trans)->title ?? $game->internal_title }}
                            </h2>

                            <!-- Short Description / Meta Description -->
                            <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2 mb-4">
                                {{ optional($trans)->meta_description ?? Str::limit(strip_tags(optional($trans)->content), 100) }}
                            </p>
                        </div>

                        <!-- Card Footer / Action Button -->
                        <div class="pt-4 border-t border-slate-100 dark:border-slate-700 flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300">
                                @if(optional($trans)->price > 0)
                                    {{ $trans->price }} {{ $trans->currency ?? 'USD' }}
                                @else
                                    <span class="text-emerald-600">Free / Review</span>
                                @endif
                            </span>

                            <a href="{{ route('games.show', ['id' => $game->id, 'slug' => optional($trans)->slug ?? 'game']) }}" 
                               class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold px-3.5 py-2 rounded-xl transition-colors">
                                {{ __('Read Review') }} &rarr;
                            </a>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $games->links() }}
        </div>
    @else
        <div class="text-center py-16 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700">
            <span class="text-4xl mb-3 block">🎮</span>
            <h3 class="text-base font-bold text-slate-700 dark:text-slate-300">{{ __('No Games Found') }}</h3>
            <p class="text-xs text-slate-400 mt-1">{{ __('Check back later for new game summaries and reviews.') }}</p>
        </div>
    @endif

</div>
@endsection