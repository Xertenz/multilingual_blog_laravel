{{-- resources/views/articles/show.blade.php --}}
@extends('layouts.main')
@section('title', __('about.title'))

@section('meta_tags')
    <!-- Meta Tags للزوار ومحركات البحث -->
    <meta name="description" content="{{ $article_trans->meta_description ?? Str::limit(strip_tags($article_trans->meta_description), 160) }}">
    <!-- Open Graph للشبكات الاجتماعية -->
    <meta property="og:title" content="{{ $article_trans->meta_title ?? $article_trans->meta_title }}">
    <meta property="og:description" content="{{ $article_trans->meta_description ?? Str::limit(strip_tags($article_trans->meta_description), 160) }}">
    <meta property="og:type" content="article">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:locale" content="{{ $article_trans->language_code ?? 'en' }}">
    <meta property="og:site_name" content="Crime Chronicles">

    <!-- Twitter Cards -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $article_trans->meta_title ?? $article_trans->meta_title }}">
    <meta name="twitter:description" content="{{ $article_trans->meta_description ?? Str::limit(strip_tags($article_trans->meta_description), 160) }}">

    <!-- Canonical URL -->
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Alternate Languages -->
    @if($article_trans->article->translations ?? false)
        @foreach($article_trans->article->translations as $translation)
            <link rel="alternate"
                hreflang="{{ $translation->language_code }}" 
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
    
    <!-- ==================== -->
    <!-- محتوى المقالة الرئيسي -->
    <!-- ==================== -->
    <div class="container mx-auto px-4 py-12 max-w-3xl">
        
        <!-- ==================== -->
        <!-- رأس المقالة (Header) -->
        <!-- ==================== -->
        <header class="mb-8 pb-6 border-b border-gray-800">
            
            <!-- العنوان الرئيسي -->
            <h1 class="text-3xl capitalize md:text-4xl lg:text-5xl font-bold text-white mb-6 leading-tight">
                {{ $article_trans->title }}
            </h1>

            <!-- شريط معلومات المقال + زر حفظ المقال الذكي -->
            <div class="flex flex-wrap items-center justify-between gap-4 text-gray-400 text-sm">
                
                <!-- البيانات الوصفية (Metadata) -->
                <div class="flex flex-wrap items-center gap-x-4 gap-y-2">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-1.5 text-red-600 rtl:ml-1.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                        </svg>
                        <span>Investigator Team</span>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-1.5 text-red-600 rtl:ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span>{{ $article_trans->created_at->format('F j, Y') }}</span>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-1.5 text-red-600 rtl:ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>{{ ceil(str_word_count(strip_tags($article_trans->description)) / 200) }} min read</span>
                    </div>
                </div>

                <!-- زر حفظ المقالة في الأعلى (تم نقله هنا لجمال التصميم) -->
                @auth
                    @php
                        $isArticleSaved = auth()->user()->favoriteArticles->contains($article_trans->article_id);
                    @endphp

                    <button type="button" 
                            id="save-article-btn-{{ $article_trans->article_id }}"
                            onclick="toggleFavoriteArticle(event, {{ $article_trans->article_id }})"
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
        <div class="mt-12 bg-slate-900/60 border border-slate-800 rounded-2xl p-6 space-y-6">
            
            <!-- السطر الأول: الإعجابات وتفضيل الفئة -->
            <div class="flex flex-wrap items-center justify-between gap-4 pb-4 border-b border-slate-800/60">
                
                <!-- أزرار الـ Reactions (Like / Dislike) مجمعة ومتفاعلة بصرياً -->
                <div class="flex items-center bg-slate-800/80 rounded-xl p-1 border border-slate-700/50">
                    @auth
                        @php
                            // فحص تفاعل المستخدم الحالي مع هذه المقالة
                            $userReaction = $article_trans->article->reactions->where('user_id', auth()->id())->first();
                            $hasLiked = $userReaction && $userReaction->type === 'like';
                            $hasDisliked = $userReaction && $userReaction->type === 'dislike';
                        @endphp
                    @else
                        @php $hasLiked = false; $hasDisliked = false; @endphp
                    @endauth

                    <!-- زر Like -->
                    <button id="article-like-{{$article_trans->article->id}}" 
                            onclick="articleLike(event, {{$article_trans->article->id}})"
                            data-active="{{ $hasLiked ? 'true' : 'false' }}"
                            class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition duration-200
                            {{ $hasLiked 
                                ? 'bg-emerald-950/80 text-emerald-400 border border-emerald-800/60' 
                                : 'text-gray-300 hover:text-emerald-400 hover:bg-slate-700/60' 
                            }}">
                        <span class="uppercase">Like</span>
                        <span id="article-like-count-{{$article_trans->article->id}}" class="font-mono">
                            ({{$article_trans->article->reactions->where('type', 'like')->count()}})
                        </span>
                    </button>
                    
                    <span class="text-slate-600 px-1">|</span>
                    
                    <!-- زر Dislike -->
                    <button id="article-dislike-{{$article_trans->article->id}}" 
                            onclick="articleDislike(event, {{$article_trans->article->id}})"
                            data-active="{{ $hasDisliked ? 'true' : 'false' }}"
                            class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition duration-200
                            {{ $hasDisliked 
                                ? 'bg-rose-950/80 text-rose-400 border border-rose-800/60' 
                                : 'text-gray-300 hover:text-rose-400 hover:bg-slate-700/60' 
                            }}">
                        <span class="uppercase">Dislike</span>
                        <span id="article-dislike-count-{{$article_trans->article->id}}" class="font-mono">
                            ({{$article_trans->article->reactions->where('type', 'dislike')->count()}})
                        </span>
                    </button>
                </div>

                <!-- قسم فئة المقال وزر الحفظ الخاص بها -->
                <div class="flex items-center gap-3">
                    <span class="text-gray-500 text-xs font-medium">More in:</span>
                    @php
                        $category_translation = $article_trans->article->category->translations->first();
                        $categoryName = $category_translation ? $category_translation->name : $article_trans->article->category->sign;
                        $categoryId = $article_trans->article->category->id;
                    @endphp
                    <a href="#alskdfj" class="case-badge text-xs bg-red-950/40 text-red-400 border border-red-900/50 px-2.5 py-1 rounded-md hover:bg-red-600/20 transition">
                        {{$categoryName}}
                    </a>

                    @auth
                        @php
                            $isCategoryFavorite = Auth::user()->favoriteCategories->contains($categoryId);
                        @endphp

                        <button type="button" 
                                id="fav-category-btn-{{ $categoryId }}"
                                onclick="toggleFavoriteCategory(event, {{ $categoryId }})"
                                data-saved="{{ $isCategoryFavorite ? 'true' : 'false' }}"
                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-semibold transition-all duration-200 border
                                {{ $isCategoryFavorite 
                                    ? 'bg-red-950/60 text-red-400 border-red-800 hover:bg-red-900/40' 
                                    : 'bg-slate-800 text-gray-400 border-slate-700 hover:bg-slate-700 hover:text-gray-200' 
                                }}">
                            
                            <svg id="fav-category-icon-{{ $categoryId }}" 
                                class="w-3.5 h-3.5 transition-colors duration-200 {{ $isCategoryFavorite ? 'fill-current text-red-500' : 'fill-none' }}" 
                                stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"></path>
                            </svg>

                            <span id="fav-category-text-{{ $categoryId }}">
                                {{ $isCategoryFavorite ? __('Remove Favorites') : __('Add Favorites') }}
                            </span>
                        </button>
                    @endauth

                    @guest
                        <a href="{{ route('login') }}" class="text-xs text-gray-500 underline hover:text-gray-400">
                            {{ __('Log in to favorite this category') }}
                        </a>
                    @endguest
                </div>
            </div>

            <!-- السطر الثاني: الكلمات الدلالية (Tags) -->
            @if($article_trans->article->tags->isNotEmpty())
                <div class="flex items-center flex-wrap gap-2 pt-2">
                    <span class="text-slate-500 text-xs font-semibold mr-2 rtl:ml-2">Tags:</span>
                    
                    @foreach($article_trans->article->tags as $tag)
                        @php
                            $current_locale = app()->getLocale(); 
                            $tag_translation = $tag->translations->where('language_code', $current_locale)->first();
                            $tagName = $tag_translation ? $tag_translation->name : $tag->sign;
                        @endphp
                        
                        <a href="#" class="flex items-center gap-1 bg-slate-800 text-slate-300 hover:bg-slate-700 hover:text-white px-2.5 py-1 rounded-md text-xs font-medium transition duration-200 border border-slate-700/40">
                            <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581a1.125 1.125 0 001.591 0l7.182-7.182a1.125 1.125 0 000-1.59l-9.581-9.581A2.25 2.25 0 009.568 3z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z"></path>
                            </svg>
                            <span>{{ $tagName }}</span>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- ==================== -->
        <!-- تذييل المقالة وأدوات المشاركة والتنقل -->
        <!-- ==================== -->
        <footer class="mt-8 pt-6 border-t border-slate-800">
            <div class="flex flex-wrap items-center justify-between gap-6 bg-slate-900/20 p-4 rounded-xl">
                
                <!-- أزرار المشاركة السريعة -->
                <div class="flex items-center flex-wrap gap-2">
                    <span class="text-gray-500 text-xs font-medium">Share:</span>
                    <button onclick="shareOnTwitter()" class="px-2.5 py-1 text-xs rounded bg-slate-800 text-gray-400 hover:bg-slate-700 hover:text-white transition">Twitter</button>
                    <button onclick="shareOnFacebook()" class="px-2.5 py-1 text-xs rounded bg-slate-800 text-gray-400 hover:bg-slate-700 hover:text-white transition">Facebook</button>
                    <button onclick="copyLink()" class="px-2.5 py-1 text-xs rounded bg-slate-800 text-gray-400 hover:bg-slate-700 hover:text-white transition">Copy Link</button>
                </div>
                
                <!-- أزرار التنقل بين القضايا والمقالات المتجاورة -->
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
        </footer>

        <!-- ==================== -->
        <!-- قسم التعليقات (Comments Section) -->
        <!-- ==================== -->
        <section class="mt-16 pt-8 border-t border-gray-800">
            
            <!-- عنوان التعليقات وعددها الحالي -->
            @if ($article_trans->article->comments->count() > 0)
                <h2 class="text-xl font-bold text-white mb-6">Comments ({{$article_trans->article->comments->count()}})</h2>
            @else
                <h2 class="text-xl font-bold text-gray-400 mb-6 italic">No comments yet</h2>
            @endif

            <!-- نموذج إضافة تعليق جديد -->
            <div class="comments-wrapper mb-8 bg-slate-900/40 p-4 rounded-xl border border-slate-850">
                @auth
                    <form action="" data-article-id="{{ $article_trans->article->id }}" method="POST" id="comment-form" class="space-y-3">
                        @csrf
                        <textarea 
                            name="comment-body"
                            id="comment-body"
                            rows="4"
                            placeholder="Write a public comment..."
                            class="w-full text-slate-200 bg-slate-800 placeholder-slate-500 py-3 px-4 rounded-xl border border-slate-700 transition duration-200 focus:border-red-600 focus:outline-none focus:ring-0 text-sm"
                            style="field-sizing: content;"
                        ></textarea>
                        <div class="flex justify-end">
                            <button class="bg-sky-600 hover:bg-sky-700 text-white font-semibold text-xs py-2 px-4 rounded-lg transition duration-150" onclick="addComment(this)">
                                Add Comment
                            </button>
                        </div>
                    </form>
                @else
                    <div class="p-4 text-center rounded-lg bg-slate-900/80 border border-slate-800">
                        <p class="text-sm text-gray-400 italic">
                            Create an account to participate in the discussion: 
                            <a href="{{route('register')}}" class="text-sky-400 hover:underline font-semibold not-italic ml-1">Register Now</a>
                        </p>
                    </div>
                @endauth
            </div>

            <!-- حاوية عرض شجرة التعليقات -->
            <div class="mt-6">
                <ul id="comments-container" class="space-y-4">
                @foreach ($article_trans->article->comments->where('parent_id', null) as $comment)
                    @include('partials.comment_item', ['comment_item' => $comment])
                @endforeach
                </ul>
            </div>
        </section>

    </div>

@endsection

@push('scripts')
<script>
    // 1. شريط تقدم القراءة (Reading Progress Bar)
    window.addEventListener('scroll', function() {
        const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
        const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
        const scrolled = (winScroll / height) * 100;
        document.getElementById('readingProgress').style.width = scrolled + '%';
    });
    
    // 2. مشاركة المقال (Social Sharing)
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
    
    // 3. معرض الصور (Evidence Lightbox)
    function openLightbox(index) {
        const images = @json($article_trans->article->getMedia('content_imgs')->map(fn($img) => $img->getUrl()));
        alert('Opening evidence image #' + (index + 1));
    }

    // 4. نظام حفظ وتفضيل المقالات والفئات (Favorites System)
    async function toggleFavoriteArticle(event, articleId) {
        event.preventDefault();
        const baseUrl = "{{ route('articles.favorite', ':id') }}";
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
                showToast('success', "Successfully added to favorites!");
            } else {
                btn.setAttribute('data-saved', 'false');
                btn.className = "inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200 border bg-slate-800 text-gray-300 border-slate-700 hover:bg-slate-700 hover:text-white";
                icon.classList.remove('fill-current', 'text-white');
                icon.classList.add('fill-none');
                textSpan.textContent = "{{ __('Save Article') }}";
                showToast('success', "Successfully removed from favorites!");
            }
        } catch (error) {
            console.error('Error toggling favorite status:', error);
            showToast('error', "Error in the operation with favorite articles!");
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
                btn.className = "inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-semibold transition-all duration-200 border bg-red-950/60 text-red-400 border-red-800 hover:bg-red-900/40";
                icon.classList.remove('fill-none');
                icon.classList.add('fill-current', 'text-red-500');
                textSpan.textContent = "{{ __('Remove Favorites') }}";
                showToast('success', "Successfully added to favorites!");
            } else {
                btn.setAttribute('data-saved', 'false');
                btn.className = "inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-semibold transition-all duration-200 border bg-slate-800 text-gray-400 border-slate-700 hover:bg-slate-700 hover:text-gray-200";
                icon.classList.remove('fill-current', 'text-red-500');
                icon.classList.add('fill-none');
                textSpan.textContent = "{{ __('Add Favorites') }}";
                showToast('success', "Successfully removed from favorites!");
            }
        } catch (error) {
            console.error('Error toggling category favorite status:', error);
            showToast('error', "Error in the operation with favorite categories!");
        }
    }

    // 5. تفاعلات الإعجاب وعدم الإعجاب (Likes & Dislikes System)
    async function articleLike(event, id) {
        let type = 'like';
        let url = "{{ route('articles.reactions.toggle') }}";
        let likeBtn = document.getElementById(`article-like-${id}`);
        let dislikeBtn = document.getElementById(`article-dislike-${id}`);

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
                likeBtn.className = "flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-gray-300 hover:text-emerald-400 hover:bg-slate-700/60 rounded-lg border border-transparent transition duration-200";
            } else {
                likeBtn.setAttribute('data-active', 'true');
                likeBtn.className = "flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition duration-200 bg-emerald-950/80 text-emerald-400 border border-emerald-800/60";
                
                dislikeBtn.setAttribute('data-active', 'false');
                dislikeBtn.className = "flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-gray-300 hover:text-rose-400 hover:bg-slate-700/60 rounded-lg transition duration-200";
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
                dislikeBtn.className = "flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-gray-300 hover:text-rose-400 hover:bg-slate-700/60 rounded-lg transition duration-200";
            } else {
                dislikeBtn.setAttribute('data-active', 'true');
                dislikeBtn.className = "flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition duration-200 bg-rose-950/80 text-rose-400 border border-rose-800/60";
                
                likeBtn.setAttribute('data-active', 'false');
                likeBtn.className = "flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-gray-300 hover:text-emerald-400 hover:bg-slate-700/60 rounded-lg transition duration-200";
            }
        } catch(err) {
            console.log('error in article dislike', err);
        }
    }

    // 6. نظام التعليقات الأساسية (Comments System)
    async function addComment(event) {
        let commentForm = document.getElementById('comment-form');
        commentForm.onsubmit = function(e) { e.preventDefault(); }
        
        let article_id = commentForm.dataset.articleId;
        let comment_body = document.getElementById('comment-body');
        let locale = "{{ LaravelLocalization::getCurrentLocale() }}";
        let url = "{{ route('comments.store') }}";
        let comment_item;

        if (comment_body.value == '') { return; }

        try {
            let res = await fetch(url, {
                method: "POST",
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ article_id, parent_id: null, comment_body: comment_body.value, locale })
            });
            comment_item = await res.json();
            
            comment_body.value = '';
            let comm_cont = document.getElementById('comments-container');
            comm_cont.insertAdjacentHTML('afterbegin', comment_item.html);
        } catch(err) {
            alert('Error in adding a comment:', err);
        }                 
    }

    function showEditForm(id) {
        hideReplyForm(id);
        let comment_actions = document.getElementById(`comment-actions-${id}`);
        let edit_form = document.getElementById(`edit-form-${id}`);
        let edit_input = document.getElementById(`edit-input-${id}`);
        edit_form.style.display = 'block';
        comment_actions.style.display = 'none';
        edit_input.focus();

        let commentText = document.getElementById(`comment-text-${id}`);
        commentText.style.display = 'none';
    }

    function hideEditForm(id) {
        let comment_actions = document.getElementById(`comment-actions-${id}`);
        let edit_form = document.getElementById(`edit-form-${id}`);
        if (edit_form) { edit_form.style.display = 'none'; }
        comment_actions.style.display = 'flex';

        let commentText = document.getElementById(`comment-text-${id}`);
        commentText.style.display = 'block';
    }

    async function updateComment(id) {
        let new_body = document.getElementById(`edit-input-${id}`);
        let url = "{{ route('comments.update') }}";

        try {
            let request = await fetch(url, {
                method: "PUT",
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ id, body: new_body.value })
            });
            let data = await request.json();
            let comment_text = document.getElementById(`comment-text-${id}`);
            comment_text.textContent = data.new_body;
            hideEditForm(id);
        } catch(err) {
            console.log('Error in updating a comment:', err);
        }            
    }

    async function deleteComment(id) {
        if (!confirm('Are you sure?')) { return; }
        let url = "{{ route('comments.delete') }}";

        try {
            let request = await fetch(url, {
                method: "DELETE",
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ id })
            });
            let data = await request.json();
            let comment_item = document.getElementById(`comment-${id}`);
            comment_item.style.transition = '0.5s';
            comment_item.style.opacity = '0';
            setTimeout(() => { comment_item.remove(); }, 500);
        } catch(err) {
            console.log('Error in deleting a comment:', err);
        }
    }

    // 7. نظام الردود على التعليقات (Replies System)
    function showReplyForm(id) {
        hideEditForm(id);
        let comment_actions = document.getElementById(`comment-actions-${id}`);
        let reply_form = document.getElementById(`reply-form-${id}`);
        let reply_input = document.getElementById(`reply-input-${id}`);
        reply_form.style.display = 'block';
        comment_actions.style.display = 'none';
        reply_input.focus();
    }

    function hideReplyForm(id) {
        let comment_actions = document.getElementById(`comment-actions-${id}`);
        let reply_form = document.getElementById(`reply-form-${id}`);
        reply_form.style.display = 'none';
        comment_actions.style.display = 'flex';
    }

    async function replyToComment(event, parent_id) {
        let comment_reply_body = document.getElementById(`reply-input-${parent_id}`);
        let article_id = comment_reply_body.dataset.articleId;
        let url = "{{ route('comments.store') }}";

        if (comment_reply_body.value == '') { return; }

        try {
            let res = await fetch(url, {
                method: "POST",
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ article_id, parent_id, comment_body: comment_reply_body.value })
            });
            let comment_reply_item = await res.json();
            comment_reply_body.value = '';
            let replies_cont = document.getElementById(`replies-container-${parent_id}`);
            
            replies_cont.insertAdjacentHTML('beforeend', comment_reply_item.html);
            hideReplyForm(parent_id);
        } catch(err) {
            alert('Error in replying to a comment:', err);
        }
    }

    // --- تم توحيد دوال تعديل وحذف الردود هنا بشكل نظيف ومتناسق 100% ---
    
    function showEditReplyForm(replyId) {
        document.getElementById(`reply-text-${replyId}`).style.display = 'none';
        document.getElementById(`reply-actions-${replyId}`).style.display = 'none';
        document.getElementById(`edit-reply-form-${replyId}`).style.display = 'block';
        
        let reply_input = document.getElementById(`new-reply-input-${replyId}`);
        if (reply_input) reply_input.focus();
    }

    function hideEditReplyForm(replyId) {
        document.getElementById(`reply-text-${replyId}`).style.display = 'block';
        document.getElementById(`reply-actions-${replyId}`).style.display = 'flex';
        document.getElementById(`edit-reply-form-${replyId}`).style.display = 'none';
    }

    async function updateReply(replyId) {
        const newBody = document.getElementById(`new-reply-input-${replyId}`).value;
        const url = "{{ route('comments.update') }}";

        try {
            let response = await fetch(url, {
                method: "PUT", // ميثود الـ POST لأن الـ Controller يستقبلها هكذا في الـ Route الخاص بك
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    id: replyId,
                    body: newBody
                })
            });

            let data = await response.json();

            if (response.ok) {
                document.getElementById(`reply-text-${replyId}`).textContent = data.new_body;
                hideEditReplyForm(replyId);
            } else {
                alert(data.error || 'Error updating reply');
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }

    async function deleteReply(replyId) {
        if (!confirm('Are you sure you want to delete this reply?')) return;
        const url = "{{ route('comments.delete') }}";

        try {
            let response = await fetch(url, {
                method: "DELETE",
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    id: replyId
                })
            });

            let data = await response.json();

            if (response.ok) {
                const replyItem = document.getElementById(`reply-item-${replyId}`);
                if (replyItem) {
                    replyItem.style.opacity = '0';
                    replyItem.style.transform = 'scale(0.95)';
                    replyItem.style.transition = 'all 0.3s ease';
                    
                    setTimeout(() => {
                        replyItem.remove();
                    }, 300);
                }
            } else {
                alert(data.error || 'Error deleting reply');
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }



</script>

@endpush