@extends('layouts.admin.master')

@section('title', 'إدارة أرشيف مرئيات الجريمة')

@push('styles')
    @vite(['resources/css/components/article-gallery.css'])
@endpush

@section('content')
    {{-- Header Actions --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Media Summaries Management</h2>
            <p class="text-sm text-slate-500">Manage, edit, translate, and organize True Crime movies, series, and documentaries</p>
        </div>
        
        @hasanyrole('super-admin|editor')
        <a href="{{ route('admin.media-summaries.create') }}" 
           class="bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm px-4 py-2 rounded-lg shadow-sm hover:shadow transition-all flex items-center gap-2">
            <span>+ Create Media Summary</span>
        </a>
        @endhasanyrole
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg mb-6 text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif

    {{-- Table Container --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        <th class="py-3 px-4">Featured Image</th>
                        <th class="py-3 px-4">Type</th>
                        <th class="py-3 px-4">Year</th>
                        <th class="py-3 px-4">IMDb</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Location</th>
                        <th class="py-3 px-4">Translations</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                        <th class="py-3 px-4">Gallery</th>

                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse ($mediaList as $item)
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        {{-- Featured Image --}}
                        <td class="py-3.5 px-4">
                            <div class="w-12 h-16 rounded-lg overflow-hidden bg-slate-100 border border-slate-200 shrink-0">
                                @if($item->hasMedia('featured_imgs'))
                                    <img 
                                        src="{{ $item->getFirstMediaUrl('featured_imgs') }}"
                                        class="w-full h-full object-cover"
                                        alt="Featured Image"
                                    />
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-slate-400 text-[10px]">No Featured Image</div>
                                @endif
                            </div>
                        </td>

                        {{-- Type Badge --}}
                        <td class="py-3.5 px-4 font-medium text-slate-700 whitespace-nowrap">
                            <span class="bg-slate-100 text-slate-700 px-2.5 py-1 rounded-md text-xs font-semibold border border-slate-200 uppercase">
                                {{ $item->type?->value }}
                            </span>
                        </td>

                        {{-- Release Year --}}
                        <td class="py-3.5 px-4 text-slate-600 font-mono text-xs">
                            {{ $item->release_year ?? '—' }}
                        </td>

                        {{-- IMDb Rating --}}
                        <td class="py-3.5 px-4 text-slate-600 font-medium">
                            @if($item->imdb_rating)
                                <span class="text-amber-600 font-bold">★ {{ $item->imdb_rating }}</span>
                            @else
                                —
                            @endif
                        </td>

                        {{-- Status Badge --}}
                        <td class="py-3.5 px-4">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium border
                                {{ $item->status?->value === 'published' 
                                    ? 'bg-emerald-50 text-emerald-700 border-emerald-200' 
                                    : 'bg-amber-50 text-amber-700 border-amber-200' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $item->status?->value === 'published' ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                                {{ ucfirst($item->status?->value ?? 'published') }}
                            </span>
                        </td>

                        {{-- Location --}}
                        <td class="py-3.5 px-4 text-slate-600 text-xs">
                            {{ $item->location->translation->name ?? 'No Location' }}
                        </td>

                        {{-- Translations Column (إدارة ترجمات العمل بشكل مستقل) --}}
                        <td class="py-3.5 px-4">
                            <div class="flex items-center gap-2 flex-wrap">
                                @foreach ($item->translations as $trans)
                                    <div class="inline-flex items-center bg-slate-50 border border-slate-200 rounded-lg p-0.5 shadow-2xs">
                                        <a href="{{ route('admin.media-summaries-trans.edit', $trans->id) }}" 
                                           class="px-2 py-0.5 text-xs font-semibold text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded transition-colors"
                                           title="Edit Translation">
                                            {{ strtoupper($trans->locale) }} 
                                            <span class="text-[10px] text-slate-400">({{ $trans->status?->value }})</span>
                                        </a>

                                        <form action="{{ route('admin.media-summaries-trans.destroy', $trans->id) }}" 
                                              method="POST" 
                                              class="inline"
                                              onsubmit="return confirm('Are you sure you want to delete this translation?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-slate-400 hover:text-red-600 px-1 hover:bg-red-50 rounded transition-colors text-xs font-bold" title="Delete Translation">
                                                &times;
                                            </button>
                                        </form>
                                    </div>
                                @endforeach

                                @hasanyrole('super-admin|editor|writer')
                                    <a href="{{ route('admin.media-summaries-trans.create', $item->id) }}"
                                       class="inline-flex items-center gap-1 bg-amber-50 border border-amber-200 text-amber-700 hover:bg-amber-100 px-2 py-1 rounded-lg text-xs font-medium transition-colors">
                                        <span>+ Add Trans</span>
                                    </a>
                                @endhasanyrole
                            </div>
                        </td>

                        {{-- Actions Column (تعديل وحذف العمل الأساسي) --}}
                        <td class="py-3.5 px-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                @hasanyrole('super-admin|editor')
                                    <a href="{{ route('admin.media-summaries.edit', $item->id) }}" 
                                       class="bg-blue-50 text-blue-600 hover:bg-blue-100 border border-blue-200 px-2.5 py-1 rounded-lg text-xs font-medium transition-colors">
                                        Edit Main
                                    </a>
                                    
                                    <form action="{{ route('admin.media-summaries.destroy', $item->id) }}" 
                                          method="POST" 
                                          class="inline"
                                          onsubmit="return confirm('Are you sure you want to delete this main media summary with all its translations?');">
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

                        {{-- Gallery Column --}}
                        <td class="py-3.5 px-4">
                            <button class="show-gallery inline-flex items-center gap-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 font-medium px-2.5 py-1 rounded-lg text-xs transition-colors cursor-pointer" 
                                    data-media-summary-id="{{ $item->id }}">
                                <span>📷</span>
                                <span class="media-content-imgs-count font-semibold">{{ $item->getMedia('content_imgs')->count() }}</span>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-6 px-4 text-center text-slate-500 text-sm">
                            No media summaries found. Click "+ Create Media Summary" to add one.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $mediaList->links() }}
    </div>
@endsection


@push('scripts')
    <script>
        window.upload_url = "{{ route('admin.media-summaries.upload.content') }}";
        window.article_content_imgs_url = "{{ route('admin.media-summaries.get.content') }}";
        window.delete_content_img_url = "{{ route('admin.media-summaries.delete.content_img') }}";
    </script>
    @vite(['resources/js/components/media-summary-gallery.js'])
@endpush