@extends("layouts.admin.master")
@section('title', 'Edit Community Case Category')

@section('content')
<div class="container mx-auto py-8 px-4 max-w-3xl">
    <div class="mb-6">
        <a href="{{ route('admin.community-case-categories.index') }}" class="text-xs text-slate-500 hover:text-slate-800">&larr; Back to Categories</a>
        <h1 class="text-2xl font-bold text-slate-800 mt-2">Edit Community Category</h1>
    </div>

    <form action="{{ route('admin.community-case-categories.update', $communityCaseCategory->id) }}" method="POST" class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs space-y-6">
        @csrf
        @method('PUT')

        <!-- General Settings -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">System Label (Slug/Key)</label>
                <input type="text" name="label" value="{{ old('label', $communityCaseCategory->label) }}" required class="w-full text-sm bg-slate-50 border border-slate-300 rounded-lg p-2.5 focus:border-red-600 focus:ring-0">
                @error('label') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div class="flex items-center pt-6">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ $communityCaseCategory->is_active ? 'checked' : '' }} class="rounded border-slate-300 text-red-600 focus:ring-red-500">
                    <span class="text-sm font-medium text-slate-700">Active Status</span>
                </label>
            </div>
        </div>

        <hr class="border-slate-100">

        <!-- Translations Sections -->
        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Translations (Optional)</h3>
        
        <div class="space-y-6">
            @foreach($locales as $localeCode => $properties)
                @php
                    $trans = $translations[$localeCode] ?? null;
                @endphp
                <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 space-y-4">
                    <h4 class="text-xs font-bold text-red-600 uppercase">{{ $properties['native'] }} ({{ $localeCode }})</h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs text-slate-600 mb-1">Category Name</label>
                            <input type="text" name="translations[{{ $localeCode }}][name]" value="{{ old("translations.{$localeCode}.name", $trans->name ?? '') }}" class="w-full text-xs bg-white border border-slate-300 rounded-lg p-2">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-600 mb-1">Custom Slug (Optional)</label>
                            <input type="text" name="translations[{{ $localeCode }}][slug]" value="{{ old("translations.{$localeCode}.slug", $trans->slug ?? '') }}" class="w-full text-xs bg-white border border-slate-300 rounded-lg p-2">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs text-slate-600 mb-1">Description</label>
                        <textarea name="translations[{{ $localeCode }}][description]" rows="2" class="w-full text-xs bg-white border border-slate-300 rounded-lg p-2">{{ old("translations.{$localeCode}.description", $trans->description ?? '') }}</textarea>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="flex justify-end gap-3 pt-4">
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold text-xs py-2.5 px-5 rounded-xl transition">Update Category</button>
        </div>
    </form>
</div>
@endsection