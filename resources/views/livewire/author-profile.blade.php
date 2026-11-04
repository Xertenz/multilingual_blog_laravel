<div class="container mx-auto px-4 py-12 max-w-5xl text-white">
    
    <!-- بطاقة معلومات الكاتب الرئيسية -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-6 md:p-8 shadow-2xl mb-12 flex flex-col md:flex-row items-center gap-6 md:gap-8">
        
        <!-- صورة الصورة الشخصية / الصورة الرمزية -->
        <div class="relative">
            <div class="w-28 h-28 md:w-32 md:h-32 rounded-full overflow-hidden border-2 border-red-600/60 shadow-lg shadow-red-600/10 bg-slate-800 flex items-center justify-center text-3xl font-bold text-red-500 uppercase">
                @if($author->profile_photo_url ?? false)
                    <img src="{{ $author->profile_photo_url }}" alt="{{ $author->name }}" class="w-full h-full object-cover">
                @else
                    {{ substr($author->name, 0, 2) }}
                @endif
            </div>
        </div>

        <!-- تفاصيل الكاتب والإحصائيات -->
        <div class="flex-1 text-center md:text-left rtl:md:text-right space-y-3">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl md:text-3xl font-black text-white tracking-tight">
                        {{ $author->name }}
                    </h1>
                    <p class="text-xs text-red-400 font-semibold tracking-wider uppercase mt-1">
                        🕵️‍♂️ Investigator / Author
                    </p>
                </div>

                <!-- زر المتابعة التفاعلي -->
                @auth
                    @if(auth()->id() !== $author->id)
                        @php
                            $isFollowingAuthor = auth()->user()->followingAuthors->contains($author->id);
                        @endphp

                        <button type="button" 
                                id="follow-author-btn-{{ $author->id }}"
                                onclick="toggleFollowAuthor(event, {{ $author->id }})"
                                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 text-xs font-bold rounded-xl transition duration-200 border shadow-md
                                {{ $isFollowingAuthor 
                                    ? 'bg-rose-950/80 text-rose-400 border-rose-800/60 hover:bg-rose-900/60' 
                                    : 'bg-red-600 text-white border-red-500 hover:bg-red-700 shadow-red-600/20' 
                                }}">
                            <span id="follow-author-text-{{ $author->id }}">
                                {{ $isFollowingAuthor ? __('Following') : __('Follow Author') }}
                            </span>
                        </button>
                    @endif
                @endauth
            </div>

            <!-- إحصائيات الكاتب -->
            <div class="flex items-center justify-center md:justify-start gap-6 pt-2 text-xs text-slate-400 border-t border-slate-800/80">
                <div>
                    <span class="font-bold text-white text-sm block" id="author-followers-count-{{ $author->id }}">{{ $author->author_followers_count }}</span>
                    <span>Followers</span>
                </div>
                <div class="h-6 w-px bg-slate-800"></div>
                <div>
                    <span class="font-bold text-white text-sm block">{{ $articles->total() }}</span>
                    <span>Articles</span>
                </div>
            </div>
        </div>
    </div>

    <!-- شبكة مقالات الكاتب -->
    <div class="space-y-6">
        <h2 class="text-xl font-bold text-white flex items-center gap-2 pb-4 border-b border-slate-800">
            <span class="w-2.5 h-2.5 rounded-full bg-red-600"></span>
            Articles by {{ $author->name }}
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($articles as $artTrans)
                @php
                    $catTrans = $artTrans->article->category?->translations->where('locale', app()->getLocale())->first();
                @endphp
                <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-5 flex flex-col justify-between hover:border-slate-700 transition space-y-4">
                    <div>
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-[11px] font-semibold px-2.5 py-0.5 rounded-md bg-slate-800 text-red-400 border border-slate-700/50">
                                {{ $catTrans->name ?? 'General' }}
                            </span>
                            <span class="text-[11px] text-slate-500 font-mono">{{ $artTrans->created_at->format('M j, Y') }}</span>
                        </div>

                        <h3 class="text-base font-bold text-white line-clamp-2 hover:text-red-400 transition mb-2">
                            <a href="{{ route('articles.show', ['slug' => $artTrans->slug, 'id' => $artTrans->id]) }}">
                                {{ $artTrans->title }}
                            </a>
                        </h3>

                        <p class="text-slate-400 text-xs line-clamp-3 leading-relaxed">
                            {{ Str::limit(strip_tags($artTrans->description), 110) }}
                        </p>
                    </div>

                    <div class="pt-3 border-t border-slate-800/80 flex items-center justify-between text-[11px] text-slate-500">
                        <span>📖 {{ ceil(str_word_count(strip_tags($artTrans->description)) / 200) }} min read</span>
                        <a href="{{ route('articles.show', ['slug' => $artTrans->slug, 'id' => $artTrans->id]) }}" class="text-red-400 hover:underline font-semibold">Read Article &rarr;</a>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-16 text-center text-slate-500 text-sm italic">
                    No articles published by this author in the current language yet.
                </div>
            @endforelse
        </div>

        <!-- التصفح Pagination -->
        <div class="mt-8">
            {{ $articles->links() }}
        </div>
    </div>

</div>

@push('scripts')
<script>
    async function toggleFollowAuthor(event, authorId) {
        event.preventDefault();
        const baseUrl = "{{ route('authors.follow', ':id') }}";
        const url = baseUrl.replace(':id', authorId);
        
        const btn = document.getElementById(`follow-author-btn-${authorId}`);
        const textSpan = document.getElementById(`follow-author-text-${authorId}`);
        const countSpan = document.getElementById(`author-followers-count-${authorId}`);
        
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
                btn.className = "inline-flex items-center justify-center gap-2 px-5 py-2.5 text-xs font-bold rounded-xl transition duration-200 border shadow-md bg-rose-950/80 text-rose-400 border-rose-800/60 hover:bg-rose-900/60";
                textSpan.textContent = "{{ __('Following') }}";
                showToast('success', "Successfully followed the author!");
            } else {
                btn.className = "inline-flex items-center justify-center gap-2 px-5 py-2.5 text-xs font-bold rounded-xl transition duration-200 border shadow-md bg-red-600 text-white border-red-500 hover:bg-red-700 shadow-red-600/20";
                textSpan.textContent = "{{ __('Follow Author') }}";
                showToast('success', "Successfully unfollowed the author!");
            }

            if (countSpan && data.followers_count !== undefined) {
                countSpan.textContent = data.followers_count;
            }
        } catch (error) {
            console.error('Error toggling author follow status:', error);
        }
    }
</script>
@endpush