@extends('layouts.admin.master')

@section('content')
<div class="container-fluid px-4 py-4 max-w-4xl mx-auto">
    <h1 class="text-xl font-bold text-slate-800 mb-6">Edit Book Category: {{ $cat->label }}</h1>

    <form action="{{ route('admin.book-categories.update', $cat->id) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 space-y-6">
        @csrf
        @method('PUT')

        <!-- General Label -->
        <div>
            <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">System Label (Slug / Unique Key)</label>
            <input type="text" name="cat_label" value="{{ old('cat_label', $cat->label) }}" required class="w-full rounded-lg border border-slate-300 text-sm py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500">
            @error('cat_label') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
        </div>

        <!-- Featured Image -->
        <div>
            <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Featured Image</label>
            @if($cat->hasMedia('featured_imgs'))
                <div class="mb-3 flex items-center gap-4">
                    <img src="{{ $cat->getFirstMediaUrl('featured_imgs', 'thumb') }}" class="w-16 h-16 object-cover rounded-lg border">
                    <span class="text-xs text-slate-500">Current Image (Uploading a new one will replace it)</span>
                </div>
            @endif
            <input type="file" name="cat_featured_img" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
            @error('cat_featured_img') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
        </div>

        <hr class="border-slate-200">

        <!-- Translations Section (Multi-language) -->
        <h3 class="text-sm font-bold text-slate-800 uppercase">Translations (Supported Languages)</h3>

        <div class="space-y-4">
            @foreach($supportedLocales as $localeCode => $properties)
                @php
                    $trans = $translations[$localeCode] ?? null;
                @endphp
                <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 space-y-3">
                    <span class="inline-block bg-indigo-100 text-indigo-800 text-xs font-bold px-2 py-0.5 rounded">
                        {{ $properties['native'] }} ({{ $localeCode }})
                    </span>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Name</label>
                            <input type="text" name="translations[{{ $localeCode }}][name]" value="{{ old("translations.{$localeCode}.name", $trans->name ?? '') }}" class="w-full rounded-lg border border-slate-300 text-sm py-1.5 px-3 bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Slug (Optional)</label>
                            <input type="text" name="translations[{{ $localeCode }}][slug]" value="{{ old("translations.{$localeCode}.slug", $trans->slug ?? '') }}" class="w-full rounded-lg border border-slate-300 text-sm py-1.5 px-3 bg-white">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Description</label>
                        <textarea name="translations[{{ $localeCode }}][description]" rows="2" class="w-full rounded-lg border border-slate-300 text-sm py-1.5 px-3 bg-white">{{ old("translations.{$localeCode}.description", $trans->description ?? '') }}</textarea>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-slate-200">
            <a href="{{ route('admin.book-categories.index') }}" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-lg text-sm font-medium hover:bg-slate-200 transition">Cancel</a>
            <button type="submit" class="px-5 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition">Update Category</button>
        </div>
    </form>
</div>
@endsection