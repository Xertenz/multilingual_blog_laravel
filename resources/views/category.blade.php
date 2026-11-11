@extends('layouts.main')

{{-- 1️⃣ عنوان الصفحة والـ Meta Tags المترجمة للفئة --}}
@section('title', $category_trans->meta_title ?? $category_trans->name ?? $category->label)

@section('meta_tags')
    <meta name="description" content="{{ $category_trans->meta_description ?? Str::limit(strip_tags($category_trans->description ?? ''), 150) }}">
    <meta property="og:title" content="{{ $category_trans->title ?? $category_trans->category->label }}">
    <meta property="og:description" content="{{ $category_trans->meta_description ?? '' }}">
    
    {{-- رابط الـ Canonical للـ SEO --}}
    <link rel="canonical" href="{{ route('categories.show', ['id' => $category_trans->id, 'slug' => $category_trans->slug ?? '']) }}">
@endsection

@section('content')
<div class="container mx-auto px-4 py-8">

    {{-- 2️⃣ رأس الصفحة: تصميم مدمج ونحيف لتوفير المساحة --}}
    <div class="bg-[#0f1526] border border-slate-800 rounded-xl px-5 py-4 mb-6 shadow-md flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="space-y-1">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 bg-red-600 rounded-sm shrink-0"></span>
                <h1 class="text-lg md:text-xl font-bold text-white tracking-wide">
                    {{ $category_trans->name ?? $category_trans->category->label }}
                </h1>
            </div>
            
            {{-- وصف الفئة المترجم (مختصر) --}}
            @if(isset($category_trans->description) && !empty($category_trans->description))
                <p class="text-gray-400 text-xs max-w-2xl leading-relaxed">
                    {{ $category_trans->description }}
                </p>
            @endif
        </div>

        <div class="shrink-0 self-start sm:self-center">
            <span class="inline-block px-2.5 py-1 bg-slate-900 text-gray-300 text-[11px] font-mono rounded-md border border-slate-800">
                {{ __('category.total_articles', ['count' => $articles->total()]) }}
            </span>
        </div>
    </div>

    {{-- 3️⃣ شبكة مقالات الفئة (بنفس تصميم وتنسيق بطاقات الفئات العامة) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @forelse($articles as $article)
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
        @empty
            <div class="col-span-full py-12 text-center bg-[#0f1526] border border-slate-800 rounded-xl">
                <p class="text-sm text-gray-400">{{ __('category.no_articles') }}</p>
            </div>
        @endforelse
    </div>

    {{-- 4️⃣ الترقيم (Pagination) --}}
    <div class="mt-8">
        {{ $articles->links() }}
    </div>

</div>
@endsection