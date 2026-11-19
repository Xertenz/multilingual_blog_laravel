@extends('layouts.main')

@section('title', ($tag_trans->meta_title ?? $tag_trans->name ?? $tag_trans->tag?->label) . ' - ' . __('Games & Reviews'))

@section('content')
<div class="container mx-auto px-4 py-8">

    {{-- رأس الصفحة بنفس نمط صفحة index --}}
    <div class="bg-[#0f1526] border border-slate-800 rounded-xl px-5 py-4 mb-6 shadow-md flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="space-y-1">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 bg-red-600 rounded-sm shrink-0"></span>
                <h1 class="text-lg md:text-xl font-bold text-white tracking-wide">
                    # {{ $tag_trans->name ?? $tag_trans->tag?->label }}
                </h1>
            </div>
            <p class="text-gray-400 text-xs max-w-2xl leading-relaxed">
                {{ $tag_trans->description ?? $tag_trans->meta_description ?? __('Explore game reviews and summaries tagged with this topic.') }}
            </p>
        </div>

        <div class="flex items-center gap-2 shrink-0 self-start sm:self-center">
            @if($tag_trans->tag)
                <x-follow-button type="game_tag" :model="$tag_trans->tag" />
            @endif
            <span class="inline-block px-2.5 py-1 bg-slate-900 text-gray-300 text-[11px] font-mono rounded-md border border-slate-800">
                {{ $games->total() }} {{ __('Games') }}
            </span>
            <a href="{{ route('games.index') }}"
               class="inline-block px-2.5 py-1 bg-slate-900 hover:bg-slate-800 text-gray-300 text-[11px] font-medium rounded-md border border-slate-800 transition-colors">
                {{ __('All Games') }}
            </a>
        </div>
    </div>

    {{-- شبكة الألعاب بنفس تصميم index.blade.php --}}
    @if($games->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($games as $trans)
                @php
                    $game = $trans->game;

                    $imageUrl = ($game && $game->hasMedia('cover_imgs'))
                        ? $game->getFirstMediaUrl('cover_imgs', 'medium')
                        : asset('images/default-game.jpg');

                    $catName = optional($game?->category?->translations->where('locale', app()->getLocale())->first())->name
                            ?? optional($game?->category?->translations->first())->name
                            ?? $game?->category?->label;
                @endphp

                <a href="{{ route('games.show', ['id' => $trans->id, 'slug' => $trans->slug ?? 'game']) }}"
                   class="group relative bg-[#0f1526] border border-slate-800 hover:border-red-600/60 rounded-xl overflow-hidden shadow-lg transition-all duration-300 flex flex-col justify-between">

                    <div>
                        <div class="relative h-40 w-full overflow-hidden bg-slate-950">
                            <img src="{{ $imageUrl }}" alt="{{ $trans->title ?? $game?->label }}"
                                 class="w-full h-full object-cover opacity-80 group-hover:opacity-100 group-hover:scale-105 transition-all duration-500 ease-in-out">
                            <div class="absolute inset-0 bg-gradient-to-t from-[#0f1526] via-transparent to-transparent"></div>

                            @if($catName)
                                <span class="absolute top-3 left-3 bg-slate-950/80 backdrop-blur-md border border-slate-800 text-gray-300 text-[10px] font-mono px-2.5 py-1 rounded-lg">
                                    {{ $catName }}
                                </span>
                            @endif

                            @if($game?->published_year)
                                <span class="absolute top-3 right-3 bg-slate-950/80 backdrop-blur-md border border-slate-800 text-gray-300 text-[10px] font-mono px-2.5 py-1 rounded-lg">
                                    {{ $game->published_year }}
                                </span>
                            @endif
                        </div>

                        <div class="p-4">
                            <div class="text-[11px] text-indigo-400 font-medium mb-1 truncate">
                                {{ $game?->developer ?? 'Unknown Studio' }}
                            </div>

                            <h2 class="text-base font-bold text-gray-200 group-hover:text-red-500 transition-colors line-clamp-2">
                                {{ $trans->title ?? $game?->label }}
                            </h2>
                            <p class="text-xs text-gray-400 mt-1.5 line-clamp-2 leading-relaxed">
                                {{ $trans->meta_description ?? Str::limit(strip_tags($trans->content), 100) }}
                            </p>
                        </div>
                    </div>

                    <div class="p-4 pt-3 border-t border-slate-850 flex items-center justify-between text-xs text-slate-400">
                        <span class="font-mono truncate max-w-[120px]">
                            @if($trans->price > 0)
                                💰 {{ $trans->price }} {{ $trans->currency ?? 'USD' }}
                            @else
                                <span class="text-emerald-500 font-semibold">Free / Review</span>
                            @endif
                        </span>

                        <span class="text-red-500 font-semibold flex items-center gap-1 shrink-0">
                            <span>{{ __('Read Review') }}</span>
                            <svg class="w-4 h-4 transform group-hover:translate-x-1 rtl:group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </span>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $games->links() }}
        </div>
    @else
        <div class="col-span-full py-12 text-center bg-[#0f1526] border border-slate-800 rounded-xl">
            <span class="text-4xl mb-3 block">🎮</span>
            <h3 class="text-base font-bold text-slate-300">{{ __('No Games Found for This Tag') }}</h3>
            <p class="text-xs text-slate-500 mt-1">{{ __('There are currently no game summaries under this tag.') }}</p>
        </div>
    @endif

</div>
@endsection