@extends('layouts.admin.master')
@section('title', 'Edit Media Summary')

@section('content')
<div class="container mx-auto pb-8 px-4 max-w-2xl">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Edit Media Summary</h1>
            <p class="text-xs text-slate-500">ID: {{ $mediaSummary->id }}</p>
        </div>
        <a href="{{ route('admin.media-summaries.index') }}" class="text-xs font-semibold text-slate-600 bg-white border border-slate-200 px-3 py-1.5 rounded-lg shadow-2xs">&larr; Back</a>
    </div>

    <div class="bg-white shadow rounded-2xl border border-slate-200 overflow-hidden">
        <div class="p-6">
            <form action="{{ route('admin.media-summaries.update', $mediaSummary->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf @method('PUT')

                <div class="bg-white rounded-xl p-4 border border-slate-200">
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Label <span class="text-red-500">*</span></label>
                    <input type="text" name="label" value="{{ old('label', $mediaSummary->label) }}"
                           class="w-full rounded-lg border border-slate-300 text-sm py-2 px-3 @error('label') border-red-500 @enderror">
                    @error('label') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-white rounded-xl p-4 border border-slate-200">
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                            Category <span class="text-red-500">*</span>
                        </label>
                        <select name="media_summary_category_id"
                                class="w-full rounded-lg border border-slate-300 text-sm py-2 px-3 bg-white @error('media_summary_category_id') border-red-500 @enderror">
                            <option value="">-- Select Category --</option>
                            @foreach($categories as $category)
                                @php
                                    $catName = $category->translations->firstWhere('locale', app()->getLocale())?->name
                                        ?? $category->translations->first()?->name
                                        ?? $category->label;
                                @endphp
                                <option value="{{ $category->id }}" {{ old('media_summary_category_id', $mediaSummary->media_summary_category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $catName }}
                                </option>
                            @endforeach
                        </select>
                        @error('media_summary_category_id') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="bg-white rounded-xl p-4 border border-slate-200">
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Location</label>
                        <select name="article_location_id" class="w-full rounded-lg border border-slate-300 text-sm py-2 px-3 bg-white">
                            <option value="">-- None --</option>
                            @foreach($locations as $loc)
                                @php $locName = $loc->translations->firstWhere('locale', app()->getLocale())?->title ?? $loc->translations->first()?->title ?? 'Location #'.$loc->id; @endphp
                                <option value="{{ $loc->id }}" {{ old('article_location_id', $mediaSummary->article_location_id) == $loc->id ? 'selected' : '' }}>{{ $locName }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-white rounded-xl p-4 border border-slate-200">
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Release Year</label>
                        <input type="number" name="release_year" value="{{ old('release_year', $mediaSummary->release_year) }}" min="1880" max="2100"
                               class="w-full rounded-lg border border-slate-300 text-sm py-2 px-3">
                    </div>
                    <div class="bg-white rounded-xl p-4 border border-slate-200">
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">IMDb Rating</label>
                        <input type="number" step="0.1" name="imdb_rating" value="{{ old('imdb_rating', $mediaSummary->imdb_rating) }}" min="0" max="10"
                               class="w-full rounded-lg border border-slate-300 text-sm py-2 px-3">
                    </div>
                </div>

                <div class="bg-white rounded-xl p-4 border border-slate-200">
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Tags</label>
                    <select name="tags[]" multiple class="w-full rounded-lg border border-slate-300 text-sm p-2 h-24">
                        @foreach($tags as $tag)
                            @php $tagName = $tag->translations->firstWhere('locale', app()->getLocale())?->name ?? $tag->translations->first()?->name ?? $tag->label; @endphp
                            <option value="{{ $tag->id }}"
                                {{ is_array(old('tags')) && in_array($tag->id, old('tags')) ? 'selected' : '' }}
                                {{ !is_array(old('tags')) && in_array($tag->id, $mediaSummary->tags->pluck('id')->toArray()) ? 'selected' : '' }}>
                                {{ $tagName }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="bg-white rounded-xl p-4 border border-slate-200">
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Publication Status</label>
                    @can('publish-articles')
                        @php $statusVal = old('status', $mediaSummary->status?->value ?? $mediaSummary->status); @endphp
                        <select name="status" class="w-full rounded-lg border border-slate-300 text-sm py-2 px-3 bg-white">
                            <option value="draft" {{ $statusVal === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="pending" {{ $statusVal === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="published" {{ $statusVal === 'published' ? 'selected' : '' }}>Published</option>
                        </select>
                    @else
                        <input type="hidden" name="status" value="pending">
                        <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 text-xs text-amber-800">
                            Status: Pending Review on update.
                        </div>
                    @endcan
                </div>

                <div class="bg-white rounded-xl p-4 border border-slate-200">
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Featured Image (Optional)</label>
                    @if($mediaSummary->hasMedia('featured_imgs'))
                        <div class="mb-2 flex items-center gap-3">
                            <img src="{{ $mediaSummary->getFirstMediaUrl('featured_imgs', 'thumb') }}" class="w-14 h-14 object-cover rounded-lg border">
                            <span class="text-xs text-slate-500">Current Cover</span>
                        </div>
                    @endif
                    <div class="border-2 border-dashed border-slate-300 rounded-xl p-4 text-center hover:border-indigo-500 transition cursor-pointer bg-slate-50" id="uploadArea">
                        <input type="file" name="featured_img" id="featured_img" class="hidden" accept="image/*">
                        <p class="text-slate-600 text-xs font-medium">Click to upload new image or drag & drop</p>
                        <img id="imagePreview" class="max-w-[140px] max-h-[100px] mx-auto mt-2 rounded-lg hidden" src="#" alt="Preview">
                    </div>
                    @error('featured_img') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('admin.media-summaries.index') }}" class="bg-white text-slate-700 font-semibold px-4 py-2 rounded-lg border border-slate-300 hover:bg-slate-50 text-sm">Cancel</a>
                    <button type="submit" class="bg-indigo-600 text-white font-semibold px-5 py-2 rounded-lg hover:bg-indigo-700 text-sm">Update Media Summary &rarr;</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('featured_img');
        const imagePreview = document.getElementById('imagePreview');
        if (uploadArea && fileInput) {
            uploadArea.addEventListener('click', () => fileInput.click());
            fileInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.src = e.target.result;
                        imagePreview.classList.remove('hidden');
                    };
                    reader.readAsDataURL(this.files[0]);
                }
            });
        }



        // Show Sweet Alert Toast
        if (typeof window.showToast === 'function') {
            @if(session('error'))
                window.showToast({
                    icon: 'error',
                    title: 'Error',
                    text: @json(session('error')),
                });
            @elseif($errors->any())
                window.showToast({
                    icon: 'error',
                    title: 'Validation Error',
                    text: @json($errors->first()),
                });
            @endif
        }
    });
</script>
@endpush