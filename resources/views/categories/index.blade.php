@extends('layouts.main') {{-- أو التخطيط المعتمد لديك --}}

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- عنوان الصفحة -->
    <div class="mb-8 border-b border-slate-800 pb-4">
        <h1 class="text-2xl md:text-3xl font-black text-white tracking-wider flex items-center gap-3">
            <span class="w-3 h-3 bg-red-600 rounded-sm"></span>
            {{ __('nav.categories') }}
        </h1>
        <p class="text-xs md:text-sm text-gray-400 mt-1">
            {{ __('Explore all case categories and investigative archives.') }}
        </p>
    </div>

    <!-- شبكة التصنيفات -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @forelse($categories as $cat)
            @php
                $cat_trans = $cat->translations->first();
                $catName = $cat_trans ? $cat_trans->name : $cat->sign;
                $catDesc = $cat_trans ? $cat_trans->description : '';
                $catSlug = $cat_trans ? $cat_trans->slug : $cat->sign;
                $catId = $cat_trans ? $cat_trans->id : $cat->id;
                
                // جلب صورة البانر من Spatie Media Library إن وجدت
                $imageUrl = $cat->getFirstMediaUrl('featured_imgs', 'medium') ?: asset('images/default-category.jpg');
                $articlesCount = $cat->articles->count();
            @endphp

            <a href="{{ route('categories.show', ['id' => $catId, 'slug' => $catSlug]) }}" 
               class="group relative bg-[#0f1526] border border-slate-800 hover:border-red-600/60 rounded-xl overflow-hidden shadow-lg transition-all duration-300 flex flex-col">
                
                <!-- صورة التصنيف -->
                <div class="relative h-40 w-full overflow-hidden bg-slate-950">
                    <img src="{{ $imageUrl }}" alt="{{ $catName }}" 
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 opacity-80 group-hover:opacity-100">
                    <div class="absolute inset-0 bg-gradient-to-t from-[#0f1526] via-transparent to-transparent"></div>
                    
                    <!-- عداد المقالات -->
                    <span class="absolute top-3 right-3 bg-slate-950/80 backdrop-blur-md border border-slate-800 text-gray-300 text-[10px] font-bold px-2.5 py-1 rounded-lg">
                        {{ $articlesCount }} {{ __('Articles') }}
                    </span>
                </div>

                <!-- محتوى البطاقة -->
                <div class="p-4 flex-1 flex flex-col justify-between">
                    <div>
                        <h2 class="text-base font-bold text-gray-200 group-hover:text-red-500 transition-colors">
                            {{ $catName }}
                        </h2>
                        <p class="text-xs text-gray-400 mt-1.5 line-clamp-2">
                            {{ $catDesc ?: __('No description provided.') }}
                        </p>
                    </div>

                    <div class="mt-4 pt-3 border-t border-slate-850 flex items-center justify-between text-xs text-red-500 font-semibold">
                        <span>{{ __('Explore Archives') }}</span>
                        <svg class="w-4 h-4 transform group-hover:translate-x-1 rtl:group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </div>
            </a>
        @empty
            <div class="col-span-full py-12 text-center bg-[#0f1526] border border-slate-800 rounded-xl">
                <p class="text-sm text-gray-400">{{ __('No categories found.') }}</p>
            </div>
        @endforelse
    </div>
</div>
@endsection