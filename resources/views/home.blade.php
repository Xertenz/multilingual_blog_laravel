@extends('layouts.main')

@section('title', __('home.title', ['default' => 'Home - Latest News & Articles']))

@section('meta_tags')
    @vite(['resources/css/home.css'])
@endsection

@section('content')
@php
    $currentLocale = app()->getLocale();
@endphp

<div class="container mx-auto px-4 py-8 space-y-12">

    {{-- 1️⃣ Hero Section: المقالة البارزة --}}
    @if($featuredArticle)
        @php
            $featuredTrans = $featuredArticle->translations->firstWhere('locale', $currentLocale)
                ?? $featuredArticle->translations->first();
            $featuredCatTrans = $featuredArticle->category?->translations->firstWhere('locale', $currentLocale)
                ?? $featuredArticle->category?->translations->first();
        @endphp

        @if($featuredTrans)
            <section class="bg-[#0f1526] border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center p-5 md:p-8">

                    {{-- صورة المقالة البارزة --}}
                    <div class="lg:col-span-7 overflow-hidden rounded-xl h-64 sm:h-80 md:h-[380px] bg-slate-950 border border-slate-800/80 relative [&_img]:!h-full [&_img]:!w-full [&_img]:!object-cover [&_picture]:!h-full [&_picture]:!w-full">
                        @if($featuredArticle->hasMedia('featured_imgs'))
                            {{ $featuredArticle->getFirstMedia('featured_imgs')->img('', [
                                'class' => 'transform hover:scale-105 transition-transform duration-500',
                                'alt'   => $featuredTrans->title,
                            ]) }}
                        @else
                            <img src="{{ asset('images/default-category.jpg') }}"
                                 alt="{{ $featuredTrans->title }}"
                                 class="w-full h-full object-cover transform hover:scale-105 transition-transform duration-500">
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-[#0f1526]/70 via-transparent to-transparent pointer-events-none"></div>
                    </div>

                    {{-- تفاصيل المقالة البارزة --}}
                    <div class="lg:col-span-5 flex flex-col justify-center space-y-4">
                        <div class="flex flex-wrap items-center gap-2.5">
                            @if($featuredArticle->category && $featuredCatTrans)
                                <a href="{{ route('article-categories.show', ['id' => $featuredCatTrans->id, 'slug' => $featuredCatTrans->slug]) }}"
                                   class="px-3 py-1 text-xs font-semibold text-red-400 bg-red-600/10 border border-red-600/30 rounded-lg hover:bg-red-600/20 transition-colors">
                                    🔥 {{ $featuredCatTrans->name ?? $featuredArticle->category->label }}
                                </a>
                            @else
                                <span class="px-3 py-1 text-xs font-semibold text-red-400 bg-red-600/10 border border-red-600/30 rounded-lg">
                                    🔥 {{ __('home.featured') }}
                                </span>
                            @endif

                            <span class="text-xs text-gray-400 font-mono">
                                {{ $featuredTrans->created_at->diffForHumans() }}
                            </span>
                        </div>

                        <h1 class="text-2xl md:text-3xl lg:text-4xl font-extrabold text-white leading-snug hover:text-red-400 transition-colors">
                            <a href="{{ route('articles.show', ['id' => $featuredTrans->id, 'slug' => $featuredTrans->slug]) }}">
                                {{ $featuredTrans->title }}
                            </a>
                        </h1>

                        <p class="text-gray-400 text-xs md:text-sm line-clamp-3 leading-relaxed">
                            {{ Str::limit(strip_tags($featuredTrans->meta_description ?? $featuredTrans->description), 170) }}
                        </p>

                        <div class="pt-2 flex items-center justify-between gap-4">
                            <a href="{{ route('articles.show', ['id' => $featuredTrans->id, 'slug' => $featuredTrans->slug]) }}"
                               class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold text-xs md:text-sm rounded-xl transition-all shadow-md shadow-red-600/20">
                                <span>{{ __('home.read_article') }}</span>
                                <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                            </a>

                            <span class="text-xs text-slate-400 truncate">
                                {{ $featuredTrans->author->name ?? $featuredArticle->author->name ?? __('home.anonymous') }}
                            </span>
                        </div>
                    </div>

                </div>
            </section>
        @endif
    @endif

    {{-- 2️⃣ Body Grid: أحدث المقالات + الشريط الجانبي --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        {{-- شبكة المقالات الحديثة --}}
        <main class="lg:col-span-8 space-y-6">
            <div class="bg-[#0f1526] border border-slate-800 rounded-xl px-5 py-3.5 flex items-center justify-between gap-3">
                <h2 class="text-lg md:text-xl font-bold text-white flex items-center gap-2">
                    <span class="w-2 h-2 bg-red-600 rounded-sm shrink-0"></span>
                    <span>{{ __('home.latest_articles') }}</span>
                </h2>

                <a href="{{ route('article-categories.index') }}"
                   class="text-xs font-semibold text-red-400 hover:text-red-300 transition shrink-0">
                    {{ __('All Categories') }} &rarr;
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @forelse($articles as $article)
                    @php
                        $trans = $article->translations->firstWhere('locale', $currentLocale)
                            ?? $article->translations->first();
                        $catTrans = $article->category?->translations->firstWhere('locale', $currentLocale)
                            ?? $article->category?->translations->first();
                        $excerpt = $trans?->meta_description ?? $trans?->description ?? '';
                    @endphp

                    @if($trans)
                        <article class="group relative bg-[#0f1526] border border-slate-800 hover:border-red-600/60 rounded-xl overflow-hidden shadow-lg transition-all duration-300 flex flex-col justify-between">
                            <div>
                                <div class="relative h-44 w-full overflow-hidden bg-slate-950 [&_img]:!h-full [&_img]:!w-full [&_img]:!object-cover [&_picture]:!h-full [&_picture]:!w-full">
                                    <a href="{{ route('articles.show', ['id' => $trans->id, 'slug' => $trans->slug]) }}" class="block w-full h-full">
                                        @if($article->hasMedia('featured_imgs'))
                                            {{ $article->getFirstMedia('featured_imgs')->img('', [
                                                'class' => 'w-full h-full object-cover opacity-85 group-hover:opacity-100 group-hover:scale-105 transition-all duration-500',
                                                'alt'   => $trans->title,
                                            ]) }}
                                        @else
                                            <img src="{{ asset('images/default-article.jpg') }}"
                                                 alt="{{ $trans->title }}"
                                                 loading="lazy"
                                                 class="w-full h-full object-cover opacity-85 group-hover:opacity-100 group-hover:scale-105 transition-all duration-500">
                                        @endif
                                    </a>
                                    <div class="absolute inset-0 bg-gradient-to-t from-[#0f1526] via-transparent to-transparent pointer-events-none"></div>

                                    @if($article->category && $catTrans)
                                        <a href="{{ route('article-categories.show', ['id' => $catTrans->id, 'slug' => $catTrans->slug]) }}"
                                           class="absolute top-3 left-3 rtl:left-auto rtl:right-3 px-2.5 py-1 text-[10px] font-mono text-gray-200 bg-slate-950/85 hover:bg-red-600 hover:text-white backdrop-blur-md rounded-lg border border-slate-800 transition-colors z-10">
                                            {{ $catTrans->name ?? $article->category->label }}
                                        </a>
                                    @endif

                                    <span class="absolute top-3 right-3 rtl:right-auto rtl:left-3 px-2.5 py-1 text-[10px] font-mono text-gray-300 bg-slate-950/85 backdrop-blur-md rounded-lg border border-slate-800">
                                        {{ $trans->created_at->format('M j, Y') }}
                                    </span>
                                </div>

                                <div class="p-4">
                                    <h3 class="text-base font-bold text-gray-200 group-hover:text-red-500 transition-colors line-clamp-2">
                                        <a href="{{ route('articles.show', ['id' => $trans->id, 'slug' => $trans->slug]) }}">
                                            {{ $trans->title }}
                                        </a>
                                    </h3>

                                    <p class="text-xs text-gray-400 mt-1.5 line-clamp-2 leading-relaxed">
                                        {{ $excerpt ? Str::limit(strip_tags($excerpt), 120) : __('No description provided.') }}
                                    </p>
                                </div>
                            </div>

                            <div class="p-4 pt-3 border-t border-slate-800/70 flex items-center justify-between text-xs text-slate-400">
                                <span class="font-mono truncate max-w-[140px]">
                                    ✍️ {{ $trans->author->name ?? $article->author->name ?? __('home.anonymous') }}
                                </span>

                                <a href="{{ route('articles.show', ['id' => $trans->id, 'slug' => $trans->slug]) }}"
                                   class="text-red-500 font-semibold flex items-center gap-1 shrink-0 group-hover:translate-x-0.5 transition-transform">
                                    <span>{{ __('home.read_more') }}</span>
                                    <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </div>
                        </article>
                    @endif
                @empty
                    <div class="col-span-full py-12 text-center bg-[#0f1526] border border-slate-800 rounded-xl">
                        <p class="text-sm text-gray-400">{{ __('home.no_articles_found') }}</p>
                    </div>
                @endforelse
            </div>
        </main>

        {{-- 3️⃣ Sidebar --}}
        <aside class="lg:col-span-4 space-y-6">

            {{-- الفئات النشطة --}}
            <div class="bg-[#0f1526] border border-slate-800 rounded-xl p-5 shadow-md">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3 mb-4">
                    <h3 class="text-base font-bold text-white flex items-center gap-2">
                        <span class="w-2 h-2 bg-red-600 rounded-sm"></span>
                        <span>{{ __('home.categories') }}</span>
                    </h3>
                    <a href="{{ route('article-categories.index') }}" class="text-xs text-red-400 hover:underline">
                        {{ __('All') }}
                    </a>
                </div>

                <div class="flex flex-wrap gap-2">
                    @foreach($categories as $category)
                        @php
                            $catTrans = $category->translations->firstWhere('locale', $currentLocale)
                                ?? $category->translations->first();
                        @endphp

                        @if($catTrans)
                            <a href="{{ route('article-categories.show', ['id' => $catTrans->id, 'slug' => $catTrans->slug]) }}"
                               class="px-3 py-1.5 text-xs font-medium bg-slate-900 hover:bg-red-600/15 hover:border-red-600/50 text-gray-300 hover:text-red-400 border border-slate-800 rounded-lg transition-all flex items-center gap-1.5">
                                <span>{{ $catTrans->name ?? $category->label }}</span>
                                <span class="text-[10px] font-mono text-slate-400">({{ $category->published_articles_count ?? 0 }})</span>
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- روابط سريعة لأقسام المنصة --}}
            <div class="bg-[#0f1526] border border-slate-800 rounded-xl p-5 shadow-md space-y-2.5">
                <h3 class="text-base font-bold text-white border-b border-slate-800 pb-3 mb-3 flex items-center gap-2">
                    <span class="w-2 h-2 bg-red-600 rounded-sm"></span>
                    <span>{{ __('Explore Archives') }}</span>
                </h3>

                <a href="{{ route('media-summaries.index') }}"
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-lg bg-slate-900 hover:bg-slate-800/90 border border-slate-800 text-xs font-semibold text-gray-200 transition">
                    <span>🎬 {{ __('Media Summaries') }}</span>
                    <span class="text-red-400">&rarr;</span>
                </a>

                <a href="{{ route('books.index') }}"
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-lg bg-slate-900 hover:bg-slate-800/90 border border-slate-800 text-xs font-semibold text-gray-200 transition">
                    <span>📖 {{ __('Books') }}</span>
                    <span class="text-red-400">&rarr;</span>
                </a>

                <a href="{{ route('games.index') }}"
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-lg bg-slate-900 hover:bg-slate-800/90 border border-slate-800 text-xs font-semibold text-gray-200 transition">
                    <span>🎮 {{ __('Games') }}</span>
                    <span class="text-red-400">&rarr;</span>
                </a>
            </div>

            {{-- صندوق النشرة البريدية --}}
            <div class="bg-[#0f1526] border border-slate-800 rounded-xl p-5 shadow-md">
                <h3 class="text-base font-bold text-white mb-1.5 flex items-center gap-2">
                    <span class="w-2 h-2 bg-red-600 rounded-sm"></span>
                    <span>{{ __('home.newsletter.title') }}</span>
                </h3>
                <p class="text-xs text-gray-400 mb-4 leading-relaxed">{{ __('home.newsletter.desc') }}</p>
                <form action="#" method="POST" class="space-y-3">
                    @csrf
                    <input type="email" name="email" required
                           placeholder="{{ __('home.newsletter.email_placeholder') }}"
                           class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-lg text-xs text-white placeholder-slate-500 focus:outline-none focus:border-red-500">
                    <button type="submit"
                            class="w-full py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold text-xs rounded-lg transition-colors">
                        {{ __('home.newsletter.subscribe') }}
                    </button>
                </form>
            </div>

        </aside>

    </div>

</div>
@endsection