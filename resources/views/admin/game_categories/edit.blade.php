@extends('layouts.admin.master')

@section('title', 'Edit Game Category - Shadow Files')

@section('content')
<div class="container-fluid px-4 py-4">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Game Category</h1>

    <form action="{{ route('admin.game-categories.update', $cat->id) }}" method="POST" enctype="multipart/form-data" class="bg-white shadow-md rounded-lg p-6">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">System Label</label>
            <input type="text" name="cat_label" value="{{ old('cat_label', $cat->label) }}" required class="w-full border border-gray-300 rounded-lg p-2 focus:ring focus:ring-blue-200">
            @error('cat_label') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 font-bold mb-2">Change Featured Image</label>
            @if($cat->hasMedia('featured_imgs'))
                <div class="mb-2">
                    <img src="{{ $cat->getFirstMediaUrl('featured_imgs', 'thumb') }}" alt="Current Image" class="w-20 h-20 object-cover rounded">
                </div>
            @endif
            <input type="file" name="cat_featured_img" class="w-full border border-gray-300 rounded-lg p-2">
            @error('cat_featured_img') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <hr class="my-6">

        <h3 class="text-xl font-semibold mb-4 text-gray-700">Translations (Multi-Language)</h3>

        <div class="space-y-6">
            @foreach($supportedLocales as $localeCode => $properties)
                @php
                    $trans = $translations[$localeCode] ?? null;
                @endphp
                <div class="border border-gray-200 p-4 rounded-lg bg-gray-50">
                    <h4 class="font-bold text-blue-600 mb-2 uppercase">{{ $properties['native'] }} ({{ $localeCode }})</h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Name</label>
                            <input type="text" name="translations[{{ $localeCode }}][name]" value="{{ old("translations.{$localeCode}.name", optional($trans)->name) }}" class="w-full border border-gray-300 rounded p-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Slug</label>
                            <input type="text" name="translations[{{ $localeCode }}][slug]" value="{{ old("translations.{$localeCode}.slug", optional($trans)->slug) }}" class="w-full border border-gray-300 rounded p-2">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="translations[{{ $localeCode }}][description]" rows="2" class="w-full border border-gray-300 rounded p-2">{{ old("translations.{$localeCode}.description", optional($trans)->description) }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Meta Title</label>
                            <input type="text" name="translations[{{ $localeCode }}][meta_title]" value="{{ old("translations.{$localeCode}.meta_title", optional($trans)->meta_title) }}" class="w-full border border-gray-300 rounded p-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Meta Description</label>
                            <input type="text" name="translations[{{ $localeCode }}][meta_description]" value="{{ old("translations.{$localeCode}.meta_description", optional($trans)->meta_description) }}" class="w-full border border-gray-300 rounded p-2">
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">Update Category</button>
            <a href="{{ route('admin.game-categories.index') }}" class="ml-4 text-gray-600 hover:underline">Cancel</a>
        </div>
    </form>
</div>
@endsection