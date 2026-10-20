
<div class="container mx-auto px-4 py-12 max-w-4xl text-white">
    
    <!-- بطاقة المعلومات الشخصية الأساسية -->
    <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-8 shadow-xl flex flex-col md:flex-row items-center gap-6 mb-8">
        
        <div class="w-24 h-24 rounded-full bg-red-600/20 border-2 border-red-600 flex items-center justify-center text-3xl font-bold text-red-500 uppercase">
            {{ mb_substr($user->name, 0, 1) }}
        </div>

        <div class="text-center md:text-right flex-1">
            <h1 class="text-2xl font-bold text-white capitalize mb-1">{{ $user->name }}</h1>
            <p class="text-gray-400 text-sm mb-4">{{ $user->email }}</p>
            
            <div class="flex flex-wrap justify-center md:justify-start gap-4 text-xs text-gray-500">
                <span>Joined: {{ $user->created_at->format('F Y') }}</span>
                <span>•</span>
                <span>Saved Articles: {{ $user->savedArticles->count() }}</span>
                <span>•</span>
                <span>Followed Categories: {{ $user->favoriteCategories->count() }}</span>
            </div>
        </div>

        <!-- زر الانتقال لتعديل الحساب عبر Breeze إذا كان المصفح هو صاحب الحساب -->
        <div>
            @if(auth()->check() && auth()->id() === $user->id)
                <a href="{{ route('profile.edit') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-xs font-semibold rounded-lg text-gray-200 transition">
                    Edit Profile (Breeze)
                </a>
            @endif
        </div>
    </div>

    <!-- شريط التبويبات (Tabs Navigation) -->
    <div class="flex border-b border-slate-800 mb-6 gap-6 text-sm font-semibold">
        <button wire:click="switchTab('articles')" 
                class="pb-3 transition border-b-2 {{ $activeTab === 'articles' ? 'border-red-600 text-red-500' : 'border-transparent text-gray-400 hover:text-white' }}">
            Saved Articles ({{ $user->savedArticles->count() }})
        </button>
        
        <button wire:click="switchTab('categories')" 
                class="pb-3 transition border-b-2 {{ $activeTab === 'categories' ? 'border-red-600 text-red-500' : 'border-transparent text-gray-400 hover:text-white' }}">
            Followed Categories ({{ $user->favoriteCategories->count() }})
        </button>

        <button wire:click="switchTab('comments')" 
                class="pb-3 transition border-b-2 {{ $activeTab === 'comments' ? 'border-red-600 text-red-500' : 'border-transparent text-gray-400 hover:text-white' }}">
            Activity & Comments ({{ $user->comments->count() }})
        </button>
    </div>

    <!-- محتوى التبويبات -->
    <div class="bg-slate-900/40 border border-slate-800 rounded-2xl p-6 min-h-[300px]">
        
        <!-- تبويب المقالات المحفوظة -->
        @if($activeTab === 'articles')
            <div class="grid grid-cols-1 gap-4">
                @forelse($user->savedArticles as $article)
                    @php
                        // جلب الصورة البارزة للمقال إن وجدت، أو استخدام صورة افتراضية
                        $featuredImage = $article->getFirstMediaUrl('featured_imgs') ?: asset('images/default-article.jpg');
                    @endphp
                    <div class="p-4 bg-slate-800/40 hover:bg-slate-800/80 border border-slate-800 hover:border-slate-700 rounded-xl transition-all duration-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 group">
                        
                        <!-- صورة المقال والـ Title -->
                        <div class="flex items-start gap-4 flex-1">
                            <img src="{{ $featuredImage }}" alt="{{ $this->getArticleTrans($article)->title }}" class="w-16 h-16 rounded-lg object-cover border border-slate-700/60 shrink-0">
                            
                            <div class="space-y-1">
                                <h4 class="text-sm font-bold text-white group-hover:text-red-400 transition">
                                    {{ $this->getArticleTrans($article)->title ?? $article->internal_title }}
                                </h4>
                                <p class="text-xs text-gray-400 line-clamp-1">
                                    {{ Str::limit(strip_tags($this->getArticleTrans($article)->description ?? ''), 80) }}
                                </p>
                            </div>
                        </div>
                        <!-- زر الانتقال لقراءة المقال -->
                        <a href="{{ route('articles.show', [$this->getArticleTrans($article)->id, $this->getArticleTrans($article)->slug ]) }}" 
                           class="inline-flex items-center gap-1.5 px-3 py-2 bg-slate-700/50 hover:bg-red-600 text-gray-200 hover:text-white text-xs font-semibold rounded-lg transition shrink-0">
                            <span>Read Case</span>
                            <svg class="w-3.5 h-3.5 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                @empty
                    <div class="text-center py-12">
                        <svg class="w-12 h-12 mx-auto text-slate-700 mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z"></path>
                        </svg>
                        <p class="text-gray-500 text-xs italic">No saved articles in your archive yet.</p>
                    </div>
                @endforelse
            </div>
        @endif

        <!-- تبويب الفئات المتابعة -->
        @if($activeTab === 'categories')
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @forelse($user->favoriteCategories as $category)
                    @php
                        // جلب صورة الفئة إن وجدت عبر Spatie Media Library
                        $categoryImage = $category->getFirstMediaUrl('featured_imgs') ?: null;
                    @endphp
                    <div class="p-4 bg-slate-800/40 hover:bg-slate-800/80 border border-slate-800 hover:border-slate-700 rounded-xl transition-all duration-200 flex items-center justify-between group">
                        <div class="flex items-center gap-3">
                            @if($categoryImage)
                                <img src="{{ $categoryImage }}" alt="{{ $this->getCatTrans($category)->name }}" class="w-10 h-10 rounded-lg object-cover border border-slate-700/60 shrink-0">
                            @else
                                <div class="w-10 h-10 rounded-lg bg-red-600/10 border border-red-600/20 flex items-center justify-center text-red-500 font-bold text-sm shrink-0">
                                    #
                                </div>
                            @endif
                            <div>
                                <h4 class="text-xs font-bold text-white group-hover:text-red-400 transition uppercase tracking-wide">
                                    {{ $this->getCatTrans($category)->name ?? $category->label }}
                                </h4>
                                <span class="text-[10px] text-gray-500 font-mono">Following Category</span>
                            </div>
                        </div>

                        <!-- رابط التوجه للفئة -->
                        <a href="{{ route('categories.show', ['id' => $this->getCatTrans($category)->id, 'slug' => $this->getCatTrans($category)->slug]) }}" 
                           class="p-2 text-gray-400 hover:text-white bg-slate-800 hover:bg-red-600 rounded-lg transition">
                            <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                @empty
                    <div class="text-center py-12 col-span-2">
                        <svg class="w-12 h-12 mx-auto text-slate-700 mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"></path>
                        </svg>
                        <p class="text-gray-500 text-xs italic">Not following any categories yet.</p>
                    </div>
                @endforelse
            </div>
        @endif

        <!-- تبويب التعليقات والنشاطات -->
        @if($activeTab === 'comments')
            <div class="space-y-3">
                @forelse($user->comments as $comment)
                    <div class="p-4 bg-slate-800/40 border border-slate-800 rounded-xl text-xs space-y-1.5">
                        @if($comment->article)
                            <a href="{{ route('articles.show', [$this->getArticleTrans($comment->article)->id, $this->getArticleTrans($comment->article)->slug ]) }}" class="text-red-400 hover:text-red-300 font-semibold inline-flex items-center gap-1 transition">
                                <span>On Case: {{ $this->getArticleTrans($comment->article)->title ?? $comment->article->internal_title }}</span>
                                <svg class="w-3 h-3 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        @endif
                        <p class="text-gray-300 leading-relaxed">{{ $comment->body }}</p>
                        <span class="text-gray-500 font-mono text-[10px] block pt-1">{{ $comment->created_at->diffForHumans() }}</span>
                    </div>
                @empty
                    <div class="text-center py-12">
                        <p class="text-gray-500 text-xs italic">No activity or comments recorded yet.</p>
                    </div>
                @endforelse
            </div>
        @endif

    </div>

</div>