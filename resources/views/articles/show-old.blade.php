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
    {{--
    @if($article->hasFeaturedImage())
        <meta property="og:image" content="{{ $article->getFeaturedImageUrl('large') }}">
    @endif
    --}}
    <meta property="og:locale" content="{{ $article_trans->language_code ?? 'en' }}">
    <meta property="og:site_name" content="Crime Chronicles">

    <!-- Twitter Cards -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $article_trans->meta_title ?? $article_trans->meta_title }}">
    <meta name="twitter:description" content="{{ $article_trans->meta_description ?? Str::limit(strip_tags($article_trans->meta_description), 160) }}">
    {{--
    @if($article->hasFeaturedImage())
        <meta name="twitter:image" content="{{ $article->getFeaturedImageUrl('large') }}">
    @endif
    --}}

    <!-- Canonical URL -->
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Alternate Languages (إذا كان المقال متوفر بلغات أخرى) -->
    @if($article_trans->article->translations ?? false)
        @foreach($article_trans->article->translations as $translation)
            <link rel="alternate" hreflang="{{ $translation->language_code }}" 
                    {{-- href="{{ route('articles.show', ['slug' => $translation->slug, 'lang' => $translation->language_code]) }}"> --}}
                    href="{{ route('articles.show', ['slug' => $translation->slug, 'id' => $translation->id]) }}">
        @endforeach
    @endif

    @vite(['resources/css/show.css'])
@endsection

@section('content')

    <!-- شريط تقدم القراءة -->
    <div class="reading-progress" id="readingProgress"></div>
    
    
    <!-- ==================== -->
    <!-- محتوى المقالة الرئيسي -->
    <!-- ==================== -->
    <div class="container mx-auto px-4 py-12 max-w-4xl">
        
        <!-- ==================== -->
        <!-- رأس المقالة -->
        <!-- ==================== -->
        <header class="mb-1">
            
            <!-- العنوان -->
            <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-6 leading-tight">
                {{ $article_trans->title }}
            </h1>

            <!-- معلومات الكاتب/النشر -->
            <div class="flex items-center text-gray-500 text-sm space-x-4">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-1 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                    </svg>
                    <span>Investigator Team</span>
                </div>
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-1 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span>{{ $article_trans->created_at->format('F j, Y') }}</span>
                </div>
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-1 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ ceil(str_word_count(strip_tags($article_trans->description)) / 200) }} min read</span>
                </div>
            </div>
        </header>


        
        <!-- ==================== -->
        <!-- الصورة الرئيسية -->
        <!-- ==================== -->
        {{--
        @if($article->hasFeaturedImage())
        <div class="case-image mb-12">
            <img src="{{ $article->getFeaturedImageUrl('large') }}" 
                 alt="{{ $article->title }}"
                 class="w-full h-[500px] object-cover">
            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 to-transparent p-6">
                <p class="text-gray-300 text-sm italic">Featured evidence from the case files</p>
            </div>
        </div>
        @endif
        --}}



             <!-- التصنيفات أسفل المقالة -->
            <div class="mt-3 mb-6 pt-3 border-t border-gray-800">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-gray-500 text-sm">More in:</span>
                    @php
                        // جلب سطر الترجمة المجلوب مسبقاً والمفلتر من الـ Controller
                        $category_translation = $article_trans->article->category->translations->first();
                        // تحديد الاسم المترجم أو الـ sign كبديل أمان
                        $categoryName = $category_translation ? $category_translation->name : $article_trans->article->category->sign;
                    @endphp
                    <a href="#alskdfj" class="case-badge text-xs hover:bg-red-600/20">
                        {{$categoryName}}
                    </a>

                    @auth
                        @php
                            // فحص ما إذا كان المستخدم الحالي يفضل هذه الفئة بالفعل لتغيير شكل أو نص الزر
                            $isCategoryFavorite = Auth::user()->favoriteCategories->contains($article_trans->article->category->id);
                        @endphp

                        <form action="{{ route('categories.favorite', $article_trans->article->category->id) }}" method="POST" class="inline-block">
                            @csrf
                            <button type="submit" 
                                    class="inline-flex items-center gap-2 px-[1rem] py-[6px] rounded-[6px] text-sm font-semibold transition-all duration-200 
                                    {{ $isCategoryFavorite 
                                        ? 'bg-red-50 text-red-900 hover:bg-red-100 border border-red-200' 
                                        : 'bg-gray-50 text-gray-600 hover:bg-gray-100 border border-gray-200' 
                                    }}">
                                
                                <svg class="w-4 h-4 {{ $isCategoryFavorite ? 'fill-current text-red-550' : 'none' }}" 
                                    stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"></path>
                                </svg>

                                <span>{{ $isCategoryFavorite ? __('Remove from Favorites') : __('Add to Favorites') }}</span>
                            </button>
                        </form>
                    @endauth

                    @guest
                        <a href="{{ route('login') }}" class="text-xs text-gray-500 underline">
                            {{ __('Log in to favorite this category') }}
                        </a>
                    @endguest

                </div>
            </div>
        








            @auth
                @php
                    // التحقق مما إذا كان المستخدم قد حفظ هذه المقالة مسبقاً
                    $isArticleSaved = auth()->user()->favoriteArticles->contains($article_trans->article_id);
                @endphp

                <form action="{{ route('articles.favorite', $article_trans->article_id) }}" method="POST" class="inline-block">
                    @csrf
                    <button type="submit" 
                            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-[6px] text-xs font-semibold transition-all duration-200 border
                            {{ $isArticleSaved 
                                ? 'bg-indigo-50 text-indigo-600 border-indigo-200' 
                                : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' 
                            }}">
                        
                        <svg class="w-4 h-4 {{ $isArticleSaved ? 'fill-current text-indigo-600' : 'none' }}" 
                            stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z"></path>
                        </svg>

                        <span>{{ $isArticleSaved ? __('Saved') : __('Save Article') }}</span>
                    </button>
                </form>
            @endauth







        <!-- ==================== -->
        <!-- المحتوى الرئيسي -->
        <!-- ==================== -->
        <div class="article-description">
            {!! $article_trans->description !!}
        </div>

        <hr />


        <div class="article-reactions mt-8">
            <button 
                id="article-like-{{$article_trans->article->id}}"
                onclick="articleLike(event, {{$article_trans->article->id}})"
                >
                <span>like</span>
                <span id="article-like-count-{{$article_trans->article->id}}">
                    ({{$article_trans->article->reactions->where('type', 'like')->count()}})
                </span>
            </button>
            <span>|</span>
            <button 
                id="article-dislike-{{$article_trans->article->id}}"
                onclick="articleDislike(event, {{$article_trans->article->id}})"
                >
                <span>dislike</span>
                <span id="article-dislike-count-{{$article_trans->article->id}}">
                    ({{$article_trans->article->reactions->where('type', 'dislike')->count()}})
                </span>
            </button>
        </div>


        @if($article_trans->article->tags->isNotEmpty())
            <div class="article-tags mt-6 flex items-center flex-wrap gap-2">
                <span class="text-gray-600 font-semibold mr-2">Tags:</span>
                
                @foreach($article_trans->article->tags as $tag)
                    @php
                        // 1. الحصول على لغة الموقع الحالية
                        $current_locale = app()->getLocale(); 
                        
                        // 2. البحث عن الترجمة بناءً على الحقل الصحيح في جدولك (language_code)
                        $tag_translation = $tag->translations->where('language_code', $current_locale)->first();
                        
                        // 3. تحديد النص الذي سيظهر: 
                        // إذا وجدت الترجمة نأخذ الاسم، وإذا لم توجد نأخذ الـ sign الافتراضي للوسم
                        $tagName = $tag_translation ? $tag_translation->name : $tag->sign;
                    @endphp
                    
                    {{-- الآن سيظهر كل شيء، سواء كان مترجماً أو غير مترجم --}}
                    <a href="#" class="flex items-center gap-1.5 bg-indigo-50 text-indigo-600 hover:bg-indigo-100 px-3 py-1 rounded-full text-sm font-medium transition duration-200">
                        <svg class="w-3.5 h-3.5 flex-shrink-0 text-indigo-500" 
                            fill="none" 
                            stroke="currentColor" 
                            stroke-width="2" 
                            viewBox="0 0 24 24" 
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" 
                                stroke-linejoin="round" 
                                d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581a1.125 1.125 0 001.591 0l7.182-7.182a1.125 1.125 0 000-1.59l-9.581-9.581A2.25 2.25 0 009.568 3z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z"></path>
                        </svg>
                        
                        <span>{{ $tagName }}</span>
                    </a>
                @endforeach
            </div>
        @endif


        <!-- Comment Form -->
        <div class="comments-wrapper mt-8">
            @auth
                <form action="" data-article-id="{{ $article_trans->article->id }}" method="POST" id="comment-form">
                    @csrf
                    <textarea 
                        name="comment-body"
                        id="comment-body"
                        rows="8"
                        placeholder="Write a comment..."
                        class="w-full text-slate-200 bg-slate-600 placeholder-slate-400 py-2 px-4 rounded-md border-2 border-slate-500 transition-colors duration-300 focus:border-red-600 focus:shadow-transparent focus:outline-none focus:ring-0"
                        style="field-sizing: content;"
                    ></textarea>
                    <button class="bg-sky-500 text-sm py-2 px-3 rounded" onclick="addComment(this)">Add Comment</button>
                </form>
            @else
                <h3 class="font-bold text-slate-200 italic text-center">Create an account to comment: <a href="{{route('register')}}" class="underline">Register</a> </h3>
            @endauth
        </div>

        @if ($article_trans->article->comments->count() > 0)
            <h1 class="text-3xl font-bold my-8">Comments({{$article_trans->article->comments->count()}}):</h1>
        @else
            <h1 class="text-3xl font-bold my-8">No comments</h1>
        @endif

        <div>
            <ul id="comments-container">
            @foreach ($article_trans->article->comments->where('parent_id', null) as $comment)
                @include('partials.comment_item', ['comment_item' => $comment])
            @endforeach
            </ul>
        </div>

        <!-- ==================== -->
        <!-- صور المحتوى الإضافية -->
        <!-- ==================== -->
        {{--
        @if($article_trans->article->hasMedia('content_imgs'))
        <div class="mt-16">
            <h2 class="text-2xl font-bold text-white mb-8 flex items-center">
                Case Evidence Gallery
                <svg class="w-6 h-6 ml-2 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                </svg>
            </h2>
            
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                @foreach($article_trans->article->getMedia('content_imgs') as $index => $image)
                <div class="relative group cursor-pointer" onclick="openLightbox({{ $index }})">
                    <img src="{{ $image->getUrl() }}" 
                         alt="Evidence image {{ $index + 1 }}"
                         class="w-full h-48 object-cover rounded-lg border-2 border-gray-700 group-hover:border-red-500 transition">
                    <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition rounded-lg flex items-center justify-center">
                        <span class="text-white text-sm font-mono">EVIDENCE #{{ str_pad($index + 1, 3, '0', STR_PAD_LEFT) }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
        --}}
        
        <!-- ==================== -->
        <!-- تذييل المقالة (مشاركة، تصنيفات) -->
        <!-- ==================== -->
        <div class="article-footer">
            
            <!-- أزرار المشاركة -->
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center flex-wrap gap-2">
                    <span class="text-gray-500 text-sm">Share this case:</span>
                    <button onclick="shareOnTwitter()" class="share-button px-3 py-1 rounded-lg text-sm text-gray-300">
                        Twitter
                    </button>
                    <button onclick="shareOnFacebook()" class="share-button px-3 py-1 rounded-lg text-sm text-gray-300">
                        Facebook
                    </button>
                    <button onclick="copyLink()" class="share-button px-3 py-1 rounded-lg text-sm text-gray-300">
                        Copy Link
                    </button>
                </div>
                
                <!-- أزرار التنقل بين المقالات -->
                <div class="flex gap-4">
                    @if($previousArticle ?? false)
                    <a href="{{ route('articles.show', $previousArticle->slug) }}" 
                       class="text-gray-400 hover:text-red-500 transition text-sm flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Previous Case
                    </a>
                    @endif
                    
                    @if($nextArticle ?? false)
                    <a href="{{ route('articles.show', $nextArticle->slug) }}" 
                       class="text-gray-400 hover:text-red-500 transition text-sm flex items-center">
                        Next Case
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                    @endif
                </div>
            </div>
            

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
        
        // مشاركة المقالة
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
        
        // Lightbox بسيط للصور
        function openLightbox(index) {
            const images = @json($article_trans->article->getMedia('content_imgs')->map(fn($img) => $img->getUrl()));
            // يمكن إضافة مكتبة lightbox هنا أو نافذة بسيطة
            alert('Opening evidence image #' + (index + 1));
        }

        //Adding a Comment
        async function addComment(event){
            let commentForm = document.getElementById('comment-form');
            commentForm.onsubmit= function(e) {
                e.preventDefault();
            }
            
            let article_id = commentForm.dataset.articleId;
            let comment_body = document.getElementById('comment-body');
            let locale = "{{ LaravelLocalization::getCurrentLocale() }}";
            let url = " {{ route('comments.store') }} ";
            let comment_item;

            if(comment_body.value == '') {
                return;
            }

            try {
                let res = await fetch(url, {
                    method: "POST",
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ 
                        article_id,
                        parent_id: null,
                        comment_body: comment_body.value,
                        locale
                    })
                });
                comment_item = await res.json();
                
                comment_body.value = '';
                let comm_cont = document.getElementById('comments-container');
                comm_cont.insertAdjacentHTML('afterbegin', comment_item.html)

            } catch(err) {
                alert('Error in adding a comment:', err);
            }                 
        }


        // Updating Comment
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
            if(edit_form) {
                edit_form.style.display = 'none';
            }
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
                    body: JSON.stringify({
                        id,
                        body: new_body.value
                    })
                })
                let data = await request.json();
                let comment_text = document.getElementById(`comment-text-${id}`);
                comment_text.textContent = data.new_body;
                hideEditForm(id);
            }catch(err) {
                console.log('Error in updating a comment:', err);
            }            
        }

        async function deleteComment(id) {
            if(!confirm('are you sure?')) {
                return;
            }
            let url = "{{ route('comments.delete') }}";

            try {
                let request = await fetch(url, {
                    method: "DELETE",
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        id,
                    })
                })
                let data = await request.json();
                let comment_item = document.getElementById(`comment-${id}`);
                comment_item.style.transition = '0.5s';
                comment_item.style.opacity = '0';
                setTimeout(() => {
                    comment_item.remove();
                }, 500);
            }catch(err) {
                console.log('Error in deleting a comment:', err);
            }
        }

        // Replying to a comment
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
            
            //let locale = "{{ LaravelLocalization::getCurrentLocale() }}";
            let url = " {{ route('comments.store') }} ";

            if(comment_reply_body.value == '') {
                return;
            }

            try {
                let res = await fetch(url, {
                    method: "POST",
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ 
                        article_id,
                        parent_id,
                        comment_body: comment_reply_body.value,
                        //locale
                    })
                });
                let comment_reply_item = await res.json();
                comment_reply_body.value = '';
                let replies_cont = document.getElementById(`replies-container-${parent_id}`);
                
                replies_cont.insertAdjacentHTML('beforeend', comment_reply_item.html)
                hideReplyForm(parent_id);
            } catch(err) {
                alert('Error in replying to a comment:', err);
            }
        }

        //Editing and Deleting Reply
        function showEditReplyForm(event, id) {
            let reply_text = document.getElementById(`reply-text-${id}`);
            let edit_reply_form = document.getElementById(`edit-reply-form-${id}`);
            let reply_actions = document.getElementById(`reply-actions-${id}`);
            let reply_input = document.getElementById(`new-reply-input-${id}`);

            reply_text.style.display = 'none';
            edit_reply_form.style.display = 'block';
            reply_actions.style.display = 'none'

            reply_input.focus();
        }

        function hideEditReplyForm(event, id) {
            let reply_text = document.getElementById(`reply-text-${id}`);
            let edit_reply_form = document.getElementById(`edit-reply-form-${id}`);
            let reply_actions = document.getElementById(`reply-actions-${id}`);

            reply_text.style.display = 'block';
            edit_reply_form.style.display = 'none';
            reply_actions.style.display = 'flex'
        }

        async function updateReply(event, id) {
            let new_body = document.getElementById(`new-reply-input-${id}`);
            let url = "{{ route('comments.update') }}";

            try {
                let request = await fetch(url, {
                    method: "PUT",
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        id,
                        body: new_body.value
                    })
                })
                let data = await request.json();
                let reply_text = document.getElementById(`reply-text-${id}`);
                reply_text.textContent = data.new_body;
                hideEditReplyForm(event, id);
            }catch(err) {
                console.log('Error in updating a comment:', err);
            }            
        }

        async function deleteReply(id) {
            if(!confirm('are you sure?')) {
                return;
            }
            let url = "{{ route('comments.delete') }}";

            try {
                let request = await fetch(url, {
                    method: "DELETE",
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        id,
                    })
                })
                
                let data = await request.json();
                let reply_item = document.getElementById(`reply-${id}`);
                console.log(reply_item);
                
                reply_item.style.transition = '0.5s';
                reply_item.style.opacity = '0';
                setTimeout(() => {
                    reply_item.remove();
                }, 500);
            }catch(err) {
                console.log('Error in deleting a reply:', err);
            }
        }

        async function articleLike(event, id) {
            let type = 'like';
            let url = "{{ route('articles.reactions.toggle') }}";

            try {
                let request = await fetch(url, {
                    method: "POST",
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        id, // article id
                        type
                    })
                })

                let data = await request.json();
                // updating likes count
                let articleLikeCount = document.getElementById(`article-like-count-${id}`);
                articleLikeCount.textContent = `(${data.likes})`;
                // updating dislikes count
                let articleDislikeCount = document.getElementById(`article-dislike-count-${id}`);
                articleDislikeCount.textContent = `(${data.dislikes})`;
            }catch(err) {
                console.log('error in article like')
            }
        }

        async function articleDislike(event, id) {
            let type = 'dislike';
            let url = "{{ route('articles.reactions.toggle') }}";

            try {
                let request = await fetch(url, {
                    method: "POST",
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        id, // article id
                        type
                    })
                })

                let data = await request.json();
                // updating dislikes count
                let articleDislikeCount = document.getElementById(`article-dislike-count-${id}`);
                articleDislikeCount.textContent = `(${data.dislikes})`;
                // updating likes count
                let articleLikeCount = document.getElementById(`article-like-count-${id}`);
                articleLikeCount.textContent = `(${data.likes})`;
            }catch(err) {
                console.log('error in article dislike')
            }
        }

    </script>
@endpush