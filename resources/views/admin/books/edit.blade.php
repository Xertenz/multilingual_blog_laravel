@extends("layouts.admin.master")
@section('title', 'Edit Book Summary')

@section('content')
<div class="container mx-auto pb-8 px-4 max-w-4xl">

    <!-- Header -->
    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Edit Book Summary</h1>
            <p class="text-xs text-slate-500">Update book translations, pricing, and affiliate links.</p>
        </div>
        <a href="{{ route('admin.books.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 bg-white border border-slate-200 px-3 py-1.5 rounded-lg shadow-2xs">
            &larr; Back to Books
        </a>
    </div>

    <!-- Form Card -->
    <div class="bg-white shadow rounded-2xl border border-slate-200 overflow-hidden">
        <form action="{{ route('admin.books.update', $book->id) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-8">
            @csrf
            @method('PUT')

            <!-- Global Book Attributes -->
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">General Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4 bg-slate-50 rounded-xl border border-slate-200 items-center">

                    <div class="md:col-span-3">
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Internal Title <span class="text-red-500">*</span></label>
                        <input type="text" name="internal_title" value="{{ old('internal_title', $book->internal_title ?? '') }}" class="w-full rounded-lg border @error('internal_title') border-red-500 bg-red-50/50 @else border-slate-300 @enderror focus:border-indigo-500 text-sm py-2 px-3 outline-none transition bg-white" placeholder="e.g., atomic-habits-summary" autocomplete="off">
                        @error('internal_title')
                            <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-[11px] text-slate-400 mt-1">Unique internal reference identifier (not shown to visitors).</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Published Year</label>
                        <input type="number" name="published_year" value="{{ old('published_year', $book->published_year) }}" class="w-full rounded-lg border @error('published_year') border-red-500 bg-red-50/50 @else border-slate-300 @enderror focus:border-indigo-500 text-sm py-2 px-3 outline-none transition bg-white">
                        @error('published_year')
                            <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Pages Count</label>
                        <input type="number" name="pages_count" value="{{ old('pages_count', $book->pages_count) }}" class="w-full rounded-lg border @error('pages_count') border-red-500 bg-red-50/50 @else border-slate-300 @enderror focus:border-indigo-500 text-sm py-2 px-3 outline-none transition bg-white">
                        @error('pages_count')
                            <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Cover Image</label>
                        <div class="flex items-center gap-3">
                            @if($book->hasMedia('covers'))
                                <img src="{{ $book->getFirstMediaUrl('covers') }}" alt="Cover" class="w-8 h-10 object-cover rounded border border-slate-200">
                            @endif
                            <input type="file" name="cover_image" class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-2.5 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-200 file:text-slate-700 hover:file:bg-slate-300 cursor-pointer">
                        </div>
                        @error('cover_image')
                            <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Vertical Translations Sections -->
            @foreach($locales as $localeCode => $properties)
                @php
                    $translation = $book->translations->where('locale', $localeCode)->first();
                @endphp
                <div class="border-t border-slate-200 pt-6">
                    <!-- Section Title for Language -->
                    <div class="flex items-center space-x-2 rtl:space-x-reverse mb-4">
                        <span class="px-2.5 py-1 bg-indigo-50 text-indigo-700 text-xs font-bold rounded-md border border-indigo-100">
                            {{ strtoupper($localeCode) }}
                        </span>
                        <h2 class="text-sm font-bold text-slate-800">
                            {{ $properties['native'] }} Translation
                        </h2>
                    </div>

                    <div class="space-y-4 p-4 bg-slate-50/50 rounded-xl border border-slate-200">
                        <!-- Title & Author -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Book Title ({{ strtoupper($localeCode) }}) <span class="text-red-500">*</span></label>
                                <input type="text" name="translations[{{ $localeCode }}][title]" value="{{ old("translations.{$localeCode}.title", $translation->title ?? '') }}" class="w-full rounded-lg border @error("translations.{$localeCode}.title") border-red-500 bg-red-50/50 @else border-slate-300 @enderror focus:border-indigo-500 text-sm py-2 px-3 outline-none transition bg-white">
                                @error("translations.{$localeCode}.title")
                                    <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Author Name ({{ strtoupper($localeCode) }}) <span class="text-red-500">*</span></label>
                                <input type="text" name="translations[{{ $localeCode }}][author]" value="{{ old("translations.{$localeCode}.author", $translation->author ?? '') }}" class="w-full rounded-lg border @error("translations.{$localeCode}.author") border-red-500 bg-red-50/50 @else border-slate-300 @enderror focus:border-indigo-500 text-sm py-2 px-3 outline-none transition bg-white">
                                @error("translations.{$localeCode}.author")
                                    <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Affiliate URL & Pricing -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Affiliate URL ({{ strtoupper($localeCode) }})</label>
                                <input type="url" name="translations[{{ $localeCode }}][affiliate_url]" value="{{ old("translations.{$localeCode}.affiliate_url", $translation->affiliate_url ?? '') }}" class="w-full rounded-lg border @error("translations.{$localeCode}.affiliate_url") border-red-500 bg-red-50/50 @else border-slate-300 @enderror focus:border-indigo-500 text-sm py-2 px-3 outline-none transition bg-white">
                                @error("translations.{$localeCode}.affiliate_url")
                                    <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-600 mb-1">Price</label>
                                        <input type="number" step="0.01" name="translations[{{ $localeCode }}][price]" value="{{ old("translations.{$localeCode}.price", $translation->price ?? '') }}" class="w-full rounded-lg border @error("translations.{$localeCode}.price") border-red-500 bg-red-50/50 @else border-slate-300 @enderror focus:border-indigo-500 text-sm py-2 px-3 outline-none transition bg-white">
                                        @error("translations.{$localeCode}.price")
                                            <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-600 mb-1">Currency</label>
                                        <input type="text" name="translations[{{ $localeCode }}][currency]" value="{{ old("translations.{$localeCode}.currency", $translation->currency ?? 'USD') }}" class="w-full rounded-lg border @error("translations.{$localeCode}.currency") border-red-500 bg-red-50/50 @else border-slate-300 @enderror focus:border-indigo-500 text-sm py-2 px-3 outline-none transition bg-white">
                                        @error("translations.{$localeCode}.currency")
                                            <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Slug -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Slug ({{ strtoupper($localeCode) }})</label>
                            <input type="text" name="translations[{{ $localeCode }}][slug]" value="{{ old("translations.{$localeCode}.slug", $translation->slug ?? '') }}" class="w-full rounded-lg border @error("translations.{$localeCode}.slug") border-red-500 bg-red-50/50 @else border-slate-300 @enderror focus:border-indigo-500 text-sm py-2 px-3 outline-none transition bg-white">
                            @error("translations.{$localeCode}.slug")
                                <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Summary -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Book Summary / Review ({{ strtoupper($localeCode) }})</label>
                            <textarea name="translations[{{ $localeCode }}][summary]" rows="5" class="w-full rounded-lg border @error("translations.{$localeCode}.summary") border-red-500 bg-red-50/50 @else border-slate-300 @enderror focus:border-indigo-500 text-sm p-3 outline-none transition bg-white">{{ old("translations.{$localeCode}.summary", $translation->summary ?? '') }}</textarea>
                            @error("translations.{$localeCode}.summary")
                                <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Actions -->
            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-200">
                <a href="{{ route('admin.books.index') }}" class="bg-white text-slate-700 font-semibold px-4 py-2 rounded-lg border border-slate-300 hover:bg-slate-50 transition text-sm">
                    Cancel
                </a>
                <button type="submit" class="bg-indigo-600 text-white font-semibold px-5 py-2 rounded-lg hover:bg-indigo-700 transition text-sm shadow-2xs">
                    Update Book &rarr;
                </button>
            </div>

        </form>
    </div>
</div>
@endsection