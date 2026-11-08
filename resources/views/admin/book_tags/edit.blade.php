@extends('layouts.admin.master')

@section('content')
<div class="container-fluid px-4 py-4 max-w-3xl mx-auto">
    <h1 class="text-xl font-bold text-slate-800 mb-6">Edit Book Tag: {{ $tag->label }}</h1>

    <form action="{{ route('admin.book-tags.update', $tag->id) }}" method="POST" class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 space-y-6">
        @csrf
        @method('PUT')

        <!-- General Label -->
        <div>
            <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">System Label</label>
            <input type="text" name="label" value="{{ old('label', $tag->label) }}" required class="w-full rounded-lg border border-slate-300 text-sm py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500">
            @error('label') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
        </div>

        <hr class="border-slate-200">

        <!-- Translations -->
        <h3 class="text-sm font-bold text-slate-800 uppercase">Tag Translations</h3>

        <div class="space-y-3">
            @php
                // تجهيز الترجمات الحالية بربطها مع رمز اللغة لتسهيل استرجاعها
                $translations = $tag->translations->keyBy('locale');
            @endphp

            @foreach($supportedLocales as $localeCode => $properties)
                @php
                    $trans = $translations[$localeCode] ?? null;
                @endphp
                <div class="flex items-center gap-4 p-3 bg-slate-50 rounded-lg border border-slate-200">
                    <span class="w-24 text-xs font-bold text-indigo-700">{{ $properties['native'] }} ({{ $localeCode }})</span>
                    <input type="text" name="translations[{{ $localeCode }}][name]" placeholder="Tag Name in {{ $properties['name'] }}" value="{{ old("translations.{$localeCode}.name", $trans->name ?? '') }}" class="flex-1 rounded-lg border border-slate-300 text-sm py-1.5 px-3 bg-white">
                </div>
            @endforeach
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-slate-200">
            <a href="{{ route('admin.book-tags.index') }}" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-lg text-sm font-medium hover:bg-slate-200 transition">Cancel</a>
            <button type="submit" class="px-5 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition">Update Tag</button>
        </div>
    </form>
</div>
@endsection