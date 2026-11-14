<x-app-layout>
    <div class="py-8 bg-[#0a0f1f] min-h-screen text-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @include('profile.partials.profile-menu')

            <div class="bg-slate-900/90 backdrop-blur-md shadow-xl rounded-2xl border border-slate-800 overflow-hidden">
                
                <div class="p-6 sm:p-8 border-b border-slate-800">
                    <h3 class="text-lg font-bold text-white">
                        {{ __('Followed Categories') }}
                    </h3>
                    <p class="mt-1 text-xs text-slate-400">
                        {{ __('Manage the categories you follow. You will receive database notifications when new articles are published.') }}
                    </p>
                </div>

                <div class="p-6 sm:p-8" id="favorite-categories-grid-wrapper">
                    @php
                        $categories = isset($favoriteCategories) ? $favoriteCategories : collect();
                        $userLocale = auth()->user()->locale ?? app()->getLocale();
                    @endphp

                    @forelse ($categories as $category)
                        @if($loop->first)
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6" id="categories-grid">
                        @endif

                        @php
                            // 🔍 جلب الترجمة المطابقة للغة المستخدم حصرياً، أو أخذ أول ترجمة متاحة كبديل
                            $category_translation = $category->translations->where('locale', $userLocale)->first() 
                                                 ?? $category->translations->first();
                            
                            $categoryName = $category_translation ? $category_translation->name : $category->label;
                            $categoryDesc = $category_translation?->description ?? null;
                            
                            // 🖼️ جلب صورة الفئة باستخدام Spatie MediaLibrary
                            $categoryImage = $category->hasMedia('featured_imgs') 
                                ? $category->getFirstMediaUrl('featured_imgs', 'medium') 
                                : asset('images/default-article.jpg');

                            // 📊 حساب عدد المقالات التابعة لهذه الفئة بنفس لغة المستخدم
                            $articlesCount = \App\Models\Article::where('article_category_id', $category->id)
                                ->whereHas('translations', function($q) use ($userLocale) {
                                    $q->where('locale', $userLocale);
                                })->count();
                        @endphp
                        
                        <!-- بطاقة الفئة بتصميم موحد مع صفحة الفئة -->
                        <div id="category-card-{{ $category->id }}" 
                             class="group relative bg-[#0f1526] border border-slate-800 hover:border-red-600/60 rounded-xl overflow-hidden shadow-lg transition-all duration-300 flex flex-col justify-between">
                            
                            <div>
                                <!-- صورة الفئة بنفس تصميم صورة بطاقة المقال -->
                                <div class="relative h-40 w-full overflow-hidden bg-slate-950">
                                    <a href="{{ $category_translation ? route('article-categories.show', ['id' => $category_translation->id, 'slug' => $category_translation->slug ?? '']) : '#' }}" class="block w-full h-full">
                                        <img src="{{ $categoryImage }}" 
                                             alt="{{ $categoryName }}" 
                                             class="w-full h-full object-cover opacity-80 group-hover:opacity-100 group-hover:scale-105 transition-all duration-500 ease-in-out">
                                    </a>
                                    <div class="absolute inset-0 bg-gradient-to-t from-[#0f1526] via-transparent to-transparent pointer-events-none"></div>

                                    <!-- شارة عدد المقالات في أعلى اليسار -->
                                    <span class="absolute top-3 left-3 bg-slate-950/80 backdrop-blur-md text-red-400 border border-slate-800 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider font-mono">
                                        {{ $articlesCount }} {{ $articlesCount == 1 ? __('Article') : __('Articles') }}
                                    </span>
                                </div>

                                <!-- اسم الفئة والوصف -->
                                <div class="p-4">
                                    <h2 class="text-base font-bold text-gray-200 group-hover:text-red-500 transition-colors line-clamp-2">
                                        <a href="{{ $category_translation ? route('article-categories.show', ['id' => $category_translation->id, 'slug' => $category_translation->slug ?? '']) : '#' }}">
                                            {{ $categoryName }}
                                        </a>
                                    </h2>

                                    @if($categoryDesc)
                                        <p class="text-xs text-gray-400 mt-1.5 line-clamp-2 leading-relaxed">
                                            {{ Str::limit(strip_tags($categoryDesc), 80) }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <!-- شريط زر إلغاء المتابعة في الأسفل (داخل البطاقة) -->
                            <div class="p-4 pt-3 border-t border-slate-850 flex items-center justify-end text-xs">
                                <button 
                                    onclick="removeCategoryFromProfile(event, {{ $category->id }})"
                                    type="button" 
                                    class="text-xs text-rose-400 hover:text-rose-300 bg-rose-950/40 hover:bg-rose-900/40 border border-rose-900/50 font-semibold px-3 py-1.5 rounded-lg transition-colors duration-150">
                                    {{ __('Unfollow') }}
                                </button>
                            </div>
                        </div>

                        @if($loop->last)
                            </div>
                        @endif
                    @empty
                        <div class="text-center py-12 bg-slate-900/40 rounded-xl border border-dashed border-slate-800" id="empty-categories-message">
                            <span class="text-3xl mb-2 block">📂</span>
                            <p class="text-sm font-medium text-slate-400">{{ __('You are not following any categories yet.') }}</p>
                        </div>
                    @endforelse

                </div>

            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        async function removeCategoryFromProfile(event, categoryId) {
            event.preventDefault();
            
            const baseUrl = "{{ route('article-categories.favorite', ':id') }}";
            const url = baseUrl.replace(':id', categoryId);
            const card = document.getElementById(`category-card-${categoryId}`);
            
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
                
                if (!data.is_favorite) {
                    if (card) {
                        card.style.opacity = '0';
                        card.style.transform = 'scale(0.95)';
                        card.style.transition = 'all 0.3s ease';
                    }
                    
                    setTimeout(() => {
                        if (card) card.remove();
                        
                        const grid = document.getElementById('categories-grid');
                        if (grid && grid.children.length === 0) {
                            grid.remove();
                            document.getElementById('favorite-categories-grid-wrapper').innerHTML = `
                                <div class="text-center py-12 bg-slate-900/40 rounded-xl border border-dashed border-slate-800" id="empty-categories-message">
                                    <span class="text-3xl mb-2 block">📂</span>
                                    <p class="text-sm font-medium text-slate-400">{{ __('You are not following any categories yet.') }}</p>
                                </div>
                            `;
                        }
                    }, 300);
                }

                if(typeof window.showToast == 'function') {
                    showToast('success', "{{ __('Successfully removed from favorites!') }}");
                }
            } catch (error) {
                console.error('Error removing category from profile:', error);
                if(typeof window.showToast == 'function') {
                    showToast('error', "{{ __('Error in removing from favorites!') }}");
                }
            }
        }
    </script>
    @endpush
</x-app-layout>