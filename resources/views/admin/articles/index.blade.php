@extends("layouts.admin.master")
{{-- @section('page-title', 'All Articles') --}}
@section('title', 'Articles')

@push('styles')
    @vite(['resources/css/components/article-gallery.css'])
@endpush

@section('content')
    {{-- Header Actions --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Articles Management</h2>
            <p class="text-sm text-slate-500">Manage, edit, translate, and organize your True Crime stories</p>
        </div>
        <a href="{{ route('admin.articles.create') }}" 
           class="bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm px-4 py-2 rounded-lg shadow-sm hover:shadow transition-all flex items-center gap-2">
            <span>+ Create Article</span>
        </a>
    </div>

    {{-- Table Container --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        <th class="py-3 px-4">Category</th>
                        <th class="py-3 px-4">Featured Img</th>
                        <th class="py-3 px-4">Internal Title</th>
                        <th class="py-3 px-4">Writer</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Views</th>
                        <th class="py-3 px-4">Translations</th>
                        <th class="py-3 px-4">Gallery</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @foreach ($articles as $article)
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        {{-- Category --}}
                        <td class="py-3.5 px-4 font-medium text-slate-700 whitespace-nowrap">
                            <span class="bg-slate-100 text-slate-700 px-2.5 py-1 rounded-md text-xs font-semibold border border-slate-200">
                                {{ $article->category->label ?? '[Null]' }}
                            </span>
                        </td>

                        {{-- Featured Image --}}
                        <td class="py-3.5 px-4">
                            <div class="w-14 h-10 rounded-lg overflow-hidden bg-slate-100 border border-slate-200 shrink-0">
                                @if($article->getFirstMediaUrl('featured_imgs'))
                                    <img 
                                        src="{{ $article->getFirstMediaUrl('featured_imgs') }}"
                                        srcset="{{ $article->getFirstMedia('featured_imgs') !== null ? $article->getFirstMedia('featured_imgs')->getSrcset() : '' }}"
                                        class="w-full h-full object-cover"
                                        alt="Featured image"
                                    />
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-slate-400 text-[10px]">No Image</div>
                                @endif
                            </div>
                        </td>

                        {{-- Internal Title --}}
                        <td class="py-3.5 px-4 text-slate-600 font-mono text-xs">
                            {{ $article->internal_title ?? "no internal title" }}
                        </td>

                        {{-- Writer --}}
                        <td class="py-3.5 px-4 text-slate-600 font-mono text-xs">
                            {{ $article->user->name ?? "no name" }}
                        </td>

                        {{-- Status Badge --}}
                        <td class="py-3.5 px-4">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium border
                                {{ $article->status === 'published' 
                                    ? 'bg-emerald-50 text-emerald-700 border-emerald-200' 
                                    : 'bg-amber-50 text-amber-700 border-amber-200' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $article->status === 'published' ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                                {{ ucfirst($article->status) }}
                            </span>
                        </td>

                        {{-- Views --}}
                        <td class="py-3.5 px-4 text-slate-600 font-medium">
                            {{ number_format($article->views) }}
                        </td>

                        {{-- Translations --}}
                        <td class="py-3.5 px-4">
                            <div class="flex items-center gap-2 flex-wrap">
                                @foreach ($article->translations as $article_translation)
                                    <div class="inline-flex items-center bg-slate-50 border border-slate-200 rounded-lg p-0.5 shadow-2xs">
                                        {{-- Edit Translation --}}
                                        @if (Auth::user()->hasRole(['super-admin', 'editor']) || $article->user_id === Auth::id())
                                            <a href="{{ route('admin.articles-trans.edit', $article_translation->id) }}" 
                                               class="px-2 py-0.5 text-xs font-semibold text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded transition-colors"
                                               title="Edit Translation">
                                                {{ strtoupper($article_translation->locale) }}
                                            </a>
                                        @else
                                            <span class="px-2 py-0.5 text-xs font-semibold text-slate-400">
                                                {{ strtoupper($article_translation->locale) }}
                                            </span>
                                        @endif

                                        {{-- Delete Translation --}}
                                        @hasanyrole('super-admin|editor')
                                            <form action="{{ route('admin.articles-trans.destroy', $article_translation->id) }}" 
                                                  method="POST" 
                                                  class="inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this translation?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-slate-400 hover:text-red-600 px-1 hover:bg-red-50 rounded transition-colors text-xs font-bold" title="Delete Translation">
                                                    &times;
                                                </button>
                                            </form>
                                        @endhasanyrole
                                    </div>
                                @endforeach

                                {{-- Add Translation Button --}}
                                @if (Auth::user()->hasRole(['super-admin', 'editor']) || $article->user_id === Auth::id())
                                    <a href="{{ route('admin.articles-trans.create', $article->id) }}"
                                       class="inline-flex items-center gap-1 bg-amber-50 border border-amber-200 text-amber-700 hover:bg-amber-100 px-2 py-1 rounded-lg text-xs font-medium transition-colors">
                                        <span>+ Add Trans</span>
                                    </a>
                                @endif
                            </div>
                        </td>

                        {{-- Gallery --}}
                        <td class="py-3.5 px-4">
                            <button class="show-gallery inline-flex items-center gap-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 font-medium px-2.5 py-1 rounded-lg text-xs transition-colors cursor-pointer" 
                                    data-article-id="{{ $article->id }}">
                                <span>📷</span>
                                <span class="artilce-content_imgs-count font-semibold">{{ $article->getMedia('content_imgs')->count() }}</span>
                            </button>
                        </td>

                        {{-- Actions Column --}}
                        <td class="py-3.5 px-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                @if (Auth::user()->hasRole(['super-admin', 'editor']) || $article->user_id === Auth::id())
                                    <a href="{{ route('admin.articles.edit', $article->id) }}" 
                                       class="bg-blue-50 text-blue-600 hover:bg-blue-100 border border-blue-200 px-2.5 py-1 rounded-lg text-xs font-medium transition-colors">
                                        Edit
                                    </a>
                                @else
                                    <span class="text-slate-400 italic text-xs">Read-only</span>
                                @endif

                                @hasanyrole('super-admin|editor')
                                    <form action="{{ route('admin.articles.destroy', $article->id) }}" 
                                          method="POST" 
                                          class="inline"
                                          onsubmit="return confirm('Are you sure you want to delete this main article with all its translated articles?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-50 text-red-600 hover:bg-red-100 border border-red-200 px-2.5 py-1 rounded-lg text-xs font-medium transition-colors cursor-pointer">
                                            Delete
                                        </button>
                                    </form>
                                @endhasanyrole
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $articles->links() }}
    </div>
@endsection

@push('scripts')
    <script>
        window.upload_url = "{{ route('admin.articles.upload.content') }}";
        window.article_content_imgs_url = "{{ route('admin.articles.get.content') }}";
        window.delete_content_img_url = "{{ route('admin.articles.delete.content_img') }}";
    </script>
    @vite(['resources/js/components/article-gallery.js'])

    <script>
        window.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                showToast('success', @json(session('success')));
            @elseif(session('error'))
                showToast('error', @json(session('error')));
            @endif
        });
    </script>
@endpush