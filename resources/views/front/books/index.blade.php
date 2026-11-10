@extends("layouts.main")
@section('title', 'Book Summaries')

@section('content')
<div class="container mx-auto py-12 px-4 max-w-7xl">
    
    <!-- Page Header -->
    <div class="text-center max-w-2xl mx-auto mb-12">
        <h1 class="text-3xl font-extrabold text-white tracking-tight">Book Summaries & Reviews</h1>
        <p class="text-slate-400 mt-2 text-sm">Explore our curated collection of book summaries, key insights, and recommendations.</p>
    </div>

    <!-- Books Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @forelse($books as $book)
            @php
                $trans = $book->translate(app()->getLocale());
                if (!$trans || !$trans->title) {
                    $trans = $book->translations->first();
                }
            @endphp
            <div class="bg-slate-900 rounded-2xl border border-slate-800 shadow-xs overflow-hidden flex flex-col hover:border-slate-700 transition duration-200">
                
                <!-- Book Cover with Responsive srcset -->
                <div class="relative h-64 bg-slate-950 overflow-hidden">
                    @php
                        $media = null;
                        if ($trans && $trans->hasMedia('cover_imgs')) {
                            $media = $trans->getFirstMedia('cover_imgs');
                        } elseif ($book->hasMedia('cover_imgs')) {
                            $media = $book->getFirstMedia('cover_imgs');
                        }
                    @endphp

                    @if($media)
                        <img src="{{ $media->getUrl('medium') }}" 
                             srcset="{{ $media->getUrl('medium') }} 400w, {{ $media->getUrl('large') }} 800w" 
                             sizes="(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 300px"
                             alt="{{ $trans->title ?? 'Book Cover' }}" 
                             loading="lazy"
                             class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-slate-500 text-xs">No Cover Image</div>
                    @endif
                    
                    @if($trans && $trans->price)
                        <span class="absolute top-3 right-3 bg-slate-950/80 backdrop-blur-xs text-white text-xs font-bold px-2.5 py-1 rounded-full border border-slate-800">
                            {{ $trans->currency ?? 'USD' }} {{ $trans->price }}
                        </span>
                    @endif
                </div>

                <!-- Book Info -->
                <div class="p-5 flex-1 flex flex-col justify-between">
                    <div>
                        <!-- Category Badge -->
                        @if($book->bookCategory)
                            @php
                                $catTrans = $book->bookCategory->translations->where('locale', app()->getLocale())->first() ?? $book->bookCategory->translations->first();
                            @endphp
                            <span class="inline-block bg-slate-800 text-indigo-400 text-[10px] font-bold px-2 py-0.5 rounded-md mb-2 border border-slate-700">
                                {{ $catTrans->name ?? $book->bookCategory->label }}
                            </span>
                        @endif

                        <div class="flex items-center justify-between text-[11px] text-slate-500 mb-1">
                            <span>{{ $book->published_year ?? '-' }}</span>
                            <span>{{ $book->pages_count ? $book->pages_count . ' Pages' : '' }}</span>
                        </div>
                        <h3 class="text-base font-bold text-white line-clamp-1 mb-1">
                            {{ $trans->title ?? 'Untitled' }}
                        </h3>
                        <p class="text-xs text-slate-400 mb-3">By <span class="font-medium text-slate-300">{{ $trans->author ?? 'Unknown' }}</span></p>
                        
                        <p class="text-xs text-slate-400 line-clamp-2 mb-4">
                            {{ Str::limit(strip_tags($trans->summary ?? ''), 90) }}
                        </p>
                    </div>

                    <!-- Action Button -->
                    <div>
                        <a href="{{ route('books.show', ['id' => $book->id, 'slug' => $trans->slug ?? 'book']) }}" 
                           class="w-full block text-center bg-slate-800 hover:bg-indigo-600 text-slate-300 hover:text-white font-semibold text-xs py-2.5 rounded-xl transition duration-200 border border-slate-700/60 hover:border-indigo-600">
                            Read Summary &rarr;
                        </a>
                    </div>
                </div>

            </div>
        @empty
            <div class="col-span-full py-16 text-center text-slate-500 text-sm">
                No book summaries available at the moment. Please check back later!
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($books->hasPages())
        <div class="mt-10">
            {{ $books->links() }}
        </div>
    @endif

</div>
@endsection