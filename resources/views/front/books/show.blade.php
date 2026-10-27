@extends("layouts.main")
@section('title', $translation->title ?? 'Book Summary')

@section('content')
<div class="container mx-auto py-12 px-4 max-w-4xl">
    
    <!-- Back Link -->
    <div class="mb-6">
        <a href="{{ route('books.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-900 flex items-center gap-1 transition">
            &larr; Back to Book Summaries
        </a>
    </div>

    <!-- Main Card -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden p-6 md:p-10">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            
            <!-- Cover Image & Buy Button Column -->
            <div class="space-y-4">
                <div class="relative h-80 md:h-96 bg-slate-100 rounded-2xl overflow-hidden border border-slate-200 shadow-xs">
                    @if($book->hasMedia('cover_imgs'))
                        <img src="{{ $book->getFirstMediaUrl('cover_imgs', 'large') }}" alt="{{ $translation->title }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-slate-400 text-xs">No Cover Image</div>
                    @endif
                </div>

                @if($translation->affiliate_url)
                    <a href="{{ $translation->affiliate_url }}" target="_blank" rel="nofollow noopener" 
                       class="w-full flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm py-3 px-4 rounded-xl shadow-md transition duration-200">
                        <span>Buy Book</span>
                        @if($translation->price)
                            <span class="bg-emerald-800/60 px-2 py-0.5 rounded-md text-xs font-semibold">
                                {{ $translation->currency ?? 'USD' }} {{ $translation->price }}
                            </span>
                        @endif
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                @endif
            </div>

            <!-- Book Content & Details Column -->
            <div class="md:col-span-2 space-y-6">
                <div>
                    <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight mb-2">
                        {{ $translation->title ?? 'N/A' }}
                    </h1>
                    <p class="text-sm text-slate-500">Author: <span class="font-bold text-slate-800">{{ $translation->author ?? 'N/A' }}</span></p>
                </div>

                <!-- Meta Badges -->
                <div class="flex flex-wrap gap-3 py-3 border-y border-slate-100 text-xs text-slate-600">
                    @if($book->published_year)
                        <div class="bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-200">
                            <span class="text-slate-400 block text-[10px]">Published Year</span>
                            <span class="font-semibold text-slate-800">{{ $book->published_year }}</span>
                        </div>
                    @endif
                    @if($book->pages_count)
                        <div class="bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-200">
                            <span class="text-slate-400 block text-[10px]">Pages</span>
                            <span class="font-semibold text-slate-800">{{ $book->pages_count }} Pages</span>
                        </div>
                    @endif
                    @if($translation->price)
                        <div class="bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-200">
                            <span class="text-slate-400 block text-[10px]">Price</span>
                            <span class="font-semibold text-emerald-600">{{ $translation->currency ?? 'USD' }} {{ $translation->price }}</span>
                        </div>
                    @endif
                </div>

                <!-- Summary / Review Text -->
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Book Summary & Insights</h3>
                    <div class="prose prose-slate text-sm text-slate-700 leading-relaxed whitespace-pre-line">
                        {!! $translation->summary ?? 'No summary provided for this language yet.' !!}
                    </div>
                </div>

            </div>

        </div>
    </div>

</div>
@endsection