{{-- resources/views/admin/categories/index.blade.php --}}
@extends('layouts.admin.master')

@section('page-title', 'All Categories')
@section('title', 'Categories')

@section('content')
    {{-- Header Actions --}}
    <div class="flex justify-between items-center mb-6">
        <p class="text-slate-600">Manage your content categories</p>
        <a href="{{ route('admin.categories.create') }}" 
           class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-lg flex items-center gap-1 transition">
            <span>Add</span>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
        </a>
    </div>
    
    {{-- Categories Table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="text-xs text-slate-700 bg-slate-50">
                <tr>
                    <th class="px-6 py-3">Id</th>
                    <th class="px-6 py-3">Name</th>
                    <th class="px-6 py-3">Featured Img</th>
                    <th class="px-6 py-3">Slug</th>
                    <th class="px-6 py-3">Articles Count</th>
                    <th class="px-6 py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cats as $category)
                <tr class="border-b hover:bg-slate-50">
                    <td class="px-6 py-4">{{ $category->id }}</td>
                    <td class="px-6 py-4 font-medium">{{ $category->sign }}</td>
                    <td class="px-6 py-4 font-medium">
                        <img 
                        src = "{{ $category->getFirstMediaUrl('featured_imgs', 'thumb')?? '' }}"
                         />
                    </td>
                    <td class="px-6 py-4 text-slate-500">{{ "[Inside Category Translation]" }}</td>
                    <td class="px-6 py-4">{{ $category->articles->count() ?? 0 }}</td>
                    <td class="px-6 py-4">
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.categories.edit', $category) }}" 
                               class="text-blue-600 hover:text-blue-900">Edit</a>
                            <form method="POST" action="{{route('admin.categories.destroy', $category->id)}}" 
                                  onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 cursor-pointer">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                        No categories found.
                        <a href="{{ route('admin.categories.create') }}" class="text-blue-600 block mt-2">
                            Create your first category
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    {{-- Pagination --}}
    <div class="mt-6">
        {{ $cats->links() }}
    </div>
@endsection