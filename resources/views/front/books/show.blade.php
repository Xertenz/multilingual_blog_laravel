@extends("layouts.main")
@section('title', $translation->title ?? 'Book Summary')

@section('content')
<div class="container mx-auto py-12 px-4 max-w-4xl">
    
    <!-- Back Link -->
    <div class="mb-6">
        <a href="{{ route('books.index') }}" class="text-xs font-semibold text-slate-400 hover:text-white flex items-center gap-1 transition">
            &larr; Back to Book Summaries
        </a>
    </div>

    <!-- Main Card -->
    <div class="bg-slate-900 rounded-3xl border border-slate-800 shadow-xs overflow-hidden p-6 md:p-10 mb-8 text-slate-200">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            
            <!-- Cover Image & Buy Button Column with optimized srcset -->
            <div class="space-y-4">
                <div class="relative h-80 md:h-96 bg-slate-950 rounded-2xl overflow-hidden border border-slate-800 shadow-xs">
                    @php
                        $media = null;
                        if ($translation && $translation->hasMedia('cover_imgs')) {
                            $media = $translation->getFirstMedia('cover_imgs');
                        } elseif ($book->hasMedia('cover_imgs')) {
                            $media = $book->getFirstMedia('cover_imgs');
                        }
                    @endphp

                    @if($media)
                        <img src="{{ $media->getUrl('large') }}" 
                             srcset="{{ $media->getUrl('medium') }} 500w, {{ $media->getUrl('large') }} 1000w" 
                             sizes="(max-width: 768px) 100vw, 400px"
                             alt="{{ $translation->title ?? 'Book Cover' }}" 
                             fetchpriority="high"
                             loading="eager"
                             class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-slate-500 text-xs">No Cover Image</div>
                    @endif
                </div>

                @if($translation->affiliate_url)
                    <a href="{{ $translation->affiliate_url }}" target="_blank" rel="nofollow noopener" 
                       class="w-full flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm py-3 px-4 rounded-xl shadow-md transition duration-200">
                        <span>Buy Book</span>
                        @if($translation->price)
                            <span class="bg-emerald-800/80 px-2 py-0.5 rounded-md text-xs font-semibold">
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
                    <h1 class="text-2xl md:text-3xl font-extrabold text-white tracking-tight mb-2">
                        {{ $translation->title ?? 'N/A' }}
                    </h1>
                    <p class="text-sm text-slate-400">Author: <span class="font-bold text-slate-200">{{ $translation->author ?? 'N/A' }}</span></p>
                </div>

                <!-- Author card here -->
                @include('partials.author-card', ['user' => $translation->user, 'label' => __('Translated & Summarized By'), 'bgClass' => 'bg-slate-800/80 border border-slate-700/60 text-slate-200'])

                <!-- Meta Badges -->
                <div class="flex flex-wrap gap-3 py-3 border-y border-slate-800 text-xs text-slate-300">

                    @if($book->bookCategory)
                        @php
                            $catTrans = $book->bookCategory->translations->where('locale', app()->getLocale())->first() ?? $book->bookCategory->translations->first();
                        @endphp
                        <div class="bg-slate-800 px-3 py-1.5 rounded-lg border border-slate-700">
                            <span class="text-slate-400 block text-[10px]">Category</span>
                            <span class="font-semibold text-indigo-400">{{ $catTrans->name ?? $book->bookCategory->label }}</span>
                        </div>
                    @endif

                    @if($book->published_year)
                        <div class="bg-slate-800 px-3 py-1.5 rounded-lg border border-slate-700">
                            <span class="text-slate-400 block text-[10px]">Published Year</span>
                            <span class="font-semibold text-slate-200">{{ $book->published_year }}</span>
                        </div>
                    @endif

                    @if($book->pages_count)
                        <div class="bg-slate-800 px-3 py-1.5 rounded-lg border border-slate-700">
                            <span class="text-slate-400 block text-[10px]">Pages</span>
                            <span class="font-semibold text-slate-200">{{ $book->pages_count }} Pages</span>
                        </div>
                    @endif

                    @if($translation->price)
                        <div class="bg-slate-800 px-3 py-1.5 rounded-lg border border-slate-700">
                            <span class="text-slate-400 block text-[10px]">Price</span>
                            <span class="font-semibold text-emerald-400">{{ $translation->currency ?? 'USD' }} {{ $translation->price }}</span>
                        </div>
                    @endif

                    <div class="bg-slate-800 px-3 py-1.5 rounded-lg border border-slate-700">
                        <span class="text-slate-400 block text-[10px]">Views</span>
                        <span class="font-semibold text-slate-200">{{ number_format($translation->views) }}</span>
                    </div>

                </div>

                <!-- Summary / Review Text -->
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Book Summary & Insights</h3>
                    <div class="prose prose-invert max-w-none text-sm text-slate-300 leading-relaxed whitespace-pre-line">
                        {!! $translation->summary ?? 'No summary provided for this language yet.' !!}
                    </div>
                </div>

                <!-- Book Tags -->
                @if($book->bookTags->count() > 0)
                    <div class="pt-4 border-t border-slate-800">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Book Tags</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($book->bookTags as $tag)
                                @php
                                    $tagTrans = $tag->translations->where('locale', app()->getLocale())->first() ?? $tag->translations->first();
                                    $tagName = $tagTrans->name ?? $tag->label;
                                @endphp
                                <span class="bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs px-3 py-1 rounded-full transition font-medium border border-slate-700/60">
                                    # {{ $tagName }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>

        </div>

    </div>

    <!-- قسم التعليقات والنقاشات الموحد لملخص الكتاب -->
    <livewire:comments-section :model="$book" />

</div>
@endsection

@push('scripts')
<script>
    async function toggleFollowAuthor(event, authorId) {
        event.preventDefault();
        const baseUrl = "{{ route('authors.follow', ':id') }}";
        const url = baseUrl.replace(':id', authorId);
        
        const btn = document.getElementById(`follow-author-btn-${authorId}`);
        const textSpan = document.getElementById(`follow-author-text-${authorId}`);
        
        try {
            let response = await fetch(url, {
                method: "POST",
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) throw new Error('Network response was not ok');
            let data = await response.json();
            
            if (data.is_following) {
                btn.className = "inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-xl transition duration-200 border bg-rose-950/80 text-rose-400 border-rose-800/60";
                textSpan.textContent = "{{ __('Following') }}";
                showToast('success', "Successfully followed the author!");
            } else {
                btn.className = "inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-xl transition duration-200 border bg-slate-800 text-gray-300 border-slate-700 hover:text-white";
                textSpan.textContent = "{{ __('Follow Author') }}";
                showToast('success', "Successfully unfollowed the author!");
            }
        } catch (error) {
            console.error('Error toggling author follow status:', error);
        }
    }
</script>
@endpush