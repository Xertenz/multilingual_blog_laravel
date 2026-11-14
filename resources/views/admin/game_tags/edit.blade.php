@extends('layouts.admin.master')

@section('title', 'Edit Game Tag - Shadow Files')

@section('content')
<div class="container-fluid px-4 py-4">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Game Tag</h1>

    <form action="{{ route('admin.game-tags.update', $tag->id) }}" method="POST" class="bg-white shadow-md rounded-lg p-6">
        @csrf
        @method('PUT')

        <div class="mb-6">
            <label class="block text-gray-700 font-bold mb-2">System Label *</label>
            <input type="text" name="label" value="{{ old('label', $tag->label) }}" required class="w-full border border-gray-300 rounded-lg p-2 focus:ring focus:ring-blue-200">
            @error('label') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <hr class="my-6">

        <h3 class="text-xl font-semibold mb-4 text-gray-700">Translations & SEO (Multi-Language)</h3>

        <div class="space-y-4">
            @php
                $translations = $tag->translations->keyBy('locale');
            @endphp
            @foreach($supportedLocales as $localeCode => $properties)
                @php
                    $trans = $translations[$localeCode] ?? null;
                @endphp
                <div class="border border-gray-200 p-4 rounded-lg bg-gray-50 space-y-3">
                    <h4 class="font-bold text-blue-600 uppercase">{{ $properties['native'] }} ({{ $localeCode }})</h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tag Name</label>
                            <input type="text" name="translations[{{ $localeCode }}][name]" value="{{ old("translations.{$localeCode}.name", optional($trans)->name) }}" dir="{{ $localeCode == 'ar' ? 'rtl' : 'ltr' }}" class="w-full border border-gray-300 rounded p-2 bg-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Slug</label>
                            <input type="text" name="translations[{{ $localeCode }}][slug]" value="{{ old("translations.{$localeCode}.slug", optional($trans)->slug) }}" dir="{{ $localeCode == 'ar' ? 'rtl' : 'ltr' }}" class="w-full border border-gray-300 rounded p-2 bg-white">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Description (SEO / Header)</label>
                        <textarea name="translations[{{ $localeCode }}][description]" rows="2" dir="{{ $localeCode == 'ar' ? 'rtl' : 'ltr' }}" class="w-full border border-gray-300 rounded p-2 bg-white" placeholder="Brief description of this game tag...">{{ old("translations.{$localeCode}.description", optional($trans)->description) }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Meta Title</label>
                            <input type="text" name="translations[{{ $localeCode }}][meta_title]" value="{{ old("translations.{$localeCode}.meta_title", optional($trans)->meta_title) }}" dir="{{ $localeCode == 'ar' ? 'rtl' : 'ltr' }}" class="w-full border border-gray-300 rounded p-2 bg-white" placeholder="SEO Title">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Meta Description</label>
                            <input type="text" name="translations[{{ $localeCode }}][meta_description]" value="{{ old("translations.{$localeCode}.meta_description", optional($trans)->meta_description) }}" dir="{{ $localeCode == 'ar' ? 'rtl' : 'ltr' }}" class="w-full border border-gray-300 rounded p-2 bg-white" placeholder="SEO Description">
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">Update Tag</button>
            <a href="{{ route('admin.game-tags.index') }}" class="ml-4 text-gray-600 hover:underline">Cancel</a>
        </div>
    </form>
</div>
@endsection