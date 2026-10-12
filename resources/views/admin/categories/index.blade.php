{{-- resources/views/admin/categories/index.blade.php --}}
@extends('layouts.admin.master')

{{-- @section('page-title', 'All Categories') --}}
@section('title', 'Categories')

@section('content')
    {{-- Header Actions --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Categories Management</h2>
            <p class="text-sm text-slate-500">Manage and organize your content categories</p>
        </div>
        <a href="{{ route('admin.categories.create') }}" 
           class="bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm px-4 py-2 rounded-lg shadow-sm hover:shadow transition-all flex items-center gap-2">
            <span>+ Create Category</span>
        </a>
    </div>
    
    {{-- Categories Table Container --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        <th class="py-3 px-4">ID</th>
                        <th class="py-3 px-4">Featured Img</th>
                        <th class="py-3 px-4">Name / Sign</th>
                        <th class="py-3 px-4">Slug</th>
                        <th class="py-3 px-4">Articles Count</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($cats as $category)
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        {{-- ID --}}
                        <td class="py-3.5 px-4 font-mono text-xs text-slate-500">
                            #{{ $category->id }}
                        </td>

                        {{-- Featured Image --}}
                        <td class="py-3.5 px-4">
                            <div class="w-12 h-10 rounded-lg overflow-hidden bg-slate-100 border border-slate-200 shrink-0 flex items-center justify-center">
                                @if($category->getFirstMediaUrl('featured_imgs', 'thumb'))
                                    <img 
                                        src="{{ $category->getFirstMediaUrl('featured_imgs', 'thumb') }}"
                                        alt="Category image"
                                        class="w-full h-full object-cover"
                                    />
                                @else
                                    <span class="text-[10px] text-slate-400">No Img</span>
                                @endif
                            </div>
                        </td>

                        {{-- Name / Sign --}}
                        <td class="py-3.5 px-4 font-semibold text-slate-800">
                            {{ $category->sign }}
                        </td>

                        {{-- Slug --}}
                        <td class="py-3.5 px-4 text-slate-500 text-xs font-mono">
                            {{ "[Inside Category Translation]" }}
                        </td>

                        {{-- Articles Count --}}
                        <td class="py-3.5 px-4">
                            <span class="bg-slate-100 text-slate-700 px-2.5 py-1 rounded-md text-xs font-semibold border border-slate-200">
                                {{ $category->articles->count() ?? 0 }} articles
                            </span>
                        </td>

                        {{-- Actions --}}
                        <td class="py-3.5 px-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.categories.edit', $category) }}"
                                   class="bg-blue-50 text-blue-600 hover:bg-blue-100 border border-blue-200 px-2.5 py-1 rounded-lg text-xs font-medium transition-colors">
                                    Edit
                                </a>
                                
                                <form method="POST" action="{{ route('admin.categories.destroy', $category->id) }}"
                                      onsubmit="return confirm('Are you sure you want to delete this category?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-50 text-red-600 hover:bg-red-100 border border-red-200 px-2.5 py-1 rounded-lg text-xs font-medium transition-colors cursor-pointer">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                            <div class="flex flex-col items-center justify-center">
                                <span class="text-3xl mb-2">📂</span>
                                <p class="text-sm font-medium text-slate-600">No categories found.</p>
                                <a href="{{ route('admin.categories.create') }}" class="text-blue-600 hover:underline text-xs font-medium mt-1">
                                    Create your first category &rarr;
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    {{-- Pagination --}}
    <div class="mt-6">
        {{ $cats->links() }}
    </div>
@endsection

@push('scripts')
    <script>
        window.addEventListener('DOMContentLoaded', function() {
            if(@json(session('success'))) {
                const successMsg = @json(session('success'));
                showToast('success', successMsg);
            }
        });
    </script>
@endpush