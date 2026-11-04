<x-app-layout>
    <div class="py-8 bg-[#0a0f1f] min-h-screen text-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            @include('profile.partials.profile-menu')

            <div class="bg-slate-900/90 backdrop-blur-md shadow-xl rounded-2xl border border-slate-800 overflow-hidden">
                <div class="p-6 sm:p-8 border-b border-slate-800">
                    <h3 class="text-lg font-bold text-white">
                        {{ __('Following Authors') }}
                    </h3>
                    <p class="mt-1 text-xs text-slate-400">
                        {{ __('Manage the authors and investigators you follow.') }}
                    </p>
                </div>

                <div class="p-6 sm:p-8" id="following-authors-grid-wrapper">
                    @php
                        $followingAuthors = isset($followingAuthors) ? $followingAuthors : collect();
                    @endphp

                    @forelse ($followingAuthors as $author)
                        @if($loop->first)
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5" id="authors-grid">
                        @endif
                        
                        <div id="author-card-{{ $author->id }}" class="flex flex-col justify-between p-5 bg-slate-900/50 border border-slate-800 rounded-xl shadow-sm hover:border-slate-700 transition-all duration-200 group">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center text-red-500 font-bold text-lg uppercase shrink-0">
                                    {{ substr($author->name, 0, 2) }}
                                </div>
                                <div class="overflow-hidden">
                                    <a href="{{ route('authors.show', $author->id) }}" class="text-sm font-bold text-slate-200 group-hover:text-red-500 transition-colors truncate block">
                                        {{ $author->name }}
                                    </a>
                                    <p class="text-xs text-slate-400 mt-0.5">
                                        {{ $author->authorFollowers()->count() }} {{ __('Followers') }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center justify-end border-t border-slate-800/80 pt-4 mt-4">
                                <button 
                                    onclick="removeAuthorFromProfile(event, {{ $author->id }})"
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
                        <div class="text-center py-12 bg-slate-900/40 rounded-xl border border-dashed border-slate-800" id="empty-authors-message">
                            <span class="text-3xl mb-2 block">👤</span>
                            <p class="text-sm font-medium text-slate-400">{{ __('You are not following any authors yet.') }}</p>
                        </div>
                    @endforelse

                </div>

            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        async function removeAuthorFromProfile(event, authorId) {
            event.preventDefault();
            
            const baseUrl = "{{ route('authors.follow', ':id') }}";
            const url = baseUrl.replace(':id', authorId);
            const card = document.getElementById(`author-card-${authorId}`);
            
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
                
                if (!data.is_following) {
                    if (card) {
                        card.style.opacity = '0';
                        card.style.transform = 'scale(0.95)';
                        card.style.transition = 'all 0.3s ease';
                    }
                    
                    setTimeout(() => {
                        if (card) card.remove();
                        
                        const grid = document.getElementById('authors-grid');
                        if (grid && grid.children.length === 0) {
                            grid.remove();
                            document.getElementById('following-authors-grid-wrapper').innerHTML = `
                                <div class="text-center py-12 bg-slate-900/40 rounded-xl border border-dashed border-slate-800" id="empty-authors-message">
                                    <span class="text-3xl mb-2 block">👤</span>
                                    <p class="text-sm font-medium text-slate-400">{{ __('You are not following any authors yet.') }}</p>
                                </div>
                            `;
                        }
                    }, 300);
                }

                if(typeof window.showToast == 'function') {
                    showToast('success', "{{ __('Successfully unfollowed the author!') }}");
                }
            } catch (error) {
                console.error('Error unfollowing author from profile:', error);
                if(typeof window.showToast == 'function') {
                    showToast('error', "{{ __('Error in unfollowing author!') }}");
                }
            }
        }
    </script>
    @endpush
</x-app-layout>