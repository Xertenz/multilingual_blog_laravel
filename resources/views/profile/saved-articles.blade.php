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
                        $savedArticles = isset($savedArticles) ?$savedArticles : collect();
                    @endphp

                    @forelse ($savedArticles as $article)
                        @if($loop->first)
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5" id="articles-grid">
                        @endif

                        @php
                            $translation =$article->translations->first();
                        @endphp
                        
                        @if($translation)
                            <div id="article-card-{{ $article->id }}" class="flex flex-col justify-between p-5 bg-slate-900/50 border border-slate-800 rounded-xl shadow-sm hover:border-slate-700 transition-all duration-200 group">
                                <div>
                                    <a href="{{ route('articles.show', ['id' => $translation->id, 'slug' =>$translation->slug]) }}" 
                                       class="text-sm font-bold text-slate-200 group-hover:text-red-500 transition-colors line-clamp-2 leading-snug">
                                        {{ $translation->title }}
                                    </a>
                                    <p class="text-xs text-slate-400 mt-2.5 line-clamp-2 leading-relaxed">
                                        {!! strip_tags($translation->description) !!}
                                    </p>
                                </div>

                                <div class="flex items-center justify-between border-t border-slate-800/80 pt-4 mt-4">
                                    <span class="text-[11px] text-slate-500 font-mono">
                                        {{ $article->created_at->format('Y-m-d') }}
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