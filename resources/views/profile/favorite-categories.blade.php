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
                        $categories = isset($favoriteCategories) ?$favoriteCategories : collect();
                    @endphp

                    @forelse ($categories as $category)
                        @if($loop->first)
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5" id="categories-grid">
                        @endif

                        @php
                            $category_translation =$category->translations->first();
                            $categoryName =$category_translation ? $category_translation->name :$category->sign;
                            // جلب الصورة باستخدام Spatie MediaLibrary
                            $categoryImage = $category->getFirstMediaUrl('featured_imgs', 'small') ?:$category->getFirstMediaUrl('featured_imgs');
                        @endphp
                        
                        <!-- بطاقة الفئة بتصميم مرئي يضم الصورة عبر MediaLibrary -->
                        <div id="category-card-{{ $category->id }}" class="bg-slate-900/50 border border-slate-800 rounded-xl overflow-hidden shadow-sm hover:border-slate-700 transition-all duration-300 flex flex-col justify-between">
                            
                            <div>
                                <!-- عرض صورة الفئة المرفقة -->
                                <div class="h-36 w-full overflow-hidden bg-slate-950 relative">
                                    @if($categoryImage)
                                        <img src="{{ $categoryImage }}" alt="{{ $categoryName }}" class="w-full h-full object-cover transition-transform duration-500 hover:scale-105">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-slate-600 bg-slate-950/60 border-b border-slate-800/80">
                                            <span class="text-3xl">📂</span>
                                        </div>
                                    @endif
                                </div>

                                <!-- اسم الفئة ورابطها -->
                                <div class="p-4 pb-2">
                                    <a href="{{ $category_translation ? route('categories.show', ['id' => $category_translation->id]) : '#' }}" class="text-sm font-semibold text-white hover:text-red-400 transition-colors flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-red-500 shadow-[0_0_8px_rgba(239,68,68,0.5)]"></span>
                                        {{ $categoryName }}
                                    </a>
                                </div>
                            </div>

                            <!-- زر إلغاء المتابعة في أسفل البطاقة -->
                            <div class="p-4 pt-2 flex items-center justify-end border-t border-slate-800/50 mt-2">
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
            
            const baseUrl = "{{ route('categories.favorite', ':id') }}";
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