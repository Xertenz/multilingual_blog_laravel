{{-- resources/views/articles/show.blade.php --}}
@extends('layouts.main')
@section('title', $article_trans->meta_title ?? $article_trans->title)

@section('meta_tags')
    @php
        $metaDesc = $article_trans->meta_description ?? Str::limit(strip_tags($article_trans->description), 160);
    @endphp
    <!-- Meta Tags للزوار ومحركات البحث -->
    <meta name="description" content="{{ $metaDesc }}">
    <!-- Open Graph للشبكات الاجتماعية -->
    <meta property="og:title" content="{{ $article_trans->meta_title ?? $article_trans->title }}">
    <meta property="og:description" content="{{ $metaDesc }}">
    <meta property="og:type" content="article">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:locale" content="{{ $article_trans->locale ?? 'en' }}">
    <meta property="og:site_name" content="Crime Chronicles">

    <!-- Twitter Cards -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $article_trans->meta_title ?? $article_trans->title }}">
    <meta name="twitter:description" content="{{ $metaDesc }}">

    <!-- Canonical URL -->
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Alternate Languages -->
    @if($article_trans->article->translations ?? false)
        @foreach($article_trans->article->translations as $translation)
            <link rel="alternate"
                hreflang="{{ $translation->locale }}"
                href="{{ route('articles.show', ['slug' => $translation->slug, 'id' => $translation->id]) }}">
        @endforeach
    @endif

    @vite(['resources/css/show.css'])
@endsection

@push('styles')
<style>
    /* تطبيق Capitalize على العناوين فقط داخل حاوي المحتوى */
    .article-body h1,
    .article-body h2,
    .article-body h3,
    .article-body h4,
    .article-body h5,
    .article-body h6 {
        text-transform: capitalize;
    }
</style>
@endpush

@section('content')

    <!-- شريط تقدم القراءة -->
    <div class="reading-progress" id="readingProgress"></div>

    <div class="container mx-auto px-4 py-8 max-w-3xl">

        <!-- ==================== -->
        <!-- رأس المقالة — داخل صندوق موحّد -->
        <!-- ==================== -->
        <header class="bg-[#0f1526] border border-slate-800 rounded-xl px-5 py-5 mb-6 shadow-md">

            <!-- العنوان الرئيسي -->
            <h1 class="text-2xl md:text-3xl lg:text-4xl font-bold text-white mb-4 leading-tight capitalize">
                {{ $article_trans->title }}
            </h1>

            <!-- شريط المعلومات الوصفية -->
            <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-gray-400 text-xs pb-4 border-b border-slate-800">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-1.5 text-red-600 rtl:ml-1.5 rtl:mr-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                    </svg>
                    <a href="{{ route('authors.show', ['id' => $article_trans->user_id, 'tab' => 'articles']) }}" class="hover:text-red-400 transition font-semibold">
                        {{ $article_trans->author->name ?? 'Investigator Team' }}
                    </a>
                </div>

                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-1.5 text-red-600 rtl:ml-1.5 rtl:mr-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span>{{ $article_trans->created_at->format('F j, Y') }}</span>
                </div>

                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-1.5 text-red-600 rtl:ml-1.5 rtl:mr-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ ceil(str_word_count(strip_tags($article_trans->description)) / 200) }} min read</span>
                </div>
            </div>

            <!-- أزرار الإجراءات (حفظ + متابعة الكاتب) -->
            <div class="flex items-center flex-wrap gap-2 pt-4">
                @if($article_trans->article)
                    <x-save-button type="article" :model="$article_trans->article" />
                @endif

                @auth
                    @if($article_trans->article && $article_trans->article->author && auth()->id() !== $article_trans->article->author->id)
                        <x-follow-button type="author" :model="$article_trans->article->author" />
                    @endif
                @endauth
            </div>
        </header>

        <!-- ==================== -->
        <!-- نصوص ومحتوى المقال الرئيسي -->
        <!-- ==================== -->
        <article class="article-body first-letter:uppercase article-description text-gray-200 leading-relaxed mb-8">
            {!! $article_trans->description !!}
        </article>

        <!-- ==================== -->
        <!-- أدوات التفاعل والبيانات السفلية -->
        <!-- ==================== -->
        <div class="mt-12 bg-[#0f1526] border border-slate-800 rounded-xl p-6 space-y-6">

            <!-- السطر الأول: الإعجابات ومتابعة الفئة -->
            <div class="flex flex-wrap items-center justify-between gap-4 pb-4 border-b border-slate-800/60">

                <!-- أزرار الـ Reactions (إبهام أعلى / إبهام أسفل) -->
                <div class="flex items-center bg-slate-900/80 rounded-xl p-1 border border-slate-800">
                    @auth
                        @php
                            $userReaction = $article_trans->article->reactions->where('user_id', auth()->id())->first();
                            $hasLiked = $userReaction && $userReaction->type === 'like';
                            $hasDisliked = $userReaction && $userReaction->type === 'dislike';
                        @endphp
                    @else
                        @php $hasLiked = false; $hasDisliked = false; @endphp
                    @endauth

                    <!-- زر الإعجاب (إبهام أعلى) -->
                    <button id="article-like-{{$article_trans->article->id}}"
                            onclick="articleLike(event, {{$article_trans->article->id}})"
                            data-active="{{ $hasLiked ? 'true' : 'false' }}"
                            title="{{ __('Like') }}"
                            class="flex items-center gap-2 px-3.5 py-2 text-xs font-semibold rounded-lg transition duration-200 border
                            {{ $hasLiked
                                ? 'bg-emerald-950/80 text-emerald-400 border-emerald-800/60'
                                : 'border-transparent text-gray-300 hover:text-emerald-400 hover:bg-slate-700/60'
                            }}">

                        {{-- أيقونة الإعجاب: outline + solid --}}
                        <x-heroicon-o-hand-thumb-up id="article-like-icon-outline-{{$article_trans->article->id}}" class="w-5 h-5 {{ $hasLiked ? 'hidden' : '' }}" />
                        <x-heroicon-s-hand-thumb-up id="article-like-icon-solid-{{$article_trans->article->id}}" class="w-5 h-5 {{ $hasLiked ? '' : 'hidden' }}" />

                        <span id="article-like-count-{{$article_trans->article->id}}" class="font-mono">
                            ({{$article_trans->article->reactions->where('type', 'like')->count()}})
                        </span>
                    </button>

                    <span class="text-slate-600 px-1">|</span>

                    <!-- زر عدم الإعجاب (إبهام أسفل) -->
                    <button id="article-dislike-{{$article_trans->article->id}}"
                            onclick="articleDislike(event, {{$article_trans->article->id}})"
                            data-active="{{ $hasDisliked ? 'true' : 'false' }}"
                            title="{{ __('Dislike') }}"
                            class="flex items-center gap-2 px-3.5 py-2 text-xs font-semibold rounded-lg transition duration-200 border
                            {{ $hasDisliked
                                ? 'bg-rose-950/80 text-rose-400 border-rose-800/60'
                                : 'border-transparent text-gray-300 hover:text-rose-400 hover:bg-slate-700/60'
                            }}">

                        {{-- أيقونة عدم الإعجاب: outline + solid --}}
                        <x-heroicon-o-hand-thumb-down id="article-dislike-icon-outline-{{$article_trans->article->id}}" class="w-5 h-5 {{ $hasDisliked ? 'hidden' : '' }}" />
                        <x-heroicon-s-hand-thumb-down id="article-dislike-icon-solid-{{$article_trans->article->id}}" class="w-5 h-5 {{ $hasDisliked ? '' : 'hidden' }}" />

                        <span id="article-dislike-count-{{$article_trans->article->id}}" class="font-mono">
                            ({{$article_trans->article->reactions->where('type', 'dislike')->count()}})
                        </span>
                    </button>
                </div>

                <!-- قسم فئة المقال -->
                <div class="flex items-center flex-wrap gap-2">
                    <span class="text-gray-500 text-xs font-medium">More in:</span>
                    @php
                        $current_locale = app()->getLocale();
                        $category = $article_trans->article->category;
                        $category_translation = $category?->translations->where('locale', $current_locale)->first();
                        $categoryName = $category_translation ? $category_translation->name : ($category->label ?? '');
                        $categoryId = $category?->id;
                    @endphp

                    @if($category && $category_translation)
                        <div class="inline-flex items-center bg-slate-900/80 rounded-xl p-1.5 border border-slate-800 gap-2">
                            <a href="{{ route('article-categories.show', ['id' => $category_translation->id, 'slug' => $category_translation->slug]) }}"
                                class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-semibold text-red-400 hover:text-red-300 hover:bg-slate-800/80 rounded-lg transition duration-200">
                                <span>{{ $categoryName }}</span>
                            </a>

                            @auth
                                <span class="text-slate-700">|</span>
                                <x-follow-button
                                    type="article_category"
                                    :model="$category"
                                    :follow-text="__('Follow Category')"
                                    :following-text="__('Following')" />
                            @endauth
                        </div>
                    @endif
                </div>
            </div>

            <!-- السطر الثاني: الكلمات الدلالية -->
            @if($article_trans->article->tags->isNotEmpty())
                <div class="flex items-center flex-wrap gap-2 pt-2">
                    <span class="text-slate-500 text-xs font-semibold mr-2 rtl:ml-2">Tags:</span>

                    @foreach($article_trans->article->tags as $tag)
                        @php
                            $current_locale = app()->getLocale();
                            $tag_translation = $tag->translations->where('locale', $current_locale)->first();
                            $tagName = $tag_translation ? $tag_translation->name : $tag->label;
                        @endphp

                        @if ($tag_translation)
                        <a href="{{ route('article-tags.show', ['id' => $tag_translation->id, 'slug' => $tag_translation->slug]) }}" class="flex items-center gap-1 bg-slate-900 text-slate-300 hover:bg-slate-800 hover:text-white px-2.5 py-1 rounded-md text-xs font-medium transition duration-200 border border-slate-800">
                            <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581a1.125 1.125 0 001.591 0l7.182-7.182a1.125 1.125 0 000-1.59l-9.581-9.581A2.25 2.25 0 009.568 3z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z"></path>
                            </svg>
                            <span>{{ $tagName }}</span>
                        </a>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>

        <!-- ==================== -->
        <!-- أزرار المشاركة والتنقل -->
        <!-- ==================== -->
        <div class="mt-6 bg-[#0f1526] border border-slate-800 rounded-xl p-4">
            <div class="flex flex-wrap items-center justify-between gap-4">

                <!-- أزرار المشاركة -->
                <div class="flex items-center flex-wrap gap-2">
                    <span class="text-gray-500 text-xs font-medium">Share:</span>
                    <button onclick="shareOnTwitter()" class="px-2.5 py-1 text-xs rounded bg-slate-900 text-gray-400 hover:bg-slate-800 hover:text-white transition border border-slate-800">Twitter</button>
                    <button onclick="shareOnFacebook()" class="px-2.5 py-1 text-xs rounded bg-slate-900 text-gray-400 hover:bg-slate-800 hover:text-white transition border border-slate-800">Facebook</button>
                    <button onclick="copyLink()" class="px-2.5 py-1 text-xs rounded bg-slate-900 text-gray-400 hover:bg-slate-800 hover:text-white transition border border-slate-800">Copy Link</button>
                </div>

                <!-- أزرار التنقل بين المقالات -->
                <div class="flex items-center flex-wrap gap-2.5 text-xs font-semibold">
                    @if($previousArticle ?? false)
                        <a href="{{ route('articles.show', ['id' => $previousArticle->id, 'slug' => $previousArticle->slug]) }}"
                           title="{{ $previousArticle->title }}"
                           class="group inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-slate-900/90 hover:bg-slate-800 text-gray-300 hover:text-white border border-slate-800 hover:border-red-600/50 transition max-w-[210px]">
                            <svg class="w-4 h-4 shrink-0 text-red-500 rtl:rotate-180 transform group-hover:-translate-x-0.5 rtl:group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                            <span class="truncate">{{ __('Previous Case') }}</span>
                        </a>
                    @else
                        <span class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-slate-900/40 text-slate-600 border border-slate-800/60 cursor-not-allowed">
                            <svg class="w-4 h-4 shrink-0 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                            <span>{{ __('Previous Case') }}</span>
                        </span>
                    @endif

                    @if($nextArticle ?? false)
                        <a href="{{ route('articles.show', ['id' => $nextArticle->id, 'slug' => $nextArticle->slug]) }}"
                           title="{{ $nextArticle->title }}"
                           class="group inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-slate-900/90 hover:bg-slate-800 text-gray-300 hover:text-white border border-slate-800 hover:border-red-600/50 transition max-w-[210px]">
                            <span class="truncate">{{ __('Next Case') }}</span>
                            <svg class="w-4 h-4 shrink-0 text-red-500 rtl:rotate-180 transform group-hover:translate-x-0.5 rtl:group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    @else
                        <span class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-slate-900/40 text-slate-600 border border-slate-800/60 cursor-not-allowed">
                            <span>{{ __('Next Case') }}</span>
                            <svg class="w-4 h-4 shrink-0 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- ==================== -->
        <!-- مقالات ذات صلة في نفس الفئة -->
        <!-- ==================== -->
        @if(isset($relatedArticles) && $relatedArticles->isNotEmpty())
            <section class="mt-8 bg-[#0f1526] border border-slate-800 rounded-xl p-6">
                <div class="flex items-center justify-between gap-2 mb-4 pb-3 border-b border-slate-800/70">
                    <h2 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2 h-2 bg-red-600 rounded-sm"></span>
                        <span>{{ __('Related Articles') }}</span>
                    </h2>

                    @php
                        $currentLocale = app()->getLocale();
                        $catTrans = $article_trans->article->category?->translations
                            ->where('locale', $currentLocale)
                            ->first()
                            ?? $article_trans->article->category?->translations->first();
                    @endphp

                    @if($catTrans)
                        <a href="{{ route('article-categories.show', ['id' => $catTrans->id, 'slug' => $catTrans->slug]) }}"
                        class="text-xs font-semibold text-red-400 hover:text-red-300 transition">
                            {{ __('View All in') }} {{ $catTrans->name }} &rarr;
                        </a>
                    @endif
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($relatedArticles as $relTrans)
                        @php
                            $thumb = $relTrans->article?->getFirstMedia('featured_imgs')
                                ?? $relTrans->article?->getFirstMedia('content_imgs');
                            $thumbUrl = $thumb ? $thumb->getUrl('medium') : null;
                        @endphp

                        <a href="{{ route('articles.show', ['id' => $relTrans->id, 'slug' => $relTrans->slug]) }}"
                        class="group flex items-center gap-3 p-3 rounded-xl bg-slate-900/70 hover:bg-slate-900 border border-slate-800 hover:border-red-600/50 transition">
                            @if($thumbUrl)
                                <img src="{{ $thumbUrl }}"
                                    alt="{{ $relTrans->title }}"
                                    loading="lazy"
                                    class="w-16 h-16 rounded-lg object-cover shrink-0 border border-slate-800">
                            @else
                                <div class="w-16 h-16 rounded-lg bg-slate-800 flex items-center justify-center text-[10px] text-slate-500 shrink-0">
                                    {{ __('No Image') }}
                                </div>
                            @endif

                            <div class="min-w-0 flex-1">
                                <h3 class="text-xs sm:text-sm font-bold text-gray-200 group-hover:text-red-400 transition line-clamp-2">
                                    {{ $relTrans->title }}
                                </h3>
                                <p class="text-[11px] text-slate-400 mt-1 truncate">
                                    {{ $relTrans->author->name ?? __('Investigator Team') }} • {{ $relTrans->created_at->format('M j, Y') }}
                                </p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        <!-- ==================== -->
        <!-- قسم التعليقات -->
        <!-- ==================== -->
        <livewire:comments-section :model="$article_trans->article" />

    </div>

@endsection

@push('scripts')
<script>
    // 1. شريط تقدم القراءة
    window.addEventListener('scroll', function() {
        const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
        const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
        const scrolled = (winScroll / height) * 100;
        document.getElementById('readingProgress').style.width = scrolled + '%';
    });

    // 2. مشاركة المقال
    function shareOnTwitter() {
        const url = encodeURIComponent(window.location.href);
        const text = encodeURIComponent('{{ $article_trans->title }}');
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

    // 3. معرض الصور
    function openLightbox(index) {
        const images = @json($article_trans->article->getMedia('content_imgs')->map(fn($img) => $img->getUrl()));
        alert('Opening evidence image #' + (index + 1));
    }

    async function articleLike(event, id) {
        let type = 'like';
        let url = "{{ route('articles.reactions.toggle') }}";
        let likeBtn = document.getElementById(`article-like-${id}`);
        let dislikeBtn = document.getElementById(`article-dislike-${id}`);

        let likeOutline = document.getElementById(`article-like-icon-outline-${id}`);
        let likeSolid = document.getElementById(`article-like-icon-solid-${id}`);
        let dislikeOutline = document.getElementById(`article-dislike-icon-outline-${id}`);
        let dislikeSolid = document.getElementById(`article-dislike-icon-solid-${id}`);

        try {
            let request = await fetch(url, {
                method: "POST",
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ id, type })
            });
            let data = await request.json();

            document.getElementById(`article-like-count-${id}`).textContent = `(${data.likes})`;
            document.getElementById(`article-dislike-count-${id}`).textContent = `(${data.dislikes})`;

            if (likeBtn.getAttribute('data-active') === 'true') {
                likeBtn.setAttribute('data-active', 'false');
                likeBtn.className = "flex items-center gap-2 px-3.5 py-2 text-xs font-semibold text-gray-300 hover:text-emerald-400 hover:bg-slate-700/60 rounded-lg border border-transparent transition duration-200";

                likeOutline.classList.remove('hidden');
                likeSolid.classList.add('hidden');
            } else {
                likeBtn.setAttribute('data-active', 'true');
                likeBtn.className = "flex items-center gap-2 px-3.5 py-2 text-xs font-semibold rounded-lg transition duration-200 border bg-emerald-950/80 text-emerald-400 border-emerald-800/60";

                likeOutline.classList.add('hidden');
                likeSolid.classList.remove('hidden');

                dislikeBtn.setAttribute('data-active', 'false');
                dislikeBtn.className = "flex items-center gap-2 px-3.5 py-2 text-xs font-semibold text-gray-300 hover:text-rose-400 hover:bg-slate-700/60 rounded-lg border border-transparent transition duration-200";

                dislikeOutline.classList.remove('hidden');
                dislikeSolid.classList.add('hidden');
            }
        } catch(err) {
            console.log('error in article like', err);
        }
    }

    async function articleDislike(event, id) {
        let type = 'dislike';
        let url = "{{ route('articles.reactions.toggle') }}";
        let likeBtn = document.getElementById(`article-like-${id}`);
        let dislikeBtn = document.getElementById(`article-dislike-${id}`);

        let likeOutline = document.getElementById(`article-like-icon-outline-${id}`);
        let likeSolid = document.getElementById(`article-like-icon-solid-${id}`);
        let dislikeOutline = document.getElementById(`article-dislike-icon-outline-${id}`);
        let dislikeSolid = document.getElementById(`article-dislike-icon-solid-${id}`);

        try {
            let request = await fetch(url, {
                method: "POST",
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ id, type })
            });
            let data = await request.json();

            document.getElementById(`article-like-count-${id}`).textContent = `(${data.likes})`;
            document.getElementById(`article-dislike-count-${id}`).textContent = `(${data.dislikes})`;

            if (dislikeBtn.getAttribute('data-active') === 'true') {
                dislikeBtn.setAttribute('data-active', 'false');
                dislikeBtn.className = "flex items-center gap-2 px-3.5 py-2 text-xs font-semibold text-gray-300 hover:text-rose-400 hover:bg-slate-700/60 rounded-lg border border-transparent transition duration-200";

                dislikeOutline.classList.remove('hidden');
                dislikeSolid.classList.add('hidden');
            } else {
                dislikeBtn.setAttribute('data-active', 'true');
                dislikeBtn.className = "flex items-center gap-2 px-3.5 py-2 text-xs font-semibold rounded-lg transition duration-200 border bg-rose-950/80 text-rose-400 border-rose-800/60";

                dislikeOutline.classList.add('hidden');
                dislikeSolid.classList.remove('hidden');

                likeBtn.setAttribute('data-active', 'false');
                likeBtn.className = "flex items-center gap-2 px-3.5 py-2 text-xs font-semibold text-gray-300 hover:text-emerald-400 hover:bg-slate-700/60 rounded-lg border border-transparent transition duration-200";

                likeOutline.classList.remove('hidden');
                likeSolid.classList.add('hidden');
            }
        } catch(err) {
            console.log('error in article dislike', err);
        }
    }

    // 6. التمرير والتمييز البصري للتعليقات
    document.addEventListener("DOMContentLoaded", function() {
        if (window.location.hash) {
            let targetElement = document.querySelector(window.location.hash);

            setTimeout(() => {
                targetElement = document.querySelector(window.location.hash);
                if (targetElement) {
                    targetElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    targetElement.classList.add('ring-2', 'ring-red-500', 'transition', 'duration-500');
                    setTimeout(() => {
                        targetElement.classList.remove('ring-2', 'ring-red-500');
                    }, 2500);
                }
            }, 400);
        }
    });
</script>
@endpush