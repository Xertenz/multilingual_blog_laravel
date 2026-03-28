{{-- resources/views/articles/show.blade.php --}}
<!DOCTYPE html>
<html lang="{{ $article_trans->language_code ?? 'en' }}" 
      dir="{{ in_array($article_trans->language_code ?? 'en', ['ar', 'fa', 'ur']) ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Meta Tags للزوار ومحركات البحث -->
    <title>{{ $article_trans->meta_title ?? $article_trans->meta_title }} | Crime Chronicles</title>
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
                  href="{{ route('articles.show', ['slug' => $translation->slug, 'lang' => $translation->language_code]) }}">
        @endforeach
    @endif
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Merriweather:wght@300;400;700;900&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css'])
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #f1f5f9;
            min-height: 100vh;
        }
        
        /* تخصيص المحتوى */
        .article-description {
            font-family: 'Merriweather', serif;
            line-height: 1.9;
            color: #e2e8f0;
        }
        
        .article-description > h1, 
        .article-description > h2, 
        .article-description > h3,
         {
            font-family: 'Inter', sans-serif ;
            font-weight: 700;
            margin-top: 2rem;
            margin-bottom: 1rem;
            color: white;
        }
        
        .article-description > h1 {
            font-size: 2.5rem;
            border-left: 4px solid #dc2626;
            padding-left: 1rem;
        }
        
        .article-description > h2 {
            font-family: Arial, Helvetica, sans-serif;
            font-weight: bold;
            font-size: 1.8rem;
            border-bottom: 2px solid #334155;
            padding-bottom: 0.25rem;
            margin-top: 1rem;
        }

        .article-description > h3 {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 1.5rem;
            font-weight: bold
        }
        
        .article-description p {
            margin-top: 0.5rem;
            margin-bottom: 0.5rem;
            font-family: 'Inter', sans-serif;
        }
        
        .article-description a {
            color: #ef4444;
            text-decoration: underline;
            text-underline-offset: 2px;
        }
        
        .article-description a:hover {
            color: #b91c1c;
        }
        
        .article-description > ul, 
        .article-description > ol {
            margin: 1rem 0 1.5rem 1.5rem;
        }
        
        .article-description li {
            margin-bottom: 0.5rem;
        }
        
        .article-description > blockquote {
            border-left: 4px solid #dc2626;
            background: #1e293b;
            padding: 1.5rem;
            font-style: italic;
            margin: 2rem 0;
            border-radius: 0 12px 12px 0;
            color: #cbd5e1;
        }
        
        .article-description .content_img-wrapper {
            margin: 2rem 0;
        }

        .article-description img {
            border: 2px solid #334155;
            transition: transform 0.3s ease;
            width: 40rem;
        }

        .article-description img + p {
            margin: 0px;
        }
        
        .article-description img:hover {
            transform: scale(1.02);
            border-color: #ef4444;
        }
        
        /* بطاقات القضايا */
        .case-badge {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            border: 1px solid #334155;
            border-radius: 12px;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            color: #94a3b8;
            transition: all 0.3s ease;
        }
        
        .case-badge:hover {
            border-color: #ef4444;
            color: white;
        }
        
        /* تأثيرات خاصة */
        .crime-scene-tape {
            background: repeating-linear-gradient(45deg, #fbbf24 0px, #fbbf24 20px, #000000 20px, #000000 40px);
            height: 6px;
            width: 100%;
        }
        
        .evidence-box {
            background: rgba(30, 41, 59, 0.8);
            backdrop-filter: blur(10px);
            border-left: 4px solid #dc2626;
            border-radius: 12px;
            padding: 1.5rem;
            margin: 2rem 0;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.5);
        }
        
        .share-button {
            transition: all 0.3s ease;
            background: #1e293b;
            border: 1px solid #334155;
        }
        
        .share-button:hover {
            background: #dc2626;
            border-color: #b91c1c;
            transform: translateY(-2px);
        }
        
        /* شريط تقدم القراءة */
        .reading-progress {
            position: fixed;
            top: 0;
            left: 0;
            width: 0%;
            height: 3px;
            background: linear-gradient(90deg, #ef4444, #b91c1c);
            z-index: 1000;
            transition: width 0.1s ease;
        }
        
        /* صورة القضية */
        .case-image {
            position: relative;
            overflow: hidden;
            border-radius: 16px;
            border: 2px solid #334155;
        }
        
        .case-image::after {
            content: 'EVIDENCE';
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: rgba(220, 38, 38, 0.9);
            color: white;
            padding: 0.25rem 1rem;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 2px;
            border-radius: 20px;
            transform: rotate(2deg);
        }
        
        /* تذييل المقالة */
        .article-footer {
            border-top: 2px solid #334155;
            margin-top: 4rem;
            padding-top: 2rem;
        }
    </style>
</head>
<body class="antialiased">

    <!-- شريط تقدم القراءة -->
    <div class="reading-progress" id="readingProgress"></div>
    
    <!-- Navigation -->
    @include('partials.navbar')
    
    <!-- شريط مسرح الجريمة (تأثير بصري) -->
    <div class="crime-scene-tape sticky top-[65px] left-0"></div>
    
    <!-- ==================== -->
    <!-- محتوى المقالة الرئيسي -->
    <!-- ==================== -->
    <div class="container mx-auto px-4 py-12 max-w-4xl">
        
        <!-- ==================== -->
        <!-- رأس المقالة -->
        <!-- ==================== -->
        <header class="mb-12">
            
            <!-- التصنيفات -->
            <div class="flex flex-wrap gap-3 mb-6">
                <span class="case-badge">{{ $article_trans->article->category->sign ?? 'UNSOLVED' }}</span>
                <span class="case-badge">{{ $article_trans->article->category->name ?? 'Mystery' }}</span>
                <span class="case-badge">{{ $article_trans->created_at->format('Y') }}</span>
                <span class="case-badge">{{ number_format($article_trans->article->views) }} views</span>
            </div>
            
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
        
        <!-- ==================== -->
        <!-- المحتوى الرئيسي -->
        <!-- ==================== -->
        <div class="article-description">
            {!! $article_trans->description !!}
        </div>

        <hr />
        <!-- Comment Form -->
        <div class="comments-wrapper mt-8">
            @auth
            <form action="" data-article-id="{{ $article_trans->article->id }}" method="POST" id="comment-form">
                @csrf
                <textarea 
                    name="comment-body"
                    id="comment-body"
                    rows="8"
                    placeholder="Enter Your Comment Here..."
                    class="w-full text-slate-200 bg-slate-600 placeholder-slate-400 py-2 px-4 rounded-md border-2 border-slate-500 transition-colors duration-300 focus:border-red-600 focus:shadow-transparent focus:outline-none focus:ring-0"
                    style="field-sizing: content;"
                ></textarea>
                <button class="bg-sky-500 text-sm py-2 px-3 rounded">Add Comment</button>
            </form>
            @else
                <h3 class="font-bold text-slate-200 italic text-center">Create an account to comment: <a href="{{route('register')}}" class="underline">Register</a> </h3>
            @endauth
        </div>

        <h1 class="text-3xl font-bold my-8">Comments:</h1>
        <div>
            <ul id="comments-container">
            @foreach ($article_trans->article->comments as $comment)
                @include('partials.comment_item', ['comment_item' => $comment])
            @endforeach
            </ul>
        </div>

        <!-- ==================== -->
        <!-- صور المحتوى الإضافية -->
        <!-- ==================== -->
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
                    <img src="{{ $image->getUrl('content_375w') }}" 
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
            
            <!-- التصنيفات أسفل المقالة -->
            <div class="mt-6 pt-6 border-t border-gray-800">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-gray-500 text-sm">More in:</span>
                    {{--
                    <a href="{{ route('categories.show', $article->category->slug) }}" class="case-badge text-xs hover:bg-red-600/20">
                        {{ $article->category->name ?? 'Uncategorized' }}
                    </a>
                    --}}
                    <a href="#alskdfj" class="case-badge text-xs hover:bg-red-600/20">
                        show categories
                    </a>
                </div>
            </div>
        </div>
        
        <!-- ==================== -->
        <!-- قسم التعليقات (اختياري) -->
        <!-- ==================== -->
        <div class="mt-16">
            <h3 class="text-2xl font-bold text-white mb-8">Investigation Notes</h3>
            
            <!-- نموذج التعليق البسيط -->
            {{-- <form action="{{ route('comments.store') }}" method="POST" class="mb-8"> --}}
            <form action="#" method="POST" class="mb-8">
                @csrf
                <input type="hidden" name="article_id" value="{{ $article_trans->id }}">
                <textarea name="content" 
                          rows="3" 
                          placeholder="Share your theories or findings..."
                          class="w-full bg-[#1e293b] border border-gray-700 rounded-lg px-4 py-3 text-white placeholder-gray-500 focus:border-red-500 focus:outline-none transition"></textarea>
                <button type="submit" 
                        class="mt-3 bg-red-600 hover:bg-red-700 px-6 py-2 rounded-lg text-white font-medium transition">
                    Submit Theory
                </button>
            </form>
            
            <!-- عرض التعليقات إن وجدت -->
            @if($comments ?? false)
                @foreach($comments as $comment)
                <div class="bg-[#1e293b] p-4 rounded-lg mb-3 border border-gray-700">
                    <div class="flex justify-between mb-2">
                        <span class="font-medium text-white">{{ $comment->user_name ?? 'Anonymous' }}</span>
                        <span class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-gray-400 text-sm">{{ $comment->content }}</p>
                </div>
                @endforeach
            @endif
        </div>
    </div>
    
    <!-- ==================== -->
    <!-- Footer بسيط -->
    <!-- ==================== -->
    <footer class="bg-[#0a0f1f] border-t border-gray-800 mt-16 py-8">
        <div class="container mx-auto px-4 text-center text-gray-500 text-sm">
            <p>&copy; {{ date('Y') }} Crime Chronicles. All rights reserved.</p>
            <p class="mt-2 text-xs text-gray-600">
                <a href="{{route('privacy')}}" class="hover:text-gray-400">Privacy</a> • 
                <a href="{{route('terms')}}" class="hover:text-gray-400">Terms</a> • 
                <a href="{{route('contact')}}" class="hover:text-gray-400">Contact</a>
            </p>
        </div>
    </footer>
    
    <!-- ==================== -->
    <!-- JavaScript -->
    <!-- ==================== -->
    @vite(['resources/js/app.js'])
    <script>
        // شريط تقدم القراءة
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

        //Comment
        let commentForm = document.getElementById('comment-form');
        commentForm.addEventListener('submit', async function(e){
            e.preventDefault();
            let article_id = commentForm.dataset.articleId;
            let user_id = "{{ Auth::id() }}";
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
                        user_id,
                        comment_body: comment_body.value,
                        locale
                    })
                });
                comment_item = await res.json();
            } catch(err) {
                alert('Error in adding a comment:', err);
            } finally {
                comment_body.value = '';
                let comm_cont = document.getElementById('comments-container');
                comm_cont.insertAdjacentHTML('afterbegin', comment_item.html)
            }
            
        });


        // Updating Comment
        function showEditForm(id) {
            let comment_actions = document.getElementById(`comment-actions-${id}`);
            let edit_form = document.getElementById(`edit-form-${id}`);
            edit_form.style.display = 'block';
            comment_actions.style.display = 'none';
        }

        function hideEditForm(id) {
            let comment_actions = document.getElementById(`comment-actions-${id}`);
            let edit_form = document.getElementById(`edit-form-${id}`);
            edit_form.style.display = 'none';
            comment_actions.style.display = 'flex';
        }

        async function updateComment(id) {
            let new_body = document.getElementById(`edit-input-${id}`);
            let url = "{{ route('comments.update') }}";

            let data;
            try {
                let request = await fetch(url, {
                    method: "PUT",
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        id,
                        body: new_body.value.trim()
                    })
                })
                data = await request.json();
            }catch(err) {
                console.log('Error in updating a comment:', err);
            }finally {
                let comment_text = document.getElementById(`comment-text-${id}`);
                comment_text.textContent = data.new_body;
                hideEditForm(id);
            }
            
        }

        async function deleteComment(id) {
            if(!confirm('are you sure?')) {
                return;
            }
            let url = "{{ route('comments.delete') }}";

            let data;
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
                data = await request.json();
            }catch(err) {
                console.log('Error in deleting a comment:', err);
            }finally {
                let comment_item = document.getElementById(`comment-${id}`);
                comment_item.style.transition = '0.5s';
                comment_item.style.opacity = '0';
                setTimeout(() => {
                    comment_item.remove();
                }, 500);
            }

        }
        
    </script>
</body>
</html>