{{-- resources/views/admin/tags/index.blade.php --}}
@extends('layouts.admin.master')

@section('page-title', 'Tags')
@section('title', 'Tags')

@section('content')
    {{-- Header Actions --}}
    <div class="flex justify-between items-center mb-6">
        <p class="text-slate-600">Manage your content tags</p>
        <a href="{{ route('admin.tags.create') }}" 
           class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-md flex items-center gap-1 transition">
            <span>Add +</span>
        </a>
    </div>
    
    {{-- Tags Table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="text-xs text-slate-700 bg-slate-50">
                <tr>
                    <th class="px-6 py-3">Id</th>
                    <th class="px-6 py-3">Sign</th>
                    <th class="px-6 py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tags as $tag)
                <tr class="border-b hover:bg-slate-50">
                    <td class="px-6 py-4">{{ $tag->id }}</td>
                    <td class="px-6 py-4 font-medium">{{ $tag->sign }}</td>
                    <td class="px-6 py-4">
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.tags.edit', $tag) }}" 
                               class="text-blue-600 hover:text-blue-900">Edit</a>
                            <form method="POST" action="{{route('admin.tags.destroy', $tag->id)}}" 
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
                        No tags found.
                        <a href="{{ route('admin.tags.create') }}" class="text-blue-600 block mt-2">
                            Create your first tag
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    {{-- Pagination --}}
    <div class="mt-6">
        {{ $tags->links() }}
    </div>
@endsection