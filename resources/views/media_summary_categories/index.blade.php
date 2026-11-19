@extends('layouts.main')
@section('title', __('Media Summary Categories') . ' - ' . config('app.name'))

@section('content')
<div class="container mx-auto px-4 py-8">

    <!-- ترويسة الصفحة -->
    <div class="bg-[#0f1526] border border-slate-800 rounded-xl px-5 py-4 mb-6 shadow-md flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="space-y-1">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 bg-red-600 rounded-sm shrink-0"></span>
                <h1 class="text-lg md:text-xl font-bold text-white tracking-wide">
                    {{ __('Media Summary Categories') }}
                </h1>
            </div>
            <p class="text-gray-400 text-xs max-w-2xl leading-relaxed">
                {{ __('Explore media summary categories and follow your favorite topics.') }}
            </p>
        </div>

        <div class="flex items-center gap-2 shrink-0 self-start sm:self-center">
            <span class="inline-flex items-center px-2.5 py-1 bg-slate-900 text-gray-300 text-[11px] font-mono rounded-md border border-slate-800">
                {{ $categories->count() }} {{ __('Categories') }}
            </span>
            <a href="{{ route('media-summaries.index') }}"
               class="inline-flex items-center px-2.5 py-1 bg-slate-900 hover:bg-slate-800 text-gray-300 text-[11px] font-medium rounded-md border border-slate-800 transition-colors">
                {{ __('All Media Summaries') }}
            </a>
        </div>
    </div>

    <!-- شبكة فئات ملخصات الوسائط -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @forelse($categories as $category)
            @php
                $trans = $category->translations->first();
                $name = $trans->name ?? $category->label;
                $desc = $trans->description ?? $trans->meta_description ?? '';
                $media = $category->getFirstMedia('featured_imgs') ?? $category->getFirstMedia();
                $imageUrl = $media
                    ? ($media->hasGeneratedConversion('medium') ? $media->getUrl('medium') : $media->getUrl())
                    : asset('images/default-category.jpg');
            @endphp

            <div class="group bg-[#0f1526] border border-slate-800 hover:border-red-600/60 rounded-xl overflow-hidden shadow-lg transition-all duration-300 flex flex-col justify-between">
                <div>
                    <div class="relative h-36 w-full overflow-hidden bg-slate-950">
                        @if($trans)
                            <a href="{{ route('media-summary-categories.show', ['id' => $trans->id, 'slug' => $trans->slug]) }}" class="block w-full h-full">
                                <img src="{{ $imageUrl }}"
                                     alt="{{ $name }}"
                                     loading="lazy"
                                     class="w-full h-full object-cover opacity-80 group-hover:opacity-100 group-hover:scale-105 transition-all duration-500">
                            </a>
                        @else
                            <img src="{{ $imageUrl }}"
                                 alt="{{ $name }}"
                                 loading="lazy"
                                 class="w-full h-full object-cover opacity-80">
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-[#0f1526] via-transparent to-transparent pointer-events-none"></div>

                        <span class="absolute top-3 right-3 bg-slate-950/85 backdrop-blur-md border border-slate-800 text-gray-300 text-[10px] font-mono px-2.5 py-1 rounded-lg">
                            {{ $category->media_summaries_count ?? 0 }} {{ __('Summaries') }}
                        </span>
                    </div>

                    <div class="p-4">
                        <h2 class="text-base font-bold text-gray-200 group-hover:text-red-500 transition-colors line-clamp-1">
                            @if($trans)
                                <a href="{{ route('media-summary-categories.show', ['id' => $trans->id, 'slug' => $trans->slug]) }}">
                                    {{ $name }}
                                </a>
                            @else
                                <span>{{ $name }}</span>
                            @endif
                        </h2>

                        <p class="text-xs text-gray-400 mt-1.5 line-clamp-2 leading-relaxed">
                            {{ $desc ?: __('Explore media summaries in this category.') }}
                        </p>
                    </div>
                </div>

                <div class="p-4 pt-3 border-t border-slate-800/80 flex items-center justify-between gap-2">
                    <x-follow-button type="media_summary_category" :model="$category" />

                    @if($trans)
                        <a href="{{ route('media-summary-categories.show', ['id' => $trans->id, 'slug' => $trans->slug]) }}"
                           class="text-xs font-semibold text-red-500 hover:text-red-400 flex items-center gap-1 shrink-0 transition">
                            <span>{{ __('Browse') }}</span>
                            <span class="rtl:rotate-180">&rarr;</span>
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center bg-[#0f1526] border border-slate-800 rounded-xl">
                <p class="text-sm text-gray-400">{{ __('No media summary categories found.') }}</p>
            </div>
        @endforelse
    </div>

</div>
@endsection