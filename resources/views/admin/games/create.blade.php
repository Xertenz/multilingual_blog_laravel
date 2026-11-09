@extends('layouts.admin.master')
@section('title', 'Create New Game')

@section('content')
<div class="container mx-auto pb-8 px-4 max-w-2xl">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Create New Game</h1>
            <p class="text-xs text-slate-500">Fill out basic game metadata</p>
        </div>
        <a href="{{ route('admin.games.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 bg-white border border-slate-200 px-3 py-1.5 rounded-lg shadow-2xs">
            &larr; Back to Games
        </a>
    </div>

    <div class="bg-white shadow rounded-2xl border border-slate-200 overflow-hidden">
        <div class="p-6">
            <form action="{{ route('admin.games.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <!-- Category -->
                <div class="bg-white rounded-xl p-4 border border-slate-200">
                    <label for="game_category_id" class="block text-xs font-semibold text-slate-600 mb-1.5">
                        Choose Category <span class="text-red-500">*</span>
                    </label>
                    <select name="game_category_id" id="game_category_id" class="w-full rounded-lg border border-slate-300 text-sm py-2 px-3 outline-none">
                        <option value="" disabled selected>-- Select a category --</option>
                        @foreach ($cats as $cat)
                            <option value="{{ $cat->id }}" {{ old('game_category_id') == $cat->id ? 'selected' : '' }}>
                                {{ optional($cat->translations->where('locale', app()->getLocale())->first())->name ?? $cat->label }}
                            </option>
                        @endforeach
                    </select>
                    @error('game_category_id') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                </div>

                <!-- Internal Title -->
                <div class="bg-white rounded-xl p-4 border border-slate-200">
                    <label for="internal_title" class="block text-xs font-semibold text-slate-600 mb-1.5">
                        Internal Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="internal_title" id="internal_title" value="{{ old('internal_title') }}" class="w-full rounded-lg border border-slate-300 text-sm py-2 px-3 outline-none" placeholder="e.g., la-noire-game">
                    @error('internal_title') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                </div>

                <!-- Developer & Year -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-white rounded-xl p-4 border border-slate-200">
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Developer / Studio</label>
                        <input type="text" name="developer" value="{{ old('developer') }}" class="w-full rounded-lg border border-slate-300 text-sm py-2 px-3 outline-none">
                    </div>
                    <div class="bg-white rounded-xl p-4 border border-slate-200">
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Published Year</label>
                        <input type="number" name="published_year" value="{{ old('published_year') }}" placeholder="2024" class="w-full rounded-lg border border-slate-300 text-sm py-2 px-3 outline-none">
                    </div>
                </div>

                <!-- Featured Image -->
                <div class="bg-white rounded-xl p-4 border border-slate-200">
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Featured Image <span class="text-red-500">*</span></label>
                    <input type="file" name="featured_img" class="w-full border border-slate-300 rounded-lg p-2 text-xs">
                    @error('featured_img') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                </div>

                <!-- Tags -->
                <div class="bg-white rounded-xl p-4 border border-slate-200">
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Game Tags</label>
                    <select name="tags[]" multiple class="w-full rounded-lg border border-slate-300 text-sm p-2 h-24">
                        @foreach ($tags as $tag)
                            <option value="{{ $tag->id }}" {{ is_array(old('tags')) && in_array($tag->id, old('tags')) ? 'selected' : '' }}>
                                {{ optional($tag->translations->where('locale', app()->getLocale())->first())->name ?? $tag->label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('admin.games.index') }}" class="bg-white text-slate-700 font-semibold px-4 py-2 rounded-lg border border-slate-300 text-sm">Cancel</a>
                    <button type="submit" class="bg-indigo-600 text-white font-semibold px-5 py-2 rounded-lg text-sm shadow-2xs">Save Game &rarr;</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection