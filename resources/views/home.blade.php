@extends('layouts.main')

@section('title', __('home.title', ['default' => 'Home - Latest News & Articles']))

@push('styles')
    {{-- إضافة أي أنماط خاصة بالصفحة الرئيسية إن وجدت --}}
@endpush

@section('meta_tags')
    @vite(['resources/css/home.css'])
@endsection

@section('content')
<div class="container mx-auto px-4 py-8">

    {{-- 1️⃣ Hero Section: المقالة البارزة (Featured Article) --}}
    @if(isset($featuredArticle) && $featuredArticle)
        @php
            $featuredTrans = $featuredArticle->translations->where('language_code', app()->getLocale())->first() 
                            ?? $featuredArticle->translations->first();
            
            // جلب ترجمة الفئة للمقالة البارزة
            $featuredCatTrans = $featuredArticle->category?->translations->where('language_code', app()->getLocale())->first() 
                               ?? $featuredArticle->category?->translations->first();
        @endphp

        @if($featuredTrans)
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 border border-slate-800 mb-12 shadow-2xl">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center p-6 md:p-10">
                    
                    {{-- صورة المقالة البارزة --}}
                    <div class="lg:col-span-7 overflow-hidden rounded-xl h-[320px] md:h-[420px] w-full relative [&_img]:!h-full [&_img]:!w-full [&_img]:!object-cover [&_picture]:!h-full [&_picture]:!w-full">
                        @if($featuredArticle->hasMedia('featured_imgs'))
                            {{ $featuredArticle->getFirstMedia('featured_imgs')->img('', [
                                'class' => 'transform hover:scale-105 transition-transform duration-500 rounded-xl',
                                'alt'   => $featuredTrans->title,
                            ]) }}
                        @else
                            <img src="{{ asset('images/default-category.jpg') }}" 
                                 alt="{{ $featuredTrans->title }}" 
                                 class="w-full h-full object-cover rounded-xl transform hover:scale-105 transition-transform duration-500">
                        @endif
                    </div>

                    {{-- تفاصيل المقالة البارزة --}}
                    <div class="lg:col-span-5 flex flex-col justify-center space-y-4">
                        <div class="flex items-center gap-3">
                            {{-- رابط الفئة للمقالة البارزة مع الـ Slug --}}
                            @if($featuredArticle->category)
                                <a href="{{ route('categories.show', ['id' => $featuredArticle->category->id, 'slug' => $featuredCatTrans->slug ?? '']) }}" 
                                   class="px-3 py-1 text-xs font-semibold uppercase tracking-wider text-red-400 bg-red-500/10 border border-red-500/20 rounded-full hover:bg-red-500/20 transition-colors">
                                    🔥 {{ $featuredCatTrans->title ?? $featuredArticle->category->sign }}
                                </a>
                            @else
                                <span class="px-3 py-1 text-xs font-semibold uppercase tracking-wider text-red-400 bg-red-500/10 border border-red-500/20 rounded-full">
                                    🔥 {{ __('home.featured') }}
                                </span>
                            @endif

                            <span class="text-xs text-gray-400">
                                {{ $featuredArticle->created_at->diffForHumans() }}
                            </span>
                        </div>

                        <h1 class="text-2xl md:text-4xl font-extrabold capitalize text-white leading-snug hover:text-red-400 transition-colors">
                            <a href="{{ route('articles.show', ['id' => $featuredTrans->id, 'slug' => $featuredTrans->slug]) }}">
                                {{ $featuredTrans->title }}
                            </a>
                        </h1>

                        <p class="text-gray-400 first-letter:uppercase text-sm md:text-base line-clamp-3 leading-relaxed">
                            {{ Str::limit(strip_tags($featuredTrans->description), 160) }}
                        </p>

                        <div class="pt-2">
                            <a href="{{ route('articles.show', ['id' => $featuredTrans->id, 'slug' => $featuredTrans->slug]) }}" 
                               class="inline-flex items-center gap-2 px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold text-sm rounded-xl transition-all shadow-lg shadow-red-600/30">
                                <span>{{ __('home.read_article') }}</span>
                                <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        @endif
    @endif

    {{-- 2️⃣ Body Grid: المقالات + الشريط الجانبي (Sidebar) --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        {{-- قسم شبكة المقالات (Main Articles Grid) --}}
        <main class="lg:col-span-8">
            <div class="flex items-center justify-between border-b border-slate-800 pb-4 mb-8">
                <h2 class="text-2xl  font-bold text-white flex items-center gap-2">
                    <span class="w-2.5 h-6 bg-red-600 rounded-full inline-block"></span>
                    {{ __('home.latest_articles') }}
                </h2>
            </div>

            {{-- قائمة المقالات --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @forelse($articles as $article)
                    @php
                        $trans = $article->translations->where('language_code', app()->getLocale())->first() 
                                ?? $article->translations->first();

                        // ترجمة الفئة للمقالة الحالية
                        $catTrans = $article->category?->translations->where('language_code', app()->getLocale())->first() 
                                   ?? $article->category?->translations->first();
                    @endphp

                    @if($trans)
                        <article class="bg-[#1e293b]/60 border border-slate-800/80 rounded-2xl overflow-hidden hover:border-slate-700 transition-all group flex flex-col justify-between">
                            <div>
                                {{-- غلاف المقالة --}}
                                <div class="relative overflow-hidden aspect-video [&_img]:!h-full [&_img]:!w-full [&_img]:!object-cover [&_picture]:!h-full [&_picture]:!w-full">
                                    <a href="{{ route('articles.show', ['id' => $trans->id, 'slug' => $trans->slug]) }}" class="block w-full h-full">
                                        @if($article->hasMedia('featured_imgs'))
                                            {{ $article->getFirstMedia('featured_imgs')->img('', [
                                                'class' => 'w-full h-full object-cover group-hover:scale-105 transition-transform duration-300',
                                                'alt'   => $trans->title,
                                            ]) }}
                                        @else
                                            <img src="{{ asset('images/default-article.jpg') }}" 
                                                 alt="{{ $trans->title }}" 
                                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                        @endif
                                    </a>

                                    {{-- شارة الفئة قابلة للنقر مع الـ Slug --}}
                                    @if($article->category)
                                        <a href="{{ route('categories.show', ['id' => $catTrans->id, 'slug' => $catTrans->slug ?? '']) }}" 
                                           class="absolute top-3 left-3 rtl:left-auto rtl:right-3 px-2.5 py-1 text-xs font-medium text-white bg-slate-900/80 hover:bg-red-600/90 backdrop-blur-md rounded-lg border border-slate-700 transition-colors z-10">
                                            {{ $catTrans->name ?? $article->category->sign }}
                                        </a>
                                    @endif
                                </div>

                                {{-- محتوى الكارت --}}
                                <div class="p-5">
                                    <div class="flex items-center text-xs text-gray-400 gap-3 mb-3">
                                        <span>{{ $article->created_at->format('M d, Y') }}</span>
                                        <span>•</span>
                                        <span>{{ $article->views_count ?? 0 }} {{ __('home.views') }}</span>
                                    </div>

                                    <h3 class="text-lg capitalize font-bold text-white group-hover:text-red-400 transition-colors line-clamp-2 mb-2">
                                        <a href="{{ route('articles.show', ['id' => $trans->id, 'slug' => $trans->slug]) }}">
                                            {{ $trans->title }}
                                        </a>
                                    </h3>

                                    <p class="text-gray-400 first-letter:uppercase text-xs leading-relaxed line-clamp-3 mb-4">
                                        {{ Str::limit(strip_tags($trans->description), 120) }}
                                    </p>
                                </div>
                            </div>

                            {{-- أسفل الكارت --}}
                            <div class="p-5 pt-0 border-t border-slate-800/40 mt-auto flex items-center justify-between text-xs text-gray-400">
                                <span class="font-medium text-slate-300">{{ $article->author->name ?? __('home.anonymous') }}</span>
                                <a href="{{ route('articles.show', ['id' => $trans->id, 'slug' => $trans->slug]) }}" 
                                   class="text-red-400 font-semibold group-hover:underline flex items-center gap-1">
                                    {{ __('home.read_more') }} &rarr;
                                </a>
                            </div>
                        </article>
                    @endif
                @empty
                    <div class="col-span-2 text-center py-12 bg-[#1e293b]/30 rounded-2xl border border-slate-800">
                        <p class="text-gray-400">{{ __('home.no_articles_found') }}</p>
                    </div>
                @endforelse
            </div>

            {{-- الترقيم (Pagination) --}}
            <div class="mt-8">
                {{ $articles->links() }}
            </div>
        </main>

        {{-- 3️⃣ Sidebar: الشريط الجانبي --}}
        <aside class="lg:col-span-4 space-y-8">
            
            {{-- صندوق الفئات المتاحة --}}
            <div class="bg-[#1e293b]/60 border border-slate-800 rounded-2xl p-6">
                <h3 class="text-lg font-bold text-white mb-4 border-b border-slate-800 pb-3 flex items-center gap-2">
                    📌 {{ __('home.categories') }}
                </h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($categories as $category)
                        @php
                            // جلب ترجمة الفئة بناءً على اللغة الحالية
                            $catTrans = $category->translations->where('language_code', app()->getLocale())->first() 
                                       ?? $category->translations->first();
                        @endphp
                        
                        {{-- تحديث رابط الفئة ليحتوي على الـ id والـ slug المترجم --}}
                        <a href="{{ route('categories.show', ['id' => $category->id, 'slug' => $catTrans->slug ?? '']) }}" 
                           class="px-3 py-1.5 text-xs font-medium bg-slate-800/80 hover:bg-red-600/20 hover:border-red-500/40 text-gray-300 hover:text-red-400 border border-slate-700/60 rounded-xl transition-all">
                            {{ $catTrans->title ?? $category->sign }}
                            <span class="text-slate-500 text-[10px] ml-1">({{ $category->articles_count ?? 0 }})</span>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- صندوق النشرة البريدية --}}
            <div class="bg-gradient-to-br from-red-950/40 via-slate-900 to-slate-900 border border-red-500/20 rounded-2xl p-6">
                <h3 class="text-lg font-bold text-white mb-2">📬 {{ __('home.newsletter.title') }}</h3>
                <p class="text-xs text-gray-400 mb-4">{{ __('home.newsletter.desc') }}</p>
                <form action="#" method="POST" class="space-y-3">
                    @csrf
                    <input type="email" name="email" required placeholder="{{ __('home.newsletter.email_placeholder') }}" 
                           class="w-full px-4 py-2.5 bg-slate-950/80 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-red-500">
                    <button type="submit" class="w-full py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold text-xs rounded-xl transition-colors">
                        {{ __('home.newsletter.subscribe') }}
                    </button>
                </form>
            </div>

        </aside>

    </div>

</div>
@endsection