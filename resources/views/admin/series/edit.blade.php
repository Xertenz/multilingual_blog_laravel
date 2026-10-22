@extends('layouts.admin.master')

@section('title', 'Edit Series')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Edit Series: {{ $series->currentTranslation->title ?? 'Untitled' }}</h1>
        <a href="{{ route('admin.series.index') }}" class="bg-slate-600 hover:bg-slate-700 text-white px-4 py-2 rounded-lg text-sm transition">
            &larr; Back to Series
        </a>
    </div>

    <form action="{{ route('admin.series.update', $series->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- General Information (Vertical Layout) --}}
        <div class="bg-white shadow rounded-xl p-6 border border-slate-100 space-y-4">
            <h2 class="text-lg font-semibold text-slate-700 pb-2 border-b">General Information</h2>
            
            {{-- Internal Title --}}
            <div>
                <label for="internal_title" class="block text-sm font-medium text-slate-700 mb-1">Internal Title (Unique Identifier)</label>
                <input type="text" name="internal_title" id="internal_title" value="{{ old('internal_title', $series->internal_title) }}" 
                       class="w-full rounded-lg border border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2 px-3 outline-none transition @error('internal_title') border-red-500 bg-red-50/50 @enderror"
                       placeholder="e.g., php-mastery-series">
                <p class="text-xs text-slate-500 mt-1">Used programmatically to identify the series in templates.</p>
                @error('internal_title')
                    <div class="text-red-500 text-xs mt-1 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- Status --}}
            <div>
                <label for="status" class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                <select name="status" id="status" class="w-full rounded-lg border border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2 px-3 outline-none transition @error('status') border-red-500 bg-red-50/50 @enderror">
                    <option value="published" {{ old('status', $series->status) == 'published' ? 'selected' : '' }}>Published</option>
                    <option value="draft" {{ old('status', $series->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                </select>
                @error('status')
                    <div class="text-red-500 text-xs mt-1 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>

        {{-- Translations (Vertical Layout) --}}
        <div class="bg-white shadow rounded-xl p-6 border border-slate-100 space-y-6">
            <h2 class="text-lg font-semibold text-slate-700 pb-2 border-b">Translations</h2>
            
            @php
                $translations = $series->translations->keyBy('locale');
            @endphp

            <div class="space-y-6">
                {{-- English Translation (en) --}}
                <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 space-y-4">
                    <h3 class="font-bold text-indigo-600 text-sm flex items-center">
                        <span class="bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded text-xs ml-2">en</span> English
                    </h3>
                    
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Series Title (English)*</label>
                        <input type="text" name="translations[en][title]" 
                               value="{{ old('translations.en.title', optional($translations->get('en'))->title) }}" 
                               class="w-full rounded-lg border border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2 px-3 outline-none transition @error('translations.en.title') border-red-500 bg-red-50/50 @enderror">
                        @error('translations.en.title')
                            <div class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Slug (English)*</label>
                        <input type="text" name="translations[en][slug]" 
                               value="{{ old('translations.en.slug', optional($translations->get('en'))->slug) }}" 
                               class="w-full rounded-lg border border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2 px-3 outline-none transition @error('translations.en.slug') border-red-500 bg-red-50/50 @enderror">
                        @error('translations.en.slug')
                            <div class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Description (English)</label>
                        <textarea name="translations[en][description]" rows="3" 
                                  class="w-full rounded-lg border border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2 px-3 outline-none transition @error('translations.en.description') border-red-500 bg-red-50/50 @enderror">{{ old('translations.en.description', optional($translations->get('en'))->description) }}</textarea>
                        @error('translations.en.description')
                            <div class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                {{-- Arabic Translation (ar) --}}
                <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 space-y-4">
                    <h3 class="font-bold text-indigo-600 text-sm flex items-center">
                        <span class="bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded text-xs ml-2">ar</span> Arabic
                    </h3>
                    
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Series Title (Arabic)*</label>
                        <input type="text" name="translations[ar][title]" 
                               value="{{ old('translations.ar.title', optional($translations->get('ar'))->title) }}" 
                               dir="rtl"
                               class="w-full rounded-lg border border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2 px-3 outline-none transition @error('translations.ar.title') border-red-500 bg-red-50/50 @enderror">
                        @error('translations.ar.title')
                            <div class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Slug (Arabic)*</label>
                        <input type="text" name="translations[ar][slug]" 
                               value="{{ old('translations.ar.slug', optional($translations->get('ar'))->slug) }}" 
                               dir="ltr"
                               class="w-full rounded-lg border border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2 px-3 outline-none transition @error('translations.ar.slug') border-red-500 bg-red-50/50 @enderror">
                        @error('translations.ar.slug')
                            <div class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Description (Arabic)</label>
                        <textarea name="translations[ar][description]" rows="3" 
                                  dir="rtl"
                                  class="w-full rounded-lg border border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2 px-3 outline-none transition @error('translations.ar.description') border-red-500 bg-red-50/50 @enderror">{{ old('translations.ar.description', optional($translations->get('ar'))->description) }}</textarea>
                        @error('translations.ar.description')
                            <div class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Connected Articles --}}
        <div class="bg-white shadow rounded-xl p-6 border border-slate-100">
            <h2 class="text-lg font-semibold text-slate-700 mb-2">Connected Articles</h2>
            <p class="text-xs text-slate-500 mb-4">Select the articles you want to add to this series or update their order.</p>

            @php
                $selectedArticles = old('articles', $series->articles->pluck('id')->toArray());
            @endphp

            <div class="max-h-60 overflow-y-auto border border-slate-200 rounded-lg p-3 space-y-2">
                @forelse($articles as $article)
                    <label class="flex items-center space-x-3 p-2 hover:bg-slate-50 rounded-lg cursor-pointer transition">
                        <input type="checkbox" name="articles[]" value="{{ $article->id }}" 
                               {{ in_array($article->id, $selectedArticles) ? 'checked' : '' }}
                               class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 w-4 h-4 ml-3">
                        <span class="text-sm text-slate-700 font-medium flex-1">
                            {{ $article->translations->first()->title ?? 'Untitled Article (ID: ' . $article->id . ')' }}
                        </span>
                    </label>
                @empty
                    <p class="text-sm text-slate-500 text-center py-4">No articles available to add currently.</p>
                @endforelse
            </div>
            @error('articles')
                <div class="text-red-500 text-xs mt-2 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Form Actions --}}
        <div class="flex justify-end space-x-3">
            <a href="{{ route('admin.series.index') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 px-6 py-2.5 rounded-lg text-sm font-medium transition">
                Cancel
            </a>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                Update Series
            </button>
        </div>
    </form>
</div>
@endsection