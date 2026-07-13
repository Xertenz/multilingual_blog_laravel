<x-app-layout>
    {{--
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('My Content Preferences') }}
        </h2>
    </x-slot>
    --}}

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @include('profile.partials.profile-menu')

            <div class="p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                
                <header class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        {{ __('Followed Categories') }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-650 dark:text-gray-400">
                        {{ __('Manage the categories you follow. You will receive database notifications when new articles are published.') }}
                    </p>
                </header>

                @if($favoriteCategories->isEmpty())
                    <p class="text-sm text-gray-500 italic p-4 bg-gray-55 dark:bg-gray-900 rounded-xl border border-dashed border-gray-300 dark:border-gray-700">
                        {{ __('You are not following any categories yet.') }}
                    </p>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($favoriteCategories as $category)
                            @php
                                $translation = $category->translations->first();
                                $categoryName = $translation ? $translation->name : $category->sign;
                            @endphp
                            
                            <div id="category-card-{{ $category->id }}" class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-750 rounded-xl shadow-sm transition-all duration-300">
                                <a href="#" class="text-sm font-semibold text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                                    {{ $categoryName }}
                                </a>

                                <form action="{{ route('categories.favorite', $category->id) }}" method="POST">
                                    @csrf
                                    <button 
                                        onclick="removeCategoryFromProfile(event, {{$category->id}})"
                                        type="submit" class="text-xs text-red-650 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 font-medium bg-red-50/50 hover:bg-red-50 px-2.5 py-1.5 rounded-lg transition duration-150">
                                        {{ __('Unfollow') }}
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif

            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        async function removeCategoryFromProfile(event, categoryId) {
            event.preventDefault();
            
            // جلب مسار مفضلة الفئات المستهدف
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
                
                // بما أن السيرفر يُرجع 'is_favorite'، نتحقق أنه تم إلغاء التفضيل بنجاح (false)
                if (!data.is_favorite) {
                    // تطبيق تأثير التلاشي البصري الذكي

                    if (card) {
                        card.style.opacity = '0';
                        card.style.transform = 'scale(0.95)';
                    }
                    
                    setTimeout(() => {
                        if (card) card.remove();
                        
                        // فحص ما إذا كانت شجرة العناصر فرغت بالكامل لعرض رسالة فارغة
                        const grid = document.getElementById('categories-grid');
                        if (grid && grid.children.length === 0) {
                            document.getElementById('favorite-categories-grid-wrapper').innerHTML = `
                                <p class="text-sm text-gray-500 italic p-4 bg-gray-55 rounded-xl border border-dashed border-gray-300">
                                    {{ __('Your favorite categories list is empty.') }}
                                </p>
                            `;
                        }
                    }, 300);
                }
                if(typeof window.showToast == 'function') {
                    showToast(    'success', "{{__('Successfully removed from favorites!')}}")    ;
                }
            } catch (error) {
                console.error('Error removing category from profile:', error);
                if(typeof window.showToast == 'function') {
                    showToast('error', "{{__('Error in removing from favorites!')}}");
                }
            }
        }
    </script>
    @endpush
</x-app-layout>