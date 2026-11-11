{{-- resources/views/articles/show.blade.php --}}
@extends('layouts.main')
@section('title', $article_trans->meta_title ?? $article_trans->title)

@section('meta_tags')
    <!-- Meta Tags للزوار ومحركات البحث -->
    <meta name="description" content="{{ $article_trans->meta_description ?? Str::limit(strip_tags($article_trans->meta_description), 160) }}">
    <!-- Open Graph للشبكات الاجتماعية -->
    <meta property="og:title" content="{{ $article_trans->meta_title ?? $article_trans->title }}">
    <meta property="og:description" content="{{ $article_trans->meta_description ?? Str::limit(strip_tags($article_trans->meta_description), 160) }}">
    <meta property="og:type" content="article">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:locale" content="{{ $article_trans->locale ?? 'en' }}">
    <meta property="og:site_name" content="Crime Chronicles">

    <!-- Twitter Cards -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $article_trans->meta_title ?? $article_trans->title }}">
    <meta name="twitter:description" content="{{ $article_trans->meta_description ?? Str::limit(strip_tags($article_trans->meta_description), 160) }}">

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
                    <a href="{{ route('authors.show', $article_trans->user_id) }}" class="hover:text-red-400 transition font-semibold">
                        {{ $article_trans->user->name ?? 'Investigator Team' }}
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
                @auth
                    @php
                        $isArticleSaved = auth()->user()->savedArticles->contains($article_trans->article_id);
                    @endphp

                    <button type="button"
                            id="save-article-btn-{{ $article_trans->article_id }}"
                            onclick="toggleSavedArticle(event, {{ $article_trans->article_id }})"
                            data-saved="{{ $isArticleSaved ? 'true' : 'false' }}"
                            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200 border
                            {{ $isArticleSaved
                                ? 'bg-indigo-600 text-white border-indigo-500 hover:bg-indigo-700 shadow-sm shadow-indigo-600/20'
                                : 'bg-slate-800 text-gray-300 border-slate-700 hover:bg-slate-700 hover:text-white'
                            }}">

                        <svg id="save-icon-{{ $article_trans->article_id }}"
                            class="w-4 h-4 transition-colors duration-200 {{ $isArticleSaved ? 'fill-current text-white' : 'fill-none' }}"
                            stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z"></path>
                        </svg>

                        <span id="save-text-{{ $article_trans->article_id }}">
                            {{ $isArticleSaved ? __('Saved') : __('Save Article') }}
                        </span>
                    </button>
                @endauth

                @auth
                    @if(auth()->id() !== $article_trans->user_id && $article_trans->user_id)
                        @php
                            $isFollowingAuthor = auth()->user()->followingAuthors->contains($article_trans->user_id);
                        @endphp

                        <button type="button"
                                id="follow-author-btn-{{ $article_trans->user_id }}"
                                onclick="toggleFollowAuthor(event, {{ $article_trans->user_id }})"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition duration-200 border
                                {{ $isFollowingAuthor
                                    ? 'bg-rose-950/80 text-rose-400 border-rose-800/60'
                                    : 'bg-slate-800 text-gray-300 border-slate-700 hover:text-white'
                                }}">
                            <span id="follow-author-text-{{ $article_trans->user_id }}">
                                {{ $isFollowingAuthor ? __('Following') : __('Follow Author') }}
                            </span>
                        </button>
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

            <!-- السطر الأول: الإعجابات وتفضيل الفئة -->
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
                            }}"
                            >

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
                            }}"
                            >

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
                        $category_translation = $article_trans->article->category->translations->where('locale', $current_locale)->first();
                        $categoryName = $category_translation ? $category_translation->name : $article_trans->article->category->label;
                        $categoryId = $article_trans->article->category->id;
                    @endphp

                    @if($category_translation)
                        <div class="inline-flex items-center bg-slate-900/80 rounded-xl p-1 border border-slate-800 gap-1">
                            <a href="{{ route('categories.show', ['id' => $category_translation->id, 'slug' => $category_translation->slug]) }}"
                                class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-semibold text-red-400 hover:text-red-300 hover:bg-slate-700/60 rounded-lg transition duration-200 border border-transparent">
                                <!--
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.776..."></path>
                                </svg>
                                -->
                                <span>{{ $categoryName }}</span>
                            </a>

                            @auth
                                <span class="text-slate-600 px-0.5">|</span>

                                @php
                                    $isCategoryFavorite = Auth::user()->favoriteCategories->contains($categoryId);
                                @endphp

                                <button type="button"
                                        id="fav-category-btn-{{ $categoryId }}"
                                        onclick="toggleFavoriteCategory(event, {{ $categoryId }})"
                                        data-saved="{{ $isCategoryFavorite ? 'true' : 'false' }}"
                                        class="inline-flex items-center justify-center gap-2 px-3.5 py-2 w-[170px] text-xs font-semibold rounded-lg transition duration-200 border
                                        {{ $isCategoryFavorite
                                            ? 'bg-red-950/80 text-red-400 border-red-800/60'
                                            : 'border-transparent text-gray-300 hover:text-red-400 hover:bg-slate-700/60'
                                        }}">

                                    <svg id="fav-category-icon-{{ $categoryId }}"
                                        class="w-5 h-5 transition-colors duration-200 {{ $isCategoryFavorite ? 'fill-current text-red-500' : 'fill-none' }}"
                                        stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"></path>
                                    </svg>

                                    <span id="fav-category-text-{{ $categoryId }}">
                                        {{ $isCategoryFavorite ? __('Following') : __('Follow Category') }}
                                    </span>
                                </button>
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
                        <a href="{{ route('tags.show', ['id' => $tag_translation->id]) }}" class="flex items-center gap-1 bg-slate-900 text-slate-300 hover:bg-slate-800 hover:text-white px-2.5 py-1 rounded-md text-xs font-medium transition duration-200 border border-slate-800">
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

                <!-- أزرار التنقل -->
                <div class="flex gap-4 text-xs font-semibold">
                    @if($previousArticle ?? false)
                    <a href="{{ route('articles.show', $previousArticle->slug) }}" class="text-gray-400 hover:text-red-500 transition flex items-center gap-1">
                        <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Previous Case
                    </a>
                    @endif

                    @if($nextArticle ?? false)
                    <a href="{{ route('articles.show', $nextArticle->slug) }}" class="text-gray-400 hover:text-red-500 transition flex items-center gap-1">
                        Next Case
                        <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                    @endif
                </div>
            </div>
        </div>

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

    // 4. حفظ المقالات وتفضيل الفئات
    async function toggleSavedArticle(event, articleId) {
        event.preventDefault();
        const baseUrl = "{{ route('articles.saved', ':id') }}";
        const url = baseUrl.replace(':id', articleId);

        const btn = document.getElementById(`save-article-btn-${articleId}`);
        const icon = document.getElementById(`save-icon-${articleId}`);
        const textSpan = document.getElementById(`save-text-${articleId}`);

        try {
            let response = await fetch(url, {
                method: "POST",
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) throw new Error('Network response was not ok');
            let data = await response.json();

            if (data.is_saved) {
                btn.setAttribute('data-saved', 'true');
                btn.className = "inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200 border bg-indigo-600 text-white border-indigo-500 hover:bg-indigo-700 shadow-sm shadow-indigo-600/20";
                icon.classList.remove('fill-none');
                icon.classList.add('fill-current', 'text-white');
                textSpan.textContent = "{{ __('Saved') }}";
                showToast('success', "Successfully added to saved!");
            } else {
                btn.setAttribute('data-saved', 'false');
                btn.className = "inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200 border bg-slate-800 text-gray-300 border-slate-700 hover:bg-slate-700 hover:text-white";
                icon.classList.remove('fill-current', 'text-white');
                icon.classList.add('fill-none');
                textSpan.textContent = "{{ __('Save Article') }}";
                showToast('success', "Successfully removed from saved!");
            }
        } catch (error) {
            console.error('Error toggling saved status:', error);
            showToast('error', "Error in the operation with saved articles!");
        }
    }

    async function toggleFavoriteCategory(event, categoryId) {
        event.preventDefault();
        const baseUrl = "{{ route('categories.favorite', ':id') }}";
        const url = baseUrl.replace(':id', categoryId);

        const btn = document.getElementById(`fav-category-btn-${categoryId}`);
        const icon = document.getElementById(`fav-category-icon-${categoryId}`);
        const textSpan = document.getElementById(`fav-category-text-${categoryId}`);

        try {
            let response = await fetch(url, {
                method: "POST",
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) throw new Error('Network response was not ok');
            let data = await response.json();

            if (data.is_favorite) {
                btn.setAttribute('data-saved', 'true');
                btn.className = "inline-flex items-center justify-center gap-2 px-3.5 py-2 w-[170px] text-xs font-semibold rounded-lg transition-all duration-200 border bg-red-950/80 text-red-400 border-red-800/60 hover:bg-red-900/40";
                icon.classList.remove('fill-none');
                icon.classList.add('fill-current', 'text-red-500');
                textSpan.textContent = "{{ __('Following') }}";
                showToast('success', "Successfully added to favorites!");
            } else {
                btn.setAttribute('data-saved', 'false');
                btn.className = "inline-flex items-center justify-center gap-2 px-3.5 py-2 w-[170px] text-xs font-semibold rounded-lg transition-all duration-200 border border-transparent text-gray-300 hover:text-red-400 hover:bg-slate-700/60";
                icon.classList.remove('fill-current', 'text-red-500');
                icon.classList.add('fill-none');
                textSpan.textContent = "{{ __('Follow Category') }}";
                showToast('success', "Successfully removed from favorites!");
            }
        } catch (error) {
            console.error('Error toggling category favorite status:', error);
            showToast('error', "Error in the operation with favorite categories!");
        }
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
                // إلغاء الإعجاب → outline + border transparent
                likeBtn.setAttribute('data-active', 'false');
                likeBtn.className = "flex items-center gap-2 px-3.5 py-2 text-xs font-semibold text-gray-300 hover:text-emerald-400 hover:bg-slate-700/60 rounded-lg border border-transparent transition duration-200";

                likeOutline.classList.remove('hidden');
                likeSolid.classList.add('hidden');
            } else {
                // تفعيل الإعجاب → solid + border ملون
                likeBtn.setAttribute('data-active', 'true');
                likeBtn.className = "flex items-center gap-2 px-3.5 py-2 text-xs font-semibold rounded-lg transition duration-200 border bg-emerald-950/80 text-emerald-400 border-emerald-800/60";

                likeOutline.classList.add('hidden');
                likeSolid.classList.remove('hidden');

                // إلغاء التفاعل المعاكس → outline + border transparent
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
                // إلغاء عدم الإعجاب → outline + border transparent
                dislikeBtn.setAttribute('data-active', 'false');
                dislikeBtn.className = "flex items-center gap-2 px-3.5 py-2 text-xs font-semibold text-gray-300 hover:text-rose-400 hover:bg-slate-700/60 rounded-lg border border-transparent transition duration-200";

                dislikeOutline.classList.remove('hidden');
                dislikeSolid.classList.add('hidden');
            } else {
                // تفعيل عدم الإعجاب → solid + border ملون
                dislikeBtn.setAttribute('data-active', 'true');
                dislikeBtn.className = "flex items-center gap-2 px-3.5 py-2 text-xs font-semibold rounded-lg transition duration-200 border bg-rose-950/80 text-rose-400 border-rose-800/60";

                dislikeOutline.classList.add('hidden');
                dislikeSolid.classList.remove('hidden');

                // إلغاء التفاعل المعاكس → outline + border transparent
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

    // 7. متابعة الكاتب
    async function toggleFollowAuthor(event, authorId) {
        event.preventDefault();
        const baseUrl = "{{ route('authors.follow', ':id') }}";
        const url = baseUrl.replace(':id', authorId);

        const btn = document.getElementById(`follow-author-btn-${authorId}`);
        const textSpan = document.getElementById(`follow-author-text-${authorId}`);

        try {
            let response = await fetch(url, {
                method: "POST",
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) throw new Error('Network response was not ok');
            let data = await response.json();

            if (data.is_following) {
                btn.className = "inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition duration-200 border bg-rose-950/80 text-rose-400 border-rose-800/60";
                textSpan.textContent = "{{ __('Following') }}";
                showToast('success', "Successfully followed the author!");
            } else {
                btn.className = "inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition duration-200 border bg-slate-800 text-gray-300 border-slate-700 hover:text-white";
                textSpan.textContent = "{{ __('Follow Author') }}";
                showToast('success', "Successfully unfollowed the author!");
            }
        } catch (error) {
            console.error('Error toggling author follow status:', error);
        }
    }
</script>
@endpush