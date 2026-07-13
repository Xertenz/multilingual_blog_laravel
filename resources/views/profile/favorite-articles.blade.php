<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @include('profile.partials.profile-menu')

            <div class="p-6 bg-white shadow sm:rounded-lg">
                <header class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900">
                        {{ __('My Saved Articles') }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-600">
                        {{ __('A list of articles you have bookmarked for later reading.') }}
                    </p>
                </header>

                @if($favoriteArticles->isEmpty())
                    <p class="text-sm text-gray-500 italic p-4 bg-gray-55 rounded-xl border border-dashed border-gray-300">
                        {{ __('Your reading list is empty.') }}
                    </p>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($favoriteArticles as $article)
                            @php
                                // جلب الترجمة باللغة الحالية التي قمنا بشحنها مسبقاً
                                $translation = $article->translations->first();
                            @endphp
                            
                            @if($translation)
                                <div id="article-card-{{ $article->id }}" class="flex flex-col justify-between p-4 bg-gray-50 border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-all duration-200">
                                    <div class="mb-4">
                                        <a href="{{ route('articles.show', ['id' => $translation->id, 'slug' => $translation->slug]) }}" 
                                           class="text-sm font-bold text-gray-900 hover:text-indigo-600 transition-colors line-clamp-2">
                                            {{ $translation->title }}
                                        </a>
                                        <p class="text-xs text-gray-500 mt-2 line-clamp-2">
                                            {!! strip_tags($translation->description) !!}
                                        </p>
                                    </div>

                                    <div class="flex items-center justify-between border-t border-gray-200/60 pt-3 mt-auto">
                                        <span class="text-[11px] text-gray-400 font-medium">
                                            {{ $article->created_at->format('Y-m-d') }}
                                        </span>

                                        <button 
                                            onclick="removeArticleFromProfile(event, {{$article->id}})"
                                            type="submit"
                                            class="text-xs text-red-600 hover:text-red-800 font-semibold px-2.5 py-1 rounded-lg hover:bg-red-50 transition duration-150">
                                            {{ __('Remove') }}
                                        </button>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif

            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        //window.addEventListener('DOMContentLoaded', function() {

            async function removeArticleFromProfile(event, articleId) {
                event.preventDefault();
                
                // نفس حيلة توليد المسار والاستبدال التي اعتمدناها سابقاً
                const baseUrl = "{{ route('articles.favorite', ':id') }}";
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
                    
                    // بما أن السيرفر قام بعمل toggle، نتحقق أنه تمت إزالتها بالفعل (is_saved == false)
                    if (!data.is_saved) {
                        // إضافة تأثير بصري للتلاشي قبل الحذف النهائي من الـ DOM
                        card.style.opacity = '0';
                        card.style.transform = 'scale(0.95)';
                        
                        setTimeout(() => {
                            card.remove();
                            
                            // إذا أصبحت القائمة فارغة تماماً، نعرض رسالة "قائمتك فارغة" دون الحاجة لعمل ريفريش
                            const grid = document.getElementById('articles-grid');
                            if (grid && grid.children.length === 0) {
                                document.getElementById('favorite-articles-grid-wrapper').innerHTML = `
                                    <p class="text-sm text-gray-500 italic p-4 bg-gray-55 rounded-xl border border-dashed border-gray-300">
                                        {{ __('Your reading list is empty.') }}
                                    </p>
                                `;
                            }
                        }, 300); // 300ms متطابقة مع متطلبات التلاشي duration-300
                    }

                    if(typeof window.showToast == 'function') {
                        showToast(    'success', "{{__('Successfully removed from favorites!')}}")    ;
                    }
                } catch (error) {
                    console.error('Error removing article from profile:', error);
                    if(typeof window.showToast == 'function') {
                        showToast('error', "{{__('Error in removing from favorites!')}}");
                    }
                }
            }

        //})

    </script>

    @endpush
</x-app-layout>