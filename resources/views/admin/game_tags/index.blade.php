@extends('layouts.admin.master')

@section('title', 'Game Tags - Shadow Files')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Game Tags</h1>
        <a href="{{ route('admin.game-tags.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
            + Add New Tag
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    <th class="px-6 py-3">ID</th>
                    <th class="px-6 py-3">System Label</th>
                    <th class="px-6 py-3">Name (Default)</th>
                    <th class="px-6 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($tags as $tag)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $tag->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">{{ $tag->label }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            {{ $tag->translations->where('locale', app()->getLocale())->first()->name ?? ($tag->translations->first()->name ?? 'N/A') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.game-tags.edit', $tag->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                            <form action="{{ route('admin.game-tags.destroy', $tag->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this tag?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">No tags found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $tags->links() }}
    </div>
</div>
@endsection