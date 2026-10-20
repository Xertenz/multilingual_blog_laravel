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

    {{-- 2️⃣ رأس الصفحة: اسم الفئة والوصف المترجمين --}}
    <div class="bg-[#1e293b]/60 border border-slate-800 rounded-2xl p-6 md:p-8 mb-8 text-center shadow-lg">
        <h1 class="text-3xl md:text-4xl font-extrabold text-white mb-3">
            📂 {{ $category_trans->name ?? $category_trans->category->label }}
        </h1>
        
        {{-- وصف الفئة المترجم (إذا كان متوفراً) --}}
        @if(isset($category_trans->description) && !empty($category_trans->description))
            <p class="text-gray-300 text-sm md:text-base max-w-2xl mx-auto mb-4 leading-relaxed">
                {{ $category_trans->description }}
            </p>
        @endif

        <span class="inline-block px-3 py-1 bg-slate-800/80 text-gray-400 text-xs rounded-full border border-slate-700/50">
            {{  __('category.total_articles', ['count' => $articles->total()]) }}
        </span>
    </div>

    {{-- 3️⃣ شبكة مقالات الفئة --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($articles as $article)
            @php
                // جلب ترجمة المقالة المتوافقة مع لغة التصفح الحالية
                $trans = $article->translations->where('locale', app()->getLocale())->first() 
                        ?? $article->translations->first();
            @endphp

            @if($trans)
                <article class="bg-[#1e293b]/60 border border-slate-800 rounded-2xl overflow-hidden group flex flex-col justify-between hover:border-slate-700 transition-all">
                    <div>
                        {{-- صورة المقالة --}}
                        <div class="aspect-video overflow-hidden relative [&_img]:!h-full [&_img]:!w-full [&_img]:!object-cover [&_picture]:!h-full [&_picture]:!w-full">
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
                        </div>

                        {{-- التفاصيل الخاصة بالمقالة المترجمة --}}
                        <div class="p-5">
                            <div class="text-xs text-gray-400 mb-2">
                                {{ $article->created_at->format('M d, Y') }}
                            </div>

                            <h3 class="text-lg capitalize font-bold text-white group-hover:text-red-400 transition-colors line-clamp-2 mb-2">
                                <a href="{{ route('articles.show', ['id' => $trans->id, 'slug' => $trans->slug]) }}">
                                    {{ $trans->title }}
                                </a>
                            </h3>

                            <p class="text-gray-400 first-letter:uppercase text-xs line-clamp-3 leading-relaxed">
                                {{ Str::limit(strip_tags($trans->description), 120) }}
                            </p>
                        </div>
                    </div>

                    {{-- أسفل الكارت --}}
                    <div class="p-5 pt-0 mt-auto flex justify-between items-center text-xs">
                        <span class="text-slate-400">{{ $article->author->name ?? __('home.anonymous') }}</span>
                        <a href="{{ route('articles.show', ['id' => $trans->id, 'slug' => $trans->slug]) }}" 
                           class="text-red-400 font-semibold group-hover:underline flex items-center gap-1">
                            {{ __('home.read_more') }} &rarr;
                        </a>
                    </div>
                </article>
            @endif
        @empty
            <div class="col-span-full text-center py-12 bg-[#1e293b]/30 rounded-2xl border border-slate-800 text-gray-400">
                {{ __('category.no_articles') }}
            </div>
        @endforelse
    </div>

    {{-- 4️⃣ الترقيم (Pagination) --}}
    <div class="mt-8">
        {{ $articles->links() }}
    </div>

</div>
@endsection