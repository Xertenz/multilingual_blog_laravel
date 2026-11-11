@extends("layouts.main")
@section('title', 'Book Summaries')

@section('content')
<div class="container mx-auto px-4 py-8">

    <!-- Page Header — موحّد مع صفحتي الألعاب والفئات -->
    <div class="bg-[#0f1526] border border-slate-800 rounded-xl px-5 py-4 mb-6 shadow-md flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="space-y-1">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 bg-red-600 rounded-sm shrink-0"></span>
                <h1 class="text-lg md:text-xl font-bold text-white tracking-wide">
                    Book Summaries & Reviews
                </h1>
            </div>
            <p class="text-gray-400 text-xs max-w-2xl leading-relaxed">
                Explore our curated collection of book summaries, key insights, and recommendations.
            </p>
        </div>

        <div class="shrink-0 self-start sm:self-center">
            <span class="inline-block px-2.5 py-1 bg-slate-900 text-gray-300 text-[11px] font-mono rounded-md border border-slate-800">
                {{ $books->total() }} {{ __('Books') }}
            </span>
        </div>
    </div>

    <!-- Books Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @forelse($books as $book)
            @php
                $trans = $book->translate(app()->getLocale());
                if (!$trans || !$trans->title) {
                    $trans = $book->translations->first();
                }

                // صورة الغلاف
                $media = null;
                if ($trans && $trans->hasMedia('cover_imgs')) {
                    $media = $trans->getFirstMedia('cover_imgs');
                } elseif ($book->hasMedia('cover_imgs')) {
                    $media = $book->getFirstMedia('cover_imgs');
                }
                $imageUrl = $media ? $media->getUrl('medium') : asset('images/default-book.jpg');

                // اسم الفئة المترجم
                $catName = null;
                if ($book->bookCategory) {
                    $catTrans = $book->bookCategory->translations->where('locale', app()->getLocale())->first()
                             ?? $book->bookCategory->translations->first();
                    $catName = $catTrans->name ?? $book->bookCategory->label;
                }
            @endphp

            <a href="{{ route('books.show', ['id' => $book->id, 'slug' => $trans->slug ?? 'book']) }}"
               class="group relative bg-[#0f1526] border border-slate-800 hover:border-red-600/60 rounded-xl overflow-hidden shadow-lg transition-all duration-300 flex flex-col justify-between">

                <div>
                    <!-- صورة الغلاف -->
                    <div class="relative h-80 w-full overflow-hidden bg-slate-950">
                        @if($media)
                            <img src="{{ $imageUrl }}"
                                 alt="{{ $trans->title ?? 'Book Cover' }}"
                                 loading="lazy"
                                 class="w-full h-full object-cover opacity-80 group-hover:opacity-100 group-hover:scale-105 transition-all duration-500 ease-in-out">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-slate-500 text-xs">No Cover Image</div>
                        @endif

                        <div class="absolute inset-0 bg-gradient-to-t from-[#0f1526] via-transparent to-transparent"></div>

                        <!-- شارة الفئة أعلى اليسار -->
                        @if($catName)
                            <span class="absolute top-3 left-3 bg-slate-950/80 backdrop-blur-md border border-slate-800 text-gray-300 text-[10px] font-mono px-2.5 py-1 rounded-lg">
                                {{ $catName }}
                            </span>
                        @endif

                        <!-- سنة النشر أعلى اليمين -->
                        @if($book->published_year)
                            <span class="absolute top-3 right-3 bg-slate-950/80 backdrop-blur-md border border-slate-800 text-gray-300 text-[10px] font-mono px-2.5 py-1 rounded-lg">
                                {{ $book->published_year }}
                            </span>
                        @endif

                        <!-- شارة السعر أسفل اليمين -->
                        @if($trans && $trans->price)
                            <span class="absolute bottom-3 right-3 bg-red-600/90 backdrop-blur-md text-white text-[10px] font-bold px-2.5 py-1 rounded-lg shadow-lg">
                                {{ $trans->currency ?? 'USD' }} {{ $trans->price }}
                            </span>
                        @endif
                    </div>

                    <!-- محتوى البطاقة -->
                    <div class="p-4">
                        <!-- المؤلف كسطر صغير -->
                        <div class="text-[11px] text-indigo-400 font-medium mb-1 truncate">
                            By {{ $trans->author ?? 'Unknown' }}
                        </div>

                        <h2 class="text-base font-bold text-gray-200 group-hover:text-red-500 transition-colors line-clamp-2">
                            {{ $trans->title ?? 'Untitled' }}
                        </h2>
                        <p class="text-xs text-gray-400 mt-1.5 line-clamp-2 leading-relaxed">
                            {{ Str::limit(strip_tags($trans->summary ?? ''), 100) }}
                        </p>
                    </div>
                </div>

                <!-- شريط الصفحات وزر القراءة في الأسفل -->
                <div class="p-4 pt-3 border-t border-slate-850 flex items-center justify-between text-xs text-slate-400">
                    <span class="font-mono truncate max-w-[120px]">
                        📖
                        {{ $book->pages_count ? $book->pages_count . ' ' . __('Pages') : __('Book') }}
                    </span>

                    <span class="text-red-500 font-semibold flex items-center gap-1 shrink-0">
                        <span>{{ __('Read Summary') }}</span>
                        <svg class="w-4 h-4 transform group-hover:translate-x-1 rtl:group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </span>
                </div>
            </a>
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