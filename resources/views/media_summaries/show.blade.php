{{--
@extends('layouts.main')
@section('title', $media_trans->meta_title ?? $media_trans->title)

@section('meta_tags')
    <meta name="description" content="{{ $media_trans->meta_description ?? Str::limit(strip_tags($media_trans->synopsis), 160) }}">
    <meta property="og:title" content="{{ $media_trans->meta_title ?? $media_trans->title }}">
    <meta property="og:description" content="{{ $media_trans->meta_description ?? Str::limit(strip_tags($media_trans->synopsis), 160) }}">
    <meta property="og:type" content="video.movie">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:locale" content="{{ $media_trans->locale ?? 'en' }}">
    <meta property="og:site_name" content="Crime Chronicles">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $media_trans->meta_title ?? $media_trans->title }}">
    <meta name="twitter:description" content="{{ $media_trans->meta_description ?? Str::limit(strip_tags($media_trans->synopsis), 160) }}">

    <link rel="canonical" href="{{ url()->current() }}">

    @if($media_trans->mediaSummary->translations ?? false)
        @foreach($media_trans->mediaSummary->translations as $translation)
            <link rel="alternate"
                hreflang="{{ $translation->locale }}" 
                href="{{ route('media-summaries.show', ['id' => $translation->id, 'slug' => $translation->slug]) }}">
        @endforeach
    @endif

    @vite(['resources/css/show.css'])
@endsection

@push('styles')
<style>
    .article-body h1, .article-body h2, .article-body h3, .article-body h4 {
        text-transform: capitalize;
    }
</style>
@endpush

@section('content')
    <div class="reading-progress" id="readingProgress"></div>
    
    <div class="container mx-auto px-4 py-12 max-w-3xl">
        <header class="mb-8 pb-6 border-b border-slate-800">
            <div class="flex items-center gap-2 mb-4">
                <span class="bg-indigo-950 text-indigo-400 border border-indigo-800 px-2.5 py-1 rounded-md text-xs font-bold uppercase tracking-wider">
                    {{ $media_trans->mediaSummary->type }}
                </span>
                @if($media_trans->mediaSummary->release_year)
                    <span class="text-slate-400 text-xs font-mono">• {{ $media_trans->mediaSummary->release_year }}</span>
                @endif
                @if($media_trans->mediaSummary->imdb_rating)
                    <span class="text-amber-400 text-xs font-bold flex items-center gap-1">
                        ★ IMDb {{ $media_trans->mediaSummary->imdb_rating }}
                    </span>
                @endif
            </div>

            <h1 class="text-3xl capitalize md:text-4xl lg:text-5xl font-bold text-white mb-6 leading-tight">
                {{ $media_trans->title }}
            </h1>

            <!-- الصورة الرئيسية المميزة (featured_imgs) -->
            @if($media_trans->mediaSummary->hasMedia('featured_imgs'))
                @php
                    $posterMedia = $media_trans->mediaSummary->getFirstMedia('featured_imgs');
                @endphp
                <div class="mb-6 rounded-2xl overflow-hidden border border-slate-800 shadow-xl max-h-[450px] flex justify-center bg-slate-900">
                    <img src="{{ $posterMedia->getUrl() }}" 
                         srcset="{{ $posterMedia->getSrcset() }}"
                         sizes="(max-width: 768px) 100vw, 800px"
                         alt="{{ $media_trans->title }}" 
                         class="w-full object-cover max-h-[450px]"
                         loading="lazy">
                </div>
            @endif

            <div class="flex flex-wrap items-center justify-between gap-4 text-slate-400 text-sm">
                <div class="flex flex-wrap items-center gap-x-4 gap-y-2">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-1.5 text-red-600 rtl:ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span>Added: {{ $media_trans->created_at->format('F j, Y') }}</span>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-1.5 text-red-600 rtl:ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <span>{{ $media_trans->views }} views</span>
                    </div>
                </div>
            </div>
        </header>

        @if($media_trans->synopsis)
            <section class="mb-10">
                <h2 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-red-600"></span>
                    Synopsis
                </h2>
                <div class="article-body text-slate-200 leading-relaxed space-y-4">
                    {!! $media_trans->synopsis !!}
                </div>
            </section>
        @endif

        @if($media_trans->review_analysis)
            <section class="mb-10 bg-slate-900/40 border border-slate-800/80 rounded-2xl p-6">
                <h2 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                    Review & Forensic Analysis
                </h2>
                <div class="article-body text-slate-200 leading-relaxed space-y-4">
                    {!! $media_trans->review_analysis !!}
                </div>
            </section>
        @endif

        <footer class="mt-8 pt-6 border-t border-slate-800">
            <div class="flex flex-wrap items-center justify-between gap-6 bg-slate-900/20 p-4 rounded-xl">
                <div class="flex items-center flex-wrap gap-2">
                    <span class="text-slate-500 text-xs font-medium">Share:</span>
                    <button onclick="shareOnTwitter()" class="px-2.5 py-1 text-xs rounded bg-slate-800 text-slate-400 hover:bg-slate-700 hover:text-white transition">Twitter</button>
                    <button onclick="shareOnFacebook()" class="px-2.5 py-1 text-xs rounded bg-slate-800 text-slate-400 hover:bg-slate-700 hover:text-white transition">Facebook</button>
                    <button onclick="copyLink()" class="px-2.5 py-1 text-xs rounded bg-slate-800 text-slate-400 hover:bg-slate-700 hover:text-white transition">Copy Link</button>
                </div>
            </div>
        </footer>

        <!-- قسم التعليقات الموحد للوسائط -->
        <div class="mt-12">
            <livewire:comments-section :model="$media_trans->mediaSummary" />
        </div>
    </div>
@endsection

@push('scripts')
<script>
    window.addEventListener('scroll', function() {
        const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
        const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
        const scrolled = (winScroll / height) * 100;
        document.getElementById('readingProgress').style.width = scrolled + '%';
    });
    
    function shareOnTwitter() {
        const url = encodeURIComponent(window.location.href);
        const text = encodeURIComponent('{{ $media_trans->title }}');
        window.open(`https://twitter.com/intent/tweet?url=${url}&text=${text}`, '_blank');
    }
    
    function shareOnFacebook() {
        const url = encodeURIComponent(window.location.href);
        window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank');
    }
    
    function copyLink() {
        navigator.clipboard.writeText(window.location.href).then(() => {
            alert('Link copied to clipboard!');
        });
    }
</script>
@endpush
--}}


@extends('layouts.main')
@section('title', $media_trans->meta_title ?? $media_trans->title)

@section('meta_tags')
    <meta name="description" content="{{ $media_trans->meta_description ?? Str::limit(strip_tags($media_trans->synopsis), 160) }}">
    <meta property="og:title" content="{{ $media_trans->meta_title ?? $media_trans->title }}">
    <meta property="og:description" content="{{ $media_trans->meta_description ?? Str::limit(strip_tags($media_trans->synopsis), 160) }}">
    <meta property="og:type" content="video.movie">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:locale" content="{{ $media_trans->locale ?? 'en' }}">
    <meta property="og:site_name" content="Crime Chronicles">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $media_trans->meta_title ?? $media_trans->title }}">
    <meta name="twitter:description" content="{{ $media_trans->meta_description ?? Str::limit(strip_tags($media_trans->synopsis), 160) }}">

    <link rel="canonical" href="{{ url()->current() }}">

    @if($media_trans->mediaSummary->translations ?? false)
        @foreach($media_trans->mediaSummary->translations as $translation)
            <link rel="alternate"
                hreflang="{{ $translation->locale }}" 
                href="{{ route('media-summaries.show', ['id' => $translation->id, 'slug' => $translation->slug]) }}">
        @endforeach
    @endif

    @vite(['resources/css/show.css'])
@endsection

@push('styles')
<style>
    .article-body h1, .article-body h2, .article-body h3, .article-body h4 {
        text-transform: capitalize;
    }
</style>
@endpush

@section('content')
    <div class="reading-progress" id="readingProgress"></div>
    
    <div class="container mx-auto px-4 py-12 max-w-3xl">
        <header class="mb-8 pb-6 border-b border-slate-800">
            <div class="flex items-center gap-2 mb-4">
                <span class="bg-indigo-950 text-indigo-400 border border-indigo-800 px-2.5 py-1 rounded-md text-xs font-bold uppercase tracking-wider">
                    {{ $media_trans->mediaSummary->type }}
                </span>
                @if($media_trans->mediaSummary->release_year)
                    <span class="text-slate-400 text-xs font-mono">• {{ $media_trans->mediaSummary->release_year }}</span>
                @endif
                @if($media_trans->mediaSummary->imdb_rating)
                    <span class="text-amber-400 text-xs font-bold flex items-center gap-1">
                        ★ IMDb {{ $media_trans->mediaSummary->imdb_rating }}
                    </span>
                @endif
            </div>

            <h1 class="text-3xl capitalize md:text-4xl lg:text-5xl font-bold text-white mb-6 leading-tight">
                {{ $media_trans->title }}
            </h1>

            <!-- الصورة الرئيسية المميزة (featured_imgs) -->
            @if($media_trans->mediaSummary->hasMedia('featured_imgs'))
                @php
                    $posterMedia = $media_trans->mediaSummary->getFirstMedia('featured_imgs');
                @endphp
                <div class="mb-6 rounded-2xl overflow-hidden border border-slate-800 shadow-xl max-h-[450px] flex justify-center bg-slate-900">
                    <img src="{{ $posterMedia->getUrl() }}" 
                         srcset="{{ $posterMedia->getSrcset() }}"
                         sizes="(max-width: 768px) 100vw, 800px"
                         alt="{{ $media_trans->title }}" 
                         class="w-full object-cover max-h-[450px]"
                         loading="lazy">
                </div>
            @endif

            <div class="flex flex-wrap items-center justify-between gap-4 text-slate-400 text-sm">
                <div class="flex flex-wrap items-center gap-x-4 gap-y-2">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-1.5 text-red-600 rtl:ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span>Added: {{ $media_trans->created_at->format('F j, Y') }}</span>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-1.5 text-red-600 rtl:ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <span>{{ $media_trans->views }} views</span>
                    </div>
                </div>
            </div>
        </header>

        @if($media_trans->synopsis)
            <section class="mb-10">
                <h2 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-red-600"></span>
                    Synopsis
                </h2>
                <div class="article-body text-slate-200 leading-relaxed space-y-4">
                    {!! $media_trans->synopsis !!}
                </div>
            </section>
        @endif

        @if($media_trans->review_analysis)
            <section class="mb-10 bg-slate-900/40 border border-slate-800/80 rounded-2xl p-6">
                <h2 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                    Review & Forensic Analysis
                </h2>
                <div class="article-body text-slate-200 leading-relaxed space-y-4">
                    {!! $media_trans->review_analysis !!}
                </div>
            </section>
        @endif

        <div class="mt-8 pt-6 border-t border-slate-800">
            <div class="flex flex-wrap items-center justify-between gap-6 bg-slate-900/20 p-4 rounded-xl">
                <div class="flex items-center flex-wrap gap-2">
                    <span class="text-slate-500 text-xs font-medium">Share:</span>
                    <button onclick="shareOnTwitter()" class="px-2.5 py-1 text-xs rounded bg-slate-800 text-slate-400 hover:bg-slate-700 hover:text-white transition">Twitter</button>
                    <button onclick="shareOnFacebook()" class="px-2.5 py-1 text-xs rounded bg-slate-800 text-slate-400 hover:bg-slate-700 hover:text-white transition">Facebook</button>
                    <button onclick="copyLink()" class="px-2.5 py-1 text-xs rounded bg-slate-800 text-slate-400 hover:bg-slate-700 hover:text-white transition">Copy Link</button>
                </div>
            </div>
        </div>

        <!-- قسم التعليقات الموحد للوسائط -->
        <div class="mt-8">
            <livewire:comments-section :model="$media_trans->mediaSummary" />
        </div>
    </div>
@endsection

@push('scripts')
<script>
    window.addEventListener('scroll', function() {
        const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
        const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
        const scrolled = (winScroll / height) * 100;
        document.getElementById('readingProgress').style.width = scrolled + '%';
    });
    
    function shareOnTwitter() {
        const url = encodeURIComponent(window.location.href);
        const text = encodeURIComponent('{{ $media_trans->title }}');
        window.open(`https://twitter.com/intent/tweet?url=${url}&text=${text}`, '_blank');
    }
    
    function shareOnFacebook() {
        const url = encodeURIComponent(window.location.href);
        window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank');
    }
    
    function copyLink() {
        navigator.clipboard.writeText(window.location.href).then(() => {
            alert('Link copied to clipboard!');
        });
    }
</script>
@endpush