@extends("layouts.main") {{-- أو قالب الواجهة العامة المعتمد في مشروعك --}}
@section('title', 'Book Summaries')

@section('content')
<div class="container mx-auto py-12 px-4 max-w-7xl">
    
    <!-- Page Header -->
    <div class="text-center max-w-2xl mx-auto mb-12">
        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Book Summaries & Reviews</h1>
        <p class="text-slate-500 mt-2 text-sm">Explore our curated collection of book summaries, key insights, and recommendations.</p>
    </div>

    <!-- Books Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @forelse($books as $book)
            @php
                $trans = $book->translate(app()->getLocale());
                // Fallback للغة الإنجليزية أو الأولى إذا لم توجد ترجمة للغة الحالية
                if (!$trans || !$trans->title) {
                    $trans = $book->translations->first();
                }
            @endphp
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden flex flex-col hover:shadow-md transition duration-200">
                
                <!-- Book Cover -->
                <div class="relative h-64 bg-slate-100 overflow-hidden">
                    @if($book->hasMedia('cover_imgs'))
                        <img src="{{ $book->getFirstMediaUrl('cover_imgs', 'medium') }}" alt="{{ $trans->title ?? 'Book Cover' }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-slate-400 text-xs font-medium">No Cover Image</div>
                    @endif
                    
                    @if($trans && $trans->price)
                        <span class="absolute top-3 right-3 bg-slate-900/80 backdrop-blur-xs text-white text-xs font-bold px-2.5 py-1 rounded-full">
                            {{ $trans->currency ?? 'USD' }} {{ $trans->price }}
                        </span>
                    @endif
                </div>

                <!-- Book Info -->
                <div class="p-5 flex-1 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between text-[11px] text-slate-400 mb-1">
                            <span>{{ $book->published_year ?? '-' }}</span>
                            <span>{{ $book->pages_count ? $book->pages_count . ' Pages' : '' }}</span>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 line-clamp-1 mb-1">
                            {{ $trans->title ?? 'Untitled' }}
                        </h3>
                        <p class="text-xs text-slate-500 mb-3">By <span class="font-medium text-slate-700">{{ $trans->author ?? 'Unknown' }}</span></p>
                        
                        <p class="text-xs text-slate-600 line-clamp-2 mb-4">
                            {{ Str::limit(strip_tags($trans->summary ?? ''), 90) }}
                        </p>
                    </div>

                    <!-- Action Button -->
                    <div>
                        <a href="{{ route('books.show', ['id' => $book->id, 'slug' => $trans->slug ?? 'book']) }}" 
                           class="w-full block text-center bg-indigo-50 hover:bg-indigo-600 text-indigo-600 hover:text-white font-semibold text-xs py-2.5 rounded-xl transition duration-200">
                            Read Summary &rarr;
                        </a>
                    </div>
                </div>

            </div>
        @empty
            <div class="col-span-full py-16 text-center text-slate-400 text-sm">
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