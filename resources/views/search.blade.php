@extends('layouts.main')

@section('title', __('Search results for: :query', ['query' => $query]))

@section('content')
<div class="bg-slate-900 text-slate-100 min-h-screen py-10">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- عنوان نتائج البحث --}}
        <div class="mb-8 border-b border-slate-800 pb-4">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
                {{ __('Search Results') }}: <span class="text-blue-500">"{{ $query }}"</span>
            </h1>
            <p class="text-sm text-slate-400 mt-1">
                {{ __('Found :count articles', ['count' => $articles->total()]) }}
            </p>
        </div>

        @if($articles->count() > 0)
            {{-- شبكة عرض المقالات --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($articles as $article)
                    @php
                        $trans = $article->translations->first();
                    @endphp
                    
                    @if($trans)
                        <div class="group bg-slate-800 border border-slate-700/60 rounded-xl overflow-hidden shadow-lg hover:shadow-blue-500/10 hover:border-slate-600 transition-all duration-300 flex flex-col">
                            
                            {{-- صورة المقالة مع تأثير التقريب Zoom --}}
                            <a href="{{ route('articles.show', ['id' => $trans->id, 'slug' => $trans->slug]) }}" class="block relative overflow-hidden aspect-video">
                                <img 
                                    src="{{ $article->getFirstMediaUrl('featured_imgs', 'thumb') ?: asset('images/default.jpg') }}" 
                                    alt="{{ $trans->title }}" 
                                    class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500 ease-out"
                                >
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-transparent to-transparent opacity-60 group-hover:opacity-40 transition-opacity"></div>
                            </a>
                            
                            {{-- محتوى المقالة --}}
                            <div class="p-5 flex flex-col flex-grow justify-between">
                                <div>
                                    <h2 class="capitalize text-lg font-bold text-white group-hover:text-blue-400 transition-colors line-clamp-2 mb-2 leading-snug">
                                        <a href="{{ route('articles.show', ['id' => $trans->id, 'slug' => $trans->slug]) }}">
                                            {{ $trans->title }}
                                        </a>
                                    </h2>
                                    
                                    <p class="first-letter:uppercase text-slate-400 text-sm line-clamp-3 leading-relaxed mb-4">
                                        {{ Str::limit(strip_tags($trans->description), 130) }}
                                    </p>
                                </div>

                                {{-- رابط القراءة --}}
                                <div class="pt-3 border-t border-slate-700/50 flex items-center justify-between text-xs text-slate-400">
                                    <a href="{{ route('articles.show', ['id' => $trans->id, 'slug' => $trans->slug]) }}" class="text-blue-400 hover:text-blue-300 font-semibold inline-flex items-center gap-1">
                                        {{ __('Read More') }}
                                        <svg class="w-4 h-4 ltr:rotate-0 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>

                        </div>
                    @endif
                @endforeach
            </div>

            {{-- الترقيم --}}
            <div class="mt-10">
                {{ $articles->links() }}
            </div>
        @else
            {{-- حالة عدم وجود نتائج --}}
            <div class="bg-slate-800/50 border border-slate-700/50 rounded-2xl p-12 text-center max-w-lg mx-auto my-12">
                <div class="w-16 h-16 bg-slate-700/50 text-slate-400 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">{{ __('No articles found matching your query.') }}</h3>
                <p class="text-slate-400 text-sm mb-6">{{ __('Try searching with different keywords or check for spelling errors.') }}</p>
            </div>
        @endif

    </div>
</div>
@endsection