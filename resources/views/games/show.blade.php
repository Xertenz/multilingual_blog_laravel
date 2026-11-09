@extends('layouts.main')

@section('title', optional($game_trans)->meta_title ?? (optional($game_trans)->title . ' - Shadow Files'))

{{-- @section('meta_description', optional($game_trans)->meta_description) --}}

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    
    <!-- Breadcrumb / Back Link -->
    <div class="mb-6">
        <a href="{{ route('games.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 dark:hover:text-slate-200 transition-colors">
            &larr; {{ __('Back to Games') }}
        </a>
    </div>

    <!-- Article / Game Header Section -->
    <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200 dark:border-slate-700 overflow-hidden shadow-sm mb-8">
        
        <!-- Game Cover Banner -->
        <div class="relative h-72 sm:h-96 w-full bg-slate-900 overflow-hidden">
            @if($game_trans->game->hasMedia('cover_imgs'))
                <img src="{{ $game_trans->game->getFirstMediaUrl('cover_imgs', 'large') }}" 
                     alt="{{ $game_trans->title }}" 
                     class="w-full h-full object-cover opacity-90" />
            @endif
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-950/30 to-transparent"></div>
            
            <div class="absolute bottom-6 left-6 right-6 text-white">
                <!-- Category & Year -->
                <div class="flex items-center gap-3 mb-2 flex-wrap">
                    @if($game_trans->game->gameCategory)
                        <span class="bg-indigo-600/90 backdrop-blur-md text-white text-xs font-semibold px-3 py-1 rounded-lg">
                            {{ optional($game_trans->game->gameCategory->translations->where('locale', app()->getLocale())->first())->name ?? $game_trans->game->gameCategory->label }}
                        </span>
                    @endif
                    <span class="text-xs text-slate-300 font-medium">
                        {{ $game_trans->game->developer ?? 'Unknown Studio' }} @if($game_trans->game->published_year) • {{ $game_trans->game->published_year }} @endif
                    </span>
                </div>

                <!-- Game Title -->
                <h1 class="text-2xl sm:text-4xl font-extrabold tracking-tight">
                    {{ $game_trans->title }}
                </h1>
            </div>
        </div>

        <!-- Action Bar: Price, Affiliate Link & Views -->
        <div class="px-6 py-4 bg-slate-50 dark:bg-slate-900/50 border-t border-slate-200 dark:border-slate-700 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div>
                    <span class="text-[10px] uppercase font-bold text-slate-400 block">{{ __('Price') }}</span>
                    <span class="text-base font-extrabold text-slate-800 dark:text-white">
                        @if($game_trans->price > 0)
                            {{ $game_trans->price }} {{ $game_trans->currency ?? 'USD' }}
                        @else
                            <span class="text-emerald-600">Free / Review</span>
                        @endif
                    </span>
                </div>

                <div class="h-8 w-px bg-slate-200 dark:bg-slate-700"></div>

                <div>
                    <span class="text-[10px] uppercase font-bold text-slate-400 block">{{ __('Views') }}</span>
                    <span class="text-sm font-semibold text-slate-700 dark:text-slate-300">
                        {{ number_format($game_trans->views) }}
                    </span>
                </div>
            </div>

            <!-- Store / Affiliate Button -->
            @if($game_trans->affiliate_url)
                <a href="{{ $game_trans->affiliate_url }}" target="_blank" rel="nofollow noopener" 
                   class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-5 py-2.5 rounded-xl shadow-sm transition-colors flex items-center gap-2">
                    <span>🛒 {{ __('Get / Buy Game') }}</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Main Content Review Body -->
    <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200 dark:border-slate-700 p-6 sm:p-10 shadow-sm mb-8">
        <div class="prose dark:prose-invert max-w-none text-slate-700 dark:text-slate-300 leading-relaxed">
            {!! $game_trans->content !!}
        </div>
    </div>

    <!-- Tags Section -->
    @if($game_trans->game->gameTags->count() > 0)
        <div class="flex items-center gap-2 flex-wrap mb-8">
            <span class="text-xs font-bold text-slate-400 uppercase mr-2">{{ __('Tags:') }}</span>
            @foreach($game_trans->game->gameTags as $tag)
                @php
                    $tagTrans = $tag->translations->where('locale', app()->getLocale())->first();
                @endphp
                <span class="bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold px-3 py-1.5 rounded-xl">
                    # {{ $tagTrans->name ?? $tag->label }}
                </span>
            @endforeach
        </div>
    @endif

    <!-- 💬 قسم التعليقات والنقاشات الموحد للعبة (مرتبط بنظام الـ Polymorphic) -->
    <livewire:comments-section :model="$game_trans->game" />

</div>
@endsection