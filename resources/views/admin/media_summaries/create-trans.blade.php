@extends("layouts.admin.master")
@section('title', 'Create Media Summary Translation')

@push('styles')
    @vite(['resources/css/components/article-trans-add-edit.css'])
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        .form-card { background: #fff; box-shadow: 0 4px 6px -1px rgba(0,0,0,.05); border-radius: 16px; border: 1px solid #e2e8f0; overflow: hidden; }
        .input-field { transition: all .2s; background: #fff; border: 1px solid #cbd5e1; border-radius: 10px; padding: .6rem .875rem; width: 100%; outline: none; font-size: .875rem; color: #1e293b; }
        select.input-field { appearance: none; }
        .input-field:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,.12); }
        .input-field.error { border-color: #ef4444; background-color: #fef2f2; }
        .input-label { display: block; font-size: .8125rem; font-weight: 600; color: #475569; margin-bottom: .35rem; }
        .input-label .required { color: #ef4444; margin-left: .15rem; }
        .error-message { color: #ef4444; font-size: .75rem; margin-top: .25rem; }
        .btn-primary { background: #4f46e5; color: #fff; font-weight: 600; padding: .6rem 1.5rem; border-radius: 10px; border: none; cursor: pointer; font-size: .875rem; }
        .btn-primary:hover { background: #4338ca; }
        .btn-secondary { background: #fff; color: #475569; font-weight: 600; padding: .6rem 1.5rem; border-radius: 10px; border: 1px solid #cbd5e1; font-size: .875rem; }
        .section-card { background: #fff; border-radius: 12px; padding: 1.25rem; border: 1px solid #e2e8f0; margin-bottom: 1rem; }
        .section-title { display: flex; align-items: center; gap: .5rem; font-size: .95rem; font-weight: 600; color: #1e293b; margin-bottom: .75rem; }
        .section-icon { width: 1.25rem; height: 1.25rem; color: #6366f1; }
        .alert-error { background-color: #fef2f2; border-left: 4px solid #ef4444; border-radius: 8px; padding: .875rem; margin-bottom: 1rem; color: #991b1b; font-size: .875rem; }
    </style>
@endpush

@section('content')
@php
    $supportedLocales = \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getSupportedLocales();
    $existingLocales = $mediaSummary->translations->pluck('locale')->toArray();
@endphp

<div class="container mx-auto pb-8 px-4 max-w-3xl">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Add Media Summary Translation</h1>
            <p class="text-xs text-slate-500">For: <span class="font-semibold text-slate-700">{{ $mediaSummary->label }}</span></p>
        </div>
        <a href="{{ route('admin.media-summaries.index') }}" class="text-xs font-semibold text-slate-600 bg-white border border-slate-200 px-3 py-1.5 rounded-lg">&larr; Back</a>
    </div>

    <div class="form-card">
        <div class="p-6">
            <form action="{{ route('admin.media-summaries-trans.store') }}" method="POST">
                @csrf

                @error('language_exists')
                    <div class="alert-error"><span>{{ $message }}</span></div>
                @enderror

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="locale" class="input-label">Language <span class="required">*</span></label>
                        <div class="relative">
                            <select name="locale" id="locale" class="input-field cursor-pointer pr-10 @error('locale') error @enderror">
                                <option value="">— Select a language —</option>
                                @foreach ($supportedLocales as $code => $props)
                                    @php $isTaken = in_array($code, $existingLocales); @endphp
                                    <option value="{{ $code }}" {{ old('locale', $selectedLocale ?? '') === $code ? 'selected' : '' }} {{ $isTaken ? 'disabled' : '' }}>
                                        {{ $props['native'] }}@if($isTaken) — (exists) @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('locale') <div class="error-message">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label for="slug" class="input-label">Slug <span class="required">*</span></label>
                        <input type="text" name="slug" value="{{ old('slug') }}" class="input-field @error('slug') error @enderror" placeholder="my-media-slug">
                        @error('slug') <div class="error-message">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="section-card">
                    <div class="section-title">
                        <svg class="section-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Publication Status</span>
                    </div>
                    @can('publish-articles')
                        <select name="status" class="input-field cursor-pointer">
                            <option value="draft" {{ old('status', 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="pending" {{ old('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Published</option>
                        </select>
                    @else
                        <input type="hidden" name="status" value="pending">
                        <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 text-xs text-amber-800">Pending Review on submit.</div>
                    @endcan
                </div>

                <div class="section-card">
                    <div class="section-title">
                        <svg class="section-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <span>SEO Meta Data</span>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <div>
                            <label class="input-label">Meta Title <span class="required">*</span></label>
                            <input type="text" name="meta_title" value="{{ old('meta_title') }}" class="input-field @error('meta_title') error @enderror">
                            @error('meta_title') <div class="error-message">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label class="input-label">Meta Description <span class="required">*</span></label>
                            <textarea name="meta_description" rows="3" class="input-field resize-none @error('meta_description') error @enderror">{{ old('meta_description') }}</textarea>
                            @error('meta_description') <div class="error-message">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <div class="section-card">
                    <div class="section-title">
                        <svg class="section-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Content</span>
                    </div>
                    <div class="mb-3">
                        <label class="input-label">Title <span class="required">*</span></label>
                        <input type="text" name="title" value="{{ old('title') }}" class="input-field @error('title') error @enderror">
                        @error('title') <div class="error-message">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="input-label">Synopsis</label>
                        <textarea name="synopsis" rows="4" class="input-field resize-y">{{ old('synopsis') }}</textarea>
                    </div>
                    <div>
                        <label class="input-label">Review & Analysis</label>
                        <textarea name="review_analysis" id="review_analysis" rows="8" class="input-field resize-y">{{ old('review_analysis') }}</textarea>
                    </div>
                </div>

                <input type="hidden" name="media_summary_id" value="{{ $mediaSummary->id }}" />

                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('admin.media-summaries.index') }}" class="btn-secondary">Cancel</a>
                    <button type="submit" class="btn-primary">Create Translation &rarr;</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/tinymce/tinymce.min.js') }}"></script>
    <script>
        @php
            $article_content_imgs_info = [];
            $i = 0;
            foreach ($article_content_imgs as $img) {
                $article_content_imgs_info[$i]['id'] = $img->id;
                $article_content_imgs_info[$i]['src'] = $img->original_url;
                $i++;
            }
        @endphp
        const article_content_imgs_info = @json($article_content_imgs_info);
    </script>
    @vite(['resources/js/components/article-trans-add-edit.js'])
@endpush