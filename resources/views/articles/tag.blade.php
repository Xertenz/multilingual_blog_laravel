@extends('layouts.main')
@section('title', '# ' . ($tag_trans->meta_title ?? $tag_trans->name ?? $tag_trans->tag?->label) . ' - ' . config('app.name'))

@section('meta_tags')
    <meta name="description" content="{{ $tag_trans->meta_description ?? $tag_trans->description ?? '' }}">
@endsection

@section('content')
<div class="container mx-auto px-4 py-8">

    <!-- رأس الصفحة — موحّد مع بقية الصفحات -->
    <div class="bg-[#0f1526] border border-slate-800 rounded-xl px-5 py-4 mb-6 shadow-md flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="space-y-1.5">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 bg-red-600 rounded-sm shrink-0"></span>
                <h1 class="text-lg md:text-xl font-bold text-white tracking-wide">
                    # {{ $tag_trans->name ?? $tag_trans->tag?->label }}
                </h1>
            </div>

            @if(!empty($tag_trans->description))
                <p class="text-gray-400 text-xs max-w-2xl leading-relaxed">
                    {{ $tag_trans->description }}
                </p>
            @endif
        </div>

        <div class="flex flex-wrap items-center gap-2 shrink-0 self-start md:self-center">
            @if($tag_trans->tag)
                <x-follow-button type="article_tag" :model="$tag_trans->tag" />
            @endif

            <span class="inline-flex items-center px-2.5 py-1.5 bg-slate-900 text-gray-300 text-[11px] font-mono rounded-lg border border-slate-800">
                {{ $articles->total() }} {{ __('Articles') }}
            </span>

            <a href="{{ route('article-categories.index') }}"
               class="inline-flex items-center px-2.5 py-1.5 bg-slate-900 hover:bg-slate-800 text-gray-300 text-[11px] font-medium rounded-lg border border-slate-800 transition-colors">
                {{ __('All Categories') }}
            </a>
        </div>
    </div>

    <!-- شبكة المقالات المرتبطة بالوسم -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @forelse($articles as $article)
            @php
                $trans = $article->translations->firstWhere('locale', app()->getLocale())
                    ?? $article->translations->first();
            @endphp

            @if($trans)
                @php
                    $media = $article->getFirstMedia('featured_imgs') ?? $article->getFirstMedia('content_imgs');
                    $imageUrl = $media ? $media->getUrl('medium') : asset('images/default-article.jpg');
                    $catTrans = $article->category?->translations->firstWhere('locale', app()->getLocale())
                        ?? $article->category?->translations->first();
                    $excerpt = $trans->meta_description ?? $trans->description ?? '';
                @endphp

                <a href="{{ route('articles.show', ['id' => $trans->id, 'slug' => $trans->slug]) }}"
                   class="group relative bg-[#0f1526] border border-slate-800 hover:border-red-600/60 rounded-xl overflow-hidden shadow-lg transition-all duration-300 flex flex-col justify-between">

                    <div>
                        <!-- صورة المقال -->
                        <div class="relative h-40 w-full overflow-hidden bg-slate-950">
                            <img src="{{ $imageUrl }}"
                                 alt="{{ $trans->title }}"
                                 loading="lazy"
                                 class="w-full h-full object-cover opacity-80 group-hover:opacity-100 group-hover:scale-105 transition-all duration-500 ease-in-out">
                            <div class="absolute inset-0 bg-gradient-to-t from-[#0f1526] via-transparent to-transparent"></div>

                            <!-- شارة الفئة أعلى اليسار -->
                            @if($catTrans)
                                <span class="absolute top-3 left-3 bg-slate-950/80 backdrop-blur-md border border-slate-800 text-gray-300 text-[10px] font-mono px-2.5 py-1 rounded-lg">
                                    {{ $catTrans->name }}
                                </span>
                            @endif

                            <!-- شارة التاريخ أعلى اليمين -->
                            <span class="absolute top-3 right-3 bg-slate-950/80 backdrop-blur-md border border-slate-800 text-gray-300 text-[10px] font-mono px-2.5 py-1 rounded-lg">
                                {{ $trans->created_at->format('M j, Y') }}
                            </span>
                        </div>

                        <!-- محتوى البطاقة -->
                        <div class="p-4">
                            <h2 class="text-base font-bold text-gray-200 group-hover:text-red-500 transition-colors line-clamp-2">
                                {{ $trans->title }}
                            </h2>
                            <p class="text-xs text-gray-400 mt-1.5 line-clamp-2 leading-relaxed">
                                {{ $excerpt ? Str::limit(strip_tags($excerpt), 120) : __('No description provided.') }}
                            </p>
                        </div>
                    </div>

                    <!-- الشريط السفلي — موحّد مع بقية البطاقات -->
                    <div class="p-4 pt-3 border-t border-slate-850 flex items-center justify-between text-xs text-slate-400">
                        <span class="font-mono truncate max-w-[120px]">
                            ✍️ {{ $trans->author->name ?? $article->author->name ?? __('Investigator Team') }}
                        </span>

                        <span class="text-red-500 font-semibold flex items-center gap-1 shrink-0">
                            <span>{{ __('Read Article') }}</span>
                            <svg class="w-4 h-4 transform group-hover:translate-x-1 rtl:group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </span>
                    </div>
                </a>
            @endif
        @empty
            <div class="col-span-full py-12 text-center bg-[#0f1526] border border-slate-800 rounded-xl">
                <p class="text-sm text-gray-400">{{ __('No published articles found under this tag.') }}</p>
            </div>
        @endforelse
    </div>

    <!-- الترقيم -->
    @if($articles->hasPages())
        <div class="mt-10">
            {{ $articles->links() }}
        </div>
    @endif

</div>
@endsection