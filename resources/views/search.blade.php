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
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($articles as $article)
                    @php
                        // جلب ترجمة المقالة المتوافقة مع لغة التصفح الحالية
                        $trans = $article->translations->where('locale', app()->getLocale())->first() 
                                ?? $article->translations->first();

                        // 👤 جلب الكاتب بأولوية الترجمة، ثم المقالة الأساسية، والتحقق من وجوده
                        $author = $trans?->user ?? $article->user;
                        $authorName = $author?->name ?? __('home.anonymous');
                        $authorId = $author?->id;

                        // جلب صورة البانر للمقال عبر Spatie MediaLibrary
                        $imageUrl = $article->hasMedia('featured_imgs') 
                            ? $article->getFirstMediaUrl('featured_imgs', 'medium') 
                            : asset('images/default-article.jpg');
                    @endphp

                    @if($trans)
                        <a href="{{ route('articles.show', ['id' => $trans->id, 'slug' => $trans->slug]) }}" 
                           class="group relative bg-[#0f1526] border border-slate-800 hover:border-red-600/60 rounded-xl overflow-hidden shadow-lg transition-all duration-300 flex flex-col justify-between">
                            
                            <div>
                                <!-- صورة المقالة بنفس تصميم صورة الفئة -->
                                <div class="relative h-40 w-full overflow-hidden bg-slate-950">
                                    <img src="{{ $imageUrl }}" alt="{{ $trans->title }}" 
                                         class="w-full h-full object-cover opacity-80 group-hover:opacity-100 group-hover:scale-105 transition-all duration-500 ease-in-out">
                                    <div class="absolute inset-0 bg-gradient-to-t from-[#0f1526] via-transparent to-transparent"></div>
                                    
                                    <!-- تاريخ النشر في أعلى الزاوية كشارة تكتيكية -->
                                    <span class="absolute top-3 right-3 bg-slate-950/80 backdrop-blur-md border border-slate-800 text-gray-300 text-[10px] font-mono px-2.5 py-1 rounded-lg">
                                        {{ $trans->created_at->format('M d, Y') }}
                                    </span>
                                </div>

                                <!-- محتوى البطاقة -->
                                <div class="p-4">
                                    <h2 class="text-base font-bold text-gray-200 group-hover:text-red-500 transition-colors line-clamp-2">
                                        {{ $trans->title }}
                                    </h2>
                                    <p class="text-xs text-gray-400 mt-1.5 line-clamp-2 leading-relaxed">
                                        {!! strip_tags($trans->description) !!}
                                    </p>
                                </div>
                            </div>

                            <!-- شريط الكاتب وزر القراءة في الأسفل -->
                            <div class="p-4 pt-3 border-t border-slate-850 flex items-center justify-between text-xs text-slate-400">
                                <span class="font-mono truncate max-w-[120px]">
                                    ✍️ 
                                    @if($authorId)
                                        <span onclick="event.preventDefault(); window.location.href='{{ route('authors.show', $authorId) }}';" class="hover:text-red-400 transition-colors underline decoration-slate-700 hover:decoration-red-400">
                                            {{ $authorName }}
                                        </span>
                                    @else
                                        {{ $authorName }}
                                    @endif
                                </span>

                                <span class="text-red-500 font-semibold flex items-center gap-1 shrink-0">
                                    <span>{{ __('home.read_more') }}</span>
                                    <svg class="w-4 h-4 transform group-hover:translate-x-1 rtl:group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </span>
                            </div>
                        </a>
                    @endif
                @endforeach
            </div>

            {{-- الترقيم --}}
            <div class="mt-10">
                {{ $articles->links() }}
            </div>
        @else
            {{-- حالة عدم وجود نتائج --}}
            <div class="bg-[#0f1526] border border-slate-800 rounded-xl p-12 text-center max-w-lg mx-auto my-12">
                <div class="w-16 h-16 bg-slate-800 text-slate-500 rounded-full flex items-center justify-center mx-auto mb-4">
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