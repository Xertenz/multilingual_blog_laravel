@extends("layouts.admin.master")
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

        @hasanyrole('super-admin|editor')
        <a href="{{ route('admin.articles.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm px-4 py-2 rounded-lg shadow-sm hover:shadow transition-all flex items-center gap-2">
            <span>+ Create Article</span>
        </a>
        @endhasanyrole
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
                        <th class="py-3 px-4">Creator</th>
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

                        {{-- Creator --}}
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

                        {{-- Translations (كل اللغات المدعومة) --}}


{{-- Translations (كل اللغات المدعومة) --}}
<td class="py-3.5 px-4">
    @php
        $translationMap = $article->translations->keyBy('locale');
    @endphp

    {{-- سطر واحد دائماً، بدون تمرير داخلي، والجدول كاملاً هو من يمرر أفقياً --}}
    <div class="flex items-center gap-1.5 flex-nowrap">
        @foreach ($supportedLocales as $localeCode => $localeProps)
            @php
                $existingTrans = $translationMap->get($localeCode);
                $canEdit = Auth::user()->hasRole(['super-admin', 'editor'])
                        || ($existingTrans && $existingTrans->user_id === Auth::id());
                $canCreate = Auth::user()->hasAnyRole(['super-admin', 'editor', 'writer']);
            @endphp

            @if ($existingTrans)
                {{-- ✅ الترجمة موجودة --}}
                <div class="inline-flex items-center bg-slate-50 border border-slate-200 rounded-lg shadow-2xs overflow-hidden shrink-0">
                    @if ($canEdit)
                        <a href="{{ route('admin.articles-trans.edit', $existingTrans->id) }}"
                           class="inline-flex items-center gap-1 px-2 py-1 text-xs font-semibold text-blue-600 hover:text-blue-800 hover:bg-blue-50 transition-colors whitespace-nowrap"
                           title="{{ __('Edit :lang translation', ['lang' => $localeProps['name']]) }}">
                            <span class="font-mono">{{ strtoupper($localeCode) }}</span>
                            <span class="text-[9px] px-1 py-0.5 rounded
                                {{ $existingTrans->status === 'published' ? 'bg-emerald-100 text-emerald-700' : ($existingTrans->status === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-slate-200 text-slate-600') }}">
                                {{ $existingTrans->status }}
                            </span>
                        </a>
                    @else
                        <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-semibold text-slate-400 whitespace-nowrap"
                              title="{{ __('Read-only translation') }}">
                            <span class="font-mono">{{ strtoupper($localeCode) }}</span>
                            <span class="text-[9px] px-1 py-0.5 rounded bg-slate-200 text-slate-600">
                                {{ $existingTrans->status }}
                            </span>
                        </span>
                    @endif

                    @if ($canEdit)
                        <form action="{{ route('admin.articles-trans.destroy', $existingTrans->id) }}"
                              method="POST"
                              class="inline-flex border-l border-slate-200 shrink-0"
                              onsubmit="return confirm('{{ __('Are you sure you want to delete this translation?') }}');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="px-1.5 py-1 text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors text-xs font-bold leading-none"
                                    title="{{ __('Delete Translation') }}">
                                &times;
                            </button>
                        </form>
                    @endif
                </div>
            @else
                {{-- ➕ الترجمة مفقودة — بارزة بالأحمر --}}
                @if ($canCreate)
                    <a href="{{ route('admin.articles-trans.create', $article->id) }}?locale={{ $localeCode }}"
                       class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-semibold bg-red-50 border border-dashed border-red-300 text-red-600 hover:bg-red-100 hover:border-red-400 transition-colors shrink-0 whitespace-nowrap"
                       title="{{ __('Add :lang translation', ['lang' => $localeProps['name']]) }}">
                        <span class="font-mono">{{ strtoupper($localeCode) }}</span>
                        <span class="text-[10px] font-bold">+</span>
                    </a>
                @else
                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-mono bg-red-50 border border-dashed border-red-200 text-red-300 cursor-not-allowed shrink-0 whitespace-nowrap"
                          title="{{ __('No permission to add translation') }}">
                        {{ strtoupper($localeCode) }}
                    </span>
                @endif
            @endif
        @endforeach
    </div>
</td>



                        {{-- Gallery --}}
                        <td class="py-3.5 px-4">
                            <button class="show-gallery inline-flex items-center gap-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 font-medium px-2.5 py-1 rounded-lg text-xs transition-colors cursor-pointer"
                                    data-article-id="{{ $article->id }}">
                                <span>📷</span>
                                <span class="artilce-content_imgs-count font-semibold">{{ $article->content_imgs_count }}</span>
                            </button>
                        </td>

                        {{-- Actions Column --}}
                        <td class="py-3.5 px-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                @hasanyrole('super-admin|editor')
                                    <a href="{{ route('admin.articles.edit', $article->id) }}"
                                       class="bg-blue-50 text-blue-600 hover:bg-blue-100 border border-blue-200 px-2.5 py-1 rounded-lg text-xs font-medium transition-colors">
                                        Edit Main
                                    </a>

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
                                @else
                                    <span class="text-slate-400 italic text-xs">Translations Only</span>
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