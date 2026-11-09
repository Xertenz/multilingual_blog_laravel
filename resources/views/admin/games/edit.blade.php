@extends('layouts.admin.master')

@section('title', 'Edit Game Summary - Shadow Files')

@section('content')
<div class="container-fluid px-4 py-4">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Game Summary</h1>

    <form action="{{ route('admin.games.update', $game->id) }}" method="POST" enctype="multipart/form-data" class="bg-white shadow-md rounded-lg p-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div>
                <label class="block text-gray-700 font-bold mb-2">Internal Identifier Title *</label>
                <input type="text" name="internal_title" value="{{ old('internal_title', $game->internal_title) }}" required class="w-full border border-gray-300 rounded-lg p-2">
                @error('internal_title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-gray-700 font-bold mb-2">Category *</label>
                <select name="game_category_id" required class="w-full border border-gray-300 rounded-lg p-2">
                    <option value="">Select Category</option>
                    @foreach($cats as $cat)
                        <option value="{{ $cat->id }}" {{ old('game_category_id', $game->game_category_id) == $cat->id ? 'selected' : '' }}>
                            {{ optional($cat->translations->where('locale', app()->getLocale())->first())->name ?? $cat->label }}
                        </option>
                    @endforeach
                </select>
                @error('game_category_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-gray-700 font-bold mb-2">Developer / Studio</label>
                <input type="text" name="developer" value="{{ old('developer', $game->developer) }}" class="w-full border border-gray-300 rounded-lg p-2">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-gray-700 font-bold mb-2">Published Year</label>
                <input type="number" name="published_year" value="{{ old('published_year', $game->published_year) }}" class="w-full border border-gray-300 rounded-lg p-2">
            </div>

            <div>
                <label class="block text-gray-700 font-bold mb-2">Change Main Cover Image</label>
                @if($game->hasMedia('cover_imgs'))
                    <div class="mb-2">
                        <img src="{{ $game->getFirstMediaUrl('cover_imgs', 'thumb') }}" alt="Cover" class="w-16 h-20 object-cover rounded">
                    </div>
                @endif
                <input type="file" name="featured_img" class="w-full border border-gray-300 rounded-lg p-2">
                @error('featured_img') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Tags Selection -->
        <div class="mb-6">
            <label class="block text-gray-700 font-bold mb-2">Tags</label>
            <div class="flex flex-wrap gap-4 bg-gray-50 p-4 rounded-lg border border-gray-200">
                @php
                    $selectedTags = old('tags', $game->gameTags->pluck('id')->toArray());
                @endphp
                @foreach($tags as $tag)
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="tags[]" value="{{ $tag->id }}" {{ in_array($tag->id, $selectedTags) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">
                            {{ optional($tag->translations->where('locale', app()->getLocale())->first())->name ?? $tag->label }}
                        </span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="mt-6 flex items-center justify-end">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">Update Game Basic Info</button>
            <a href="{{ route('admin.games.index') }}" class="ml-4 text-gray-600 hover:underline">Cancel</a>
        </div>
    </form>
</div>
@endsection