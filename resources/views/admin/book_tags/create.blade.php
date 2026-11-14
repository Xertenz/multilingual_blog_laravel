@extends('layouts.admin.master')

@section('content')
<div class="container-fluid px-4 py-4 max-w-3xl mx-auto">
    <h1 class="text-xl font-bold text-slate-800 mb-6">Create New Book Tag</h1>

    <form action="{{ route('admin.book-tags.store') }}" method="POST" class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 space-y-6">
        @csrf

        <!-- General Label -->
        <div>
            <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">System Label</label>
            <input type="text" name="label" value="{{ old('label') }}" required class="w-full rounded-lg border border-slate-300 text-sm py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500">
            @error('label') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
        </div>

        <hr class="border-slate-200">

        <!-- Translations -->
        <h3 class="text-sm font-bold text-slate-800 uppercase">Tag Translations & SEO</h3>

        <div class="space-y-4">
            @foreach($supportedLocales as $localeCode => $properties)
                <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-indigo-700 uppercase">{{ $properties['native'] }} ({{ $localeCode }})</span>
                    </div>

                    <!-- Name -->
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Tag Name</label>
                        <input type="text" name="translations[{{ $localeCode }}][name]" placeholder="Tag Name in {{ $properties['name'] }}" value="{{ old("translations.{$localeCode}.name") }}" class="w-full rounded-lg border border-slate-300 text-sm py-1.5 px-3 bg-white">
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Description (SEO / Header)</label>
                        <textarea name="translations[{{ $localeCode }}][description]" rows="2" placeholder="Brief description..." class="w-full rounded-lg border border-slate-300 text-sm py-1.5 px-3 bg-white">{{ old("translations.{$localeCode}.description") }}</textarea>
                    </div>

                    <!-- Meta Inputs -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">Meta Title</label>
                            <input type="text" name="translations[{{ $localeCode }}][meta_title]" value="{{ old("translations.{$localeCode}.meta_title") }}" class="w-full rounded-lg border border-slate-300 text-sm py-1.5 px-3 bg-white" placeholder="SEO Title">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">Meta Description</label>
                            <input type="text" name="translations[{{ $localeCode }}][meta_description]" value="{{ old("translations.{$localeCode}.meta_description") }}" class="w-full rounded-lg border border-slate-300 text-sm py-1.5 px-3 bg-white" placeholder="SEO Description">
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-slate-200">
            <a href="{{ route('admin.book-tags.index') }}" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-lg text-sm font-medium hover:bg-slate-200 transition">Cancel</a>
            <button type="submit" class="px-5 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition">Save Tag</button>
        </div>
    </form>
</div>
@endsection