{{--
<x-app-layout>
    <div class="py-8 bg-[#0a0f1f] min-h-screen text-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            @include('profile.partials.profile-menu')

            <div class="bg-slate-900/90 backdrop-blur-md shadow-xl rounded-2xl border border-slate-800 overflow-hidden">
                <div class="p-6 sm:p-8 border-b border-slate-800">
                    <h3 class="text-lg font-bold text-white">
                        {{ __('My Saved Articles') }}
                    </h3>
                    <p class="mt-1 text-xs text-slate-400">
                        {{ __('A list of articles you have bookmarked for later reading.') }}
                    </p>
                </div>

                <div class="p-6 sm:p-8" id="saved-articles-grid-wrapper">
                    @php
                        $savedArticles = isset($savedArticles) ? $savedArticles : collect();
                        $userLocale = auth()->user()->locale ?? app()->getLocale();
                    @endphp

                    @forelse ($savedArticles as $article)
                        @if($loop->first)
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5" id="articles-grid">
                        @endif

                        @php
                            // جلب الترجمة المطابقة للغة المستخدم أو أول ترجمة متاحة
                            $translation = $article->translations->where('locale', $userLocale)->first() 
                                        ?? $article->translations->first();

                            // جلب صورة الغلاف البارزة للمقال عبر Spatie MediaLibrary
                            $featuredImg = $article->hasMedia('featured_imgs') 
                                ? $article->getFirstMedia('featured_imgs') 
                                : null;

                            // جلب الفئة وترجمتها حسب لغة المستخدم
                            $category = $article->category;
                            $catTrans = $category ? ($category->translations->where('locale', $userLocale)->first() ?? $category->translations->first()) : null;
                            $categoryName = $catTrans ? $catTrans->name : ($category->label ?? null);

                            // جلب اسم الكاتب ومعرفه
                            $authorName = $translation?->user?->name ?? $article->user?->name ?? __('Unknown Author');
                            $authorId = $translation?->user_id ?? $article->user_id;
                        @endphp




                        
                        @if($translation)
                            <div id="article-card-{{ $article->id }}" class="bg-slate-900/50 border border-slate-800 rounded-2xl overflow-hidden shadow-sm hover:border-slate-700 transition-all duration-300 flex flex-col justify-between group">
                                
                                <div>
                                    <!-- 🖼️ حاوية الصورة البارزة وشارة الفئة -->
                                    <div class="relative h-48 bg-slate-950 overflow-hidden">
                                        <a href="{{ route('articles.show', ['slug' => $translation->slug, 'id' => $translation->id]) }}" class="block w-full h-full">
                                            @if($featuredImg)
                                                <img src="{{ $featuredImg->getUrl('medium') }}" 
                                                     srcset="{{ $featuredImg->srcset }}" 
                                                     sizes="(max-width: 640px) 100vw, 300px" 
                                                     alt="{{ $translation->title }}" 
                                                     class="w-full h-full object-cover group-hover:scale-105 transition duration-500" 
                                                     loading="lazy">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-slate-600 text-xs">
                                                    No Image
                                                </div>
                                            @endif
                                        </a>

                                        <!-- 🏷️ شارة الفئة في أعلى اليسار كـ رابط -->
                                        @if($catTrans && $categoryName)
                                            <a href="{{ route('categories.show', ['id' => $catTrans->id, 'slug' => $catTrans->slug ?? '']) }}" 
                                            class="absolute top-3 left-3 z-10 bg-slate-950/80 backdrop-blur-md text-red-400 hover:text-white hover:bg-red-600 transition duration-200 text-[11px] font-semibold px-2.5 py-1 rounded-lg border border-slate-800">
                                                {{ $categoryName }}
                                            </a>
                                        @elseif($categoryName)
                                            <span class="absolute top-3 left-3 z-10 bg-slate-950/80 backdrop-blur-md text-red-400 text-[11px] font-semibold px-2.5 py-1 rounded-lg border border-slate-800 pointer-events-none">
                                                {{ $categoryName }}
                                            </span>
                                        @endif
                                    </div>

                                    <!-- تفاصيل المحتوى -->
                                    <div class="p-5">
                                        <div class="flex items-center justify-between text-slate-400 text-xs mb-2 font-mono">
                                            <span>
                                                ✍️ 
                                                @if($authorId)
                                                    <a href="{{ route('authors.show', $authorId) }}" class="hover:text-red-400 transition-colors underline decoration-slate-700 hover:decoration-red-400">
                                                        {{ $authorName }}
                                                    </a>
                                                @else
                                                    {{ $authorName }}
                                                @endif
                                            </span>
                                            <span>📖 Article</span>
                                        </div>

                                        <h3 class="text-base font-bold text-white group-hover:text-red-400 transition line-clamp-2 mb-2 leading-snug">
                                            <a href="{{ route('articles.show', ['slug' => $translation->slug, 'id' => $translation->id]) }}">
                                                {{ $translation->title }}
                                            </a>
                                        </h3>

                                        <p class="text-slate-400 text-xs line-clamp-2 leading-relaxed">
                                            {!! strip_tags($translation->description) !!}
                                        </p>
                                    </div>
                                </div>

                                <!-- شريط الإجراءات السفلي (تاريخ الترجمة وحذف المقال) -->
                                <div class="px-5 pb-5 pt-3 border-t border-slate-800/80 flex items-center justify-between text-xs">
                                    <span class="text-slate-500 font-mono">
                                        {{ $translation->created_at->format('M j, Y') }}
                                    </span>

                                    <button 
                                        onclick="removeArticleFromProfile(event, {{ $article->id }})"
                                        type="button"
                                        class="text-xs text-rose-400 hover:text-rose-300 bg-rose-950/40 hover:bg-rose-900/40 border border-rose-900/50 font-semibold px-3 py-1.5 rounded-lg transition-colors duration-150">
                                        {{ __('Remove') }}
                                    </button>
                                </div>
                            </div>
                        @endif

                        @if($loop->last)
                            </div>
                        @endif
                    @empty
                        <div class="text-center py-12 bg-slate-900/40 rounded-xl border border-dashed border-slate-800">
                            <span class="text-3xl mb-2 block">📚</span>
                            <p class="text-sm font-medium text-slate-400">{{ __('Your reading list is empty.') }}</p>
                        </div>
                    @endforelse

                </div>

            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        async function removeArticleFromProfile(event, articleId) {
            event.preventDefault();
            
            const baseUrl = "{{ route('articles.saved', ':id') }}";
            const url = baseUrl.replace(':id', articleId);
            const card = document.getElementById(`article-card-${articleId}`);
            
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
                
                if (!data.is_saved) {
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.95)';
                    card.style.transition = 'all 0.3s ease';
                    
                    setTimeout(() => {
                        card.remove();
                        
                        const grid = document.getElementById('articles-grid');
                        if (grid && grid.children.length === 0) {
                            document.getElementById('saved-articles-grid-wrapper').innerHTML = `
                                <div class="text-center py-12 bg-slate-900/40 rounded-xl border border-dashed border-slate-800">
                                    <span class="text-3xl mb-2 block">📚</span>
                                    <p class="text-sm font-medium text-slate-400">{{ __('Your reading list is empty.') }}</p>
                                </div>
                            `;
                        }
                    }, 300);
                }

                if(typeof window.showToast == 'function') {
                    showToast('success', "{{ __('Successfully removed from saved!') }}");
                }
            } catch (error) {
                if(typeof window.showToast == 'function') {
                    showToast('error', "{{ __('Error in removing from saved!') }}");
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
--}}

<x-app-layout>
    <div class="py-8 bg-[#0a0f1f] min-h-screen text-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            @include('profile.partials.profile-menu')

            <div class="bg-slate-900/90 backdrop-blur-md shadow-xl rounded-2xl border border-slate-800 overflow-hidden">
                <div class="p-6 sm:p-8 border-b border-slate-800">
                    <h3 class="text-lg font-bold text-white">
                        {{ __('My Saved Articles') }}
                    </h3>
                    <p class="mt-1 text-xs text-slate-400">
                        {{ __('A list of articles you have bookmarked for later reading.') }}
                    </p>
                </div>

                <div class="p-6 sm:p-8" id="saved-articles-grid-wrapper">
                    @php
                        $savedArticles = isset($savedArticles) ? $savedArticles : collect();
                        $userLocale = auth()->user()->locale ?? app()->getLocale();
                    @endphp

                    @forelse ($savedArticles as $article)
                        @if($loop->first)
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6" id="articles-grid">
                        @endif

                        @php
                            // جلب الترجمة المطابقة للغة المستخدم أو أول ترجمة متاحة
                            $translation = $article->translations->where('locale', $userLocale)->first() 
                                        ?? $article->translations->first();

                            // جلب صورة الغلاف البارزة للمقال عبر Spatie MediaLibrary
                            $featuredImg = $article->hasMedia('featured_imgs') 
                                ? $article->getFirstMedia('featured_imgs') 
                                : null;

                            // جلب الفئة وترجمتها حسب لغة المستخدم
                            $category = $article->category;
                            $catTrans = $category ? ($category->translations->where('locale', $userLocale)->first() ?? $category->translations->first()) : null;
                            $categoryName = $catTrans ? $catTrans->name : ($category->label ?? null);

                            // جلب اسم الكاتب ومعرفه
                            $authorName = $translation?->user?->name ?? $article->user?->name ?? __('Unknown Author');
                            $authorId = $translation?->user_id ?? $article->user_id;

                            // جلب رابط الصورة بنفس نمط صفحة الفئة
                            $imageUrl = $featuredImg 
                                ? $featuredImg->getUrl('medium') 
                                : asset('images/default-article.jpg');
                        @endphp

                        @if($translation)
                            <div id="article-card-{{ $article->id }}" 
                                 class="group relative bg-[#0f1526] border border-slate-800 hover:border-red-600/60 rounded-xl overflow-hidden shadow-lg transition-all duration-300 flex flex-col justify-between">
                                
                                <div>
                                    <!-- صورة المقالة بنفس تصميم صورة الفئة -->
                                    <div class="relative h-40 w-full overflow-hidden bg-slate-950">
                                        <a href="{{ route('articles.show', ['slug' => $translation->slug, 'id' => $translation->id]) }}" class="block w-full h-full">
                                            <img src="{{ $imageUrl }}" 
                                                 alt="{{ $translation->title }}" 
                                                 class="w-full h-full object-cover opacity-80 group-hover:opacity-100 group-hover:scale-105 transition-all duration-500 ease-in-out"
                                                 loading="lazy">
                                        </a>
                                        <div class="absolute inset-0 bg-gradient-to-t from-[#0f1526] via-transparent to-transparent pointer-events-none"></div>
                                        
                                        <!-- 🏷️ شارة الفئة في أعلى اليسار -->
                                        @if($catTrans && $categoryName)
                                            <a href="{{ route('categories.show', ['id' => $catTrans->id, 'slug' => $catTrans->slug ?? '']) }}" 
                                               class="absolute top-3 left-3 z-10 bg-slate-950/80 backdrop-blur-md text-red-400 hover:text-white hover:bg-red-600 transition duration-200 text-[11px] font-semibold px-2.5 py-1 rounded-lg border border-slate-800">
                                                {{ $categoryName }}
                                            </a>
                                        @elseif($categoryName)
                                            <span class="absolute top-3 left-3 z-10 bg-slate-950/80 backdrop-blur-md text-red-400 text-[11px] font-semibold px-2.5 py-1 rounded-lg border border-slate-800 pointer-events-none">
                                                {{ $categoryName }}
                                            </span>
                                        @endif

                                        <!-- 📅 شارة تاريخ النشر في أعلى اليمين -->
                                        <span class="absolute top-3 right-3 bg-slate-950/80 backdrop-blur-md border border-slate-800 text-gray-300 text-[10px] font-mono px-2.5 py-1 rounded-lg">
                                            {{ $translation->created_at->format('M d, Y') }}
                                        </span>
                                    </div>

                                    <!-- محتوى البطاقة -->
                                    <div class="p-4">
                                        <h2 class="text-base font-bold text-gray-200 group-hover:text-red-500 transition-colors line-clamp-2">
                                            <a href="{{ route('articles.show', ['slug' => $translation->slug, 'id' => $translation->id]) }}">
                                                {{ $translation->title }}
                                            </a>
                                        </h2>
                                        <p class="text-xs text-gray-400 mt-1.5 line-clamp-2 leading-relaxed">
                                            {!! strip_tags($translation->description) !!}
                                        </p>
                                    </div>
                                </div>

                                <!-- شريط الكاتب وزر القراءة في الأسفل -->
                                <div class="p-4 pt-3 border-t border-slate-850 flex items-center justify-between text-xs text-slate-400">
                                    <span class="font-mono truncate max-w-[120px]">
                                        ✍️ 
                                        @if($authorId)
                                            <a href="{{ route('authors.show', $authorId) }}" class="hover:text-red-400 transition-colors underline decoration-slate-700 hover:decoration-red-400">
                                                {{ $authorName }}
                                            </a>
                                        @else
                                            {{ $authorName }}
                                        @endif
                                    </span>

                                    <a href="{{ route('articles.show', ['slug' => $translation->slug, 'id' => $translation->id]) }}" class="text-red-500 font-semibold flex items-center gap-1 shrink-0">
                                        <span>{{ __('Read More') }}</span>
                                        <svg class="w-4 h-4 transform group-hover:translate-x-1 rtl:group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </div>

                                <!-- 🗑️ شريط الحذف وتاريخ الترجمة (داخل البطاقة) -->
                                <div class="px-4 pb-4 flex items-center justify-between text-xs border-t border-slate-800/80 pt-3">
                                    <span class="text-slate-500 font-mono">
                                        {{ $translation->created_at->format('M j, Y') }}
                                    </span>

                                    <button 
                                        onclick="removeArticleFromProfile(event, {{ $article->id }})"
                                        type="button"
                                        class="text-xs text-rose-400 hover:text-rose-300 bg-rose-950/40 hover:bg-rose-900/40 border border-rose-900/50 font-semibold px-3 py-1.5 rounded-lg transition-colors duration-150">
                                        {{ __('Remove') }}
                                    </button>
                                </div>
                            </div>
                        @endif

                        @if($loop->last)
                            </div>
                        @endif
                    @empty
                        <div class="text-center py-12 bg-slate-900/40 rounded-xl border border-dashed border-slate-800">
                            <span class="text-3xl mb-2 block">📚</span>
                            <p class="text-sm font-medium text-slate-400">{{ __('Your reading list is empty.') }}</p>
                        </div>
                    @endforelse

                </div>

            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        async function removeArticleFromProfile(event, articleId) {
            event.preventDefault();
            
            const baseUrl = "{{ route('articles.saved', ':id') }}";
            const url = baseUrl.replace(':id', articleId);
            const card = document.getElementById(`article-card-${articleId}`);
            
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
                
                if (!data.is_saved) {
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.95)';
                    card.style.transition = 'all 0.3s ease';
                    
                    setTimeout(() => {
                        card.remove();
                        
                        const grid = document.getElementById('articles-grid');
                        if (grid && grid.children.length === 0) {
                            document.getElementById('saved-articles-grid-wrapper').innerHTML = `
                                <div class="text-center py-12 bg-slate-900/40 rounded-xl border border-dashed border-slate-800">
                                    <span class="text-3xl mb-2 block">📚</span>
                                    <p class="text-sm font-medium text-slate-400">{{ __('Your reading list is empty.') }}</p>
                                </div>
                            `;
                        }
                    }, 300);
                }

                if(typeof window.showToast == 'function') {
                    showToast('success', "{{ __('Successfully removed from saved!') }}");
                }
            } catch (error) {
                if(typeof window.showToast == 'function') {
                    showToast('error', "{{ __('Error in removing from saved!') }}");
                }
            }
        }
    </script>
    @endpush
</x-app-layout>