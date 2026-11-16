@extends("layouts.admin.master")
@section('title', 'Edit Game Translation')

@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/components/article-trans-add-edit.css'])
@endpush

@section('content')
@php
    $supportedLocales = \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getSupportedLocales();
    $existingLocales = $game->translations->pluck('locale')->toArray();
@endphp

<div class="container mx-auto pb-8 px-4 max-w-3xl">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Edit Game Translation</h1>
            <p class="text-xs text-slate-500">Update localized review content for this game (ID: {{ $game_trans->id }})</p>
        </div>
        <a href="{{ route('admin.games.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 bg-white border border-slate-200 px-3 py-1.5 rounded-lg shadow-2xs">
            &larr; Back to Games
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow overflow-hidden">
        <div class="p-6">
            <form action="{{ route('admin.games-trans.update', $game_trans->id) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="game_id" value="{{ $game_trans->game_id }}" />

                <!-- Language & Slug Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="locale" class="block text-xs font-semibold text-slate-600 mb-1">
                            Language <span class="text-red-500">*</span>
                        </label>
                        <select name="locale" id="locale" class="w-full rounded-lg border border-slate-300 text-sm p-2.5">
                            @foreach ($supportedLocales as $code => $props)
                                @php
                                    $isTaken = in_array($code, $existingLocales) && $code !== $game_trans->locale;
                                @endphp
                                <option value="{{ $code }}"
                                        {{ old('locale', $game_trans->locale) === $code ? 'selected' : '' }}
                                        {{ $isTaken ? 'disabled' : '' }}>
                                    {{ $props['native'] }}@if($isTaken) — (already exists) @endif
                                </option>
                            @endforeach
                        </select>
                        @error('locale') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label for="slug" class="block text-xs font-semibold text-slate-600 mb-1">
                            Slug <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="slug" value="{{ old('slug', $game_trans->slug) }}"
                               class="w-full rounded-lg border border-slate-300 text-sm p-2.5">
                        @error('slug') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>

                {{-- Publication Status --}}
                <div class="border border-slate-200 p-4 rounded-xl bg-white mb-4">
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Translation Status</label>
                    @can('publish-articles')
                        @php $currentStatus = old('status', $game_trans->status); @endphp
                        <select name="status" class="w-full rounded-lg border border-slate-300 text-sm p-2.5">
                            <option value="draft" {{ $currentStatus === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="pending" {{ $currentStatus === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="published" {{ $currentStatus === 'published' ? 'selected' : '' }}>Published</option>
                        </select>
                    @else
                        <input type="hidden" name="status" value="pending">
                        <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 text-xs text-amber-800">
                            Status: {{ ucfirst($game_trans->status) }} — updating will re-submit for review.
                        </div>
                    @endcan
                </div>

                <!-- Price & Affiliate -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4 border border-slate-200 p-4 rounded-xl bg-slate-50">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Store / Affiliate URL</label>
                        <input type="url" name="affiliate_url" value="{{ old('affiliate_url', $game_trans->affiliate_url) }}" class="w-full rounded-lg border border-slate-300 text-sm p-2">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Price</label>
                        <input type="number" step="0.01" name="price" value="{{ old('price', $game_trans->price) }}" class="w-full rounded-lg border border-slate-300 text-sm p-2">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Currency</label>
                        <input type="text" name="currency" value="{{ old('currency', $game_trans->currency ?? 'USD') }}" class="w-full rounded-lg border border-slate-300 text-sm p-2">
                    </div>
                </div>

                <!-- SEO Meta Data -->
                <div class="border border-slate-200 p-4 rounded-xl bg-slate-50 mb-4">
                    <h3 class="text-xs font-bold text-slate-700 uppercase mb-3">SEO Meta Data</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Meta Title</label>
                            <input type="text" name="meta_title" value="{{ old('meta_title', $game_trans->meta_title) }}" class="w-full rounded-lg border border-slate-300 text-sm p-2">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Meta Description</label>
                            <textarea name="meta_description" rows="2" class="w-full rounded-lg border border-slate-300 text-sm p-2 resize-none">{{ old('meta_description', $game_trans->meta_description) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Content & Title Section with TinyMCE -->
                <div class="border border-slate-200 p-4 rounded-xl bg-white mb-4">
                    <div class="mb-4">
                        <label for="title" class="block text-xs font-semibold text-slate-600 mb-1">Game Article Title <span class="text-red-500">*</span></label>
                        <input type="text" name="title" value="{{ old('title', $game_trans->title) }}" class="w-full rounded-lg border border-slate-300 text-sm p-2.5">
                        @error('title') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-xs font-semibold text-slate-600 mb-1">Rich Review Content <span class="text-red-500">*</span></label>
                        <textarea name="content" id="description" rows="10" class="w-full rounded-lg border border-slate-300 text-sm p-2.5">{{ old('content', $game_trans->content) }}</textarea>
                        @error('content') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('admin.games.index') }}" class="bg-white text-slate-700 font-semibold px-4 py-2 rounded-lg border border-slate-300 text-sm">Cancel</a>
                    <button type="submit" class="bg-indigo-600 text-white font-semibold px-5 py-2 rounded-lg text-sm">Update Translation &rarr;</button>
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
            $game_content_imgs_info = [];
            $i = 0;
            foreach($game_content_imgs as $img) {
                $game_content_imgs_info[$i]['id'] = $img->id;
                $game_content_imgs_info[$i]['src'] = $img->getUrl();
                $i++;
            }
        @endphp
        const game_content_imgs_info = @json($game_content_imgs_info);
    </script>
    @vite(['resources/js/components/game-trans-add-edit.js'])
@endpush