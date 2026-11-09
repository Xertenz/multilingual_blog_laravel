@extends("layouts.admin.master")
@section('title', 'Create Game Translation')

@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/components/article-trans-add-edit.css'])
@endpush

@section('content')
<div class="container mx-auto pb-8 px-4 max-w-3xl">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Add Game Translation</h1>
            <p class="text-xs text-slate-500">Create a localized review & review content for this game</p>
        </div>
        <a href="{{ route('admin.games.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 bg-white border border-slate-200 px-3 py-1.5 rounded-lg shadow-2xs">
            &larr; Back to Games
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow overflow-hidden">
        <div class="p-6">
            <form action="{{ route('admin.games-trans.store') }}" method="POST">
                @csrf
                <input type="hidden" name="game_id" value="{{ $game_id }}" />

                <!-- Language & Slug Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="locale" class="block text-xs font-semibold text-slate-600 mb-1">Language <span class="text-red-500">*</span></label>
                        <select name="locale" id="locale" class="w-full rounded-lg border border-slate-300 text-sm p-2.5">
                            <option value="en">🇬🇧 English</option>
                            <option value="ar">🇸🇦 Arabic</option>
                            <option value="de">🇩🇪 German</option>
                            <option value="es">🇪🇸 Spanish</option>
                            <option value="fr">🇫🇷 French</option>
                            <option value="ru">🇷🇺 Russian</option>
                            <option value="pt">🇵🇹 Portuguese</option>
                            <option value="nl">🇳🇱 Dutch</option>
                            <option value="it">🇮🇹 Italian</option>
                            <option value="sv">🇸🇪 Swedish</option>
                            <option value="da">🇩🇰 Danish</option>
                            <option value="nb">🇳🇴 Norwegian (Bokmål)</option>
                        </select>
                        @error('locale') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label for="slug" class="block text-xs font-semibold text-slate-600 mb-1">Slug <span class="text-red-500">*</span></label>
                        <input type="text" name="slug" value="{{ old('slug') }}" class="w-full rounded-lg border border-slate-300 text-sm p-2.5" placeholder="game-review-slug">
                        @error('slug') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>

                <!-- Price & Affiliate -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4 border border-slate-200 p-4 rounded-xl bg-slate-50">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Store / Affiliate URL</label>
                        <input type="url" name="affiliate_url" value="{{ old('affiliate_url') }}" class="w-full rounded-lg border border-slate-300 text-sm p-2" placeholder="https://store.steampowered.com/...">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Price</label>
                        <input type="number" step="0.01" name="price" value="{{ old('price') }}" class="w-full rounded-lg border border-slate-300 text-sm p-2">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Currency</label>
                        <input type="text" name="currency" value="{{ old('currency', 'USD') }}" class="w-full rounded-lg border border-slate-300 text-sm p-2">
                    </div>
                </div>

                <!-- SEO Meta Data -->
                <div class="border border-slate-200 p-4 rounded-xl bg-slate-50 mb-4">
                    <h3 class="text-xs font-bold text-slate-700 uppercase mb-3">SEO Meta Data</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Meta Title</label>
                            <input type="text" name="meta_title" value="{{ old('meta_title') }}" class="w-full rounded-lg border border-slate-300 text-sm p-2">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Meta Description</label>
                            <textarea name="meta_description" rows="2" class="w-full rounded-lg border border-slate-300 text-sm p-2 resize-none">{{ old('meta_description') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Content & Title Section with TinyMCE -->
                <div class="border border-slate-200 p-4 rounded-xl bg-white mb-4">
                    <div class="mb-4">
                        <label for="title" class="block text-xs font-semibold text-slate-600 mb-1">Game Article Title <span class="text-red-500">*</span></label>
                        <input type="text" name="title" value="{{ old('title') }}" class="w-full rounded-lg border border-slate-300 text-sm p-2.5">
                        @error('title') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-xs font-semibold text-slate-600 mb-1">Rich Review Content <span class="text-red-500">*</span></label>
                        <textarea name="content" id="description" rows="10" class="w-full rounded-lg border border-slate-300 text-sm p-2.5">{{ old('content') }}</textarea>
                        @error('content') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('admin.games.index') }}" class="bg-white text-slate-700 font-semibold px-4 py-2 rounded-lg border border-slate-300 text-sm">Cancel</a>
                    <button type="submit" class="bg-indigo-600 text-white font-semibold px-5 py-2 rounded-lg text-sm">Save Translation &rarr;</button>
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
            // جلب الصور المرتبطة باللعبة الأساسية عبر Spatie Media Library
            foreach($game->getMedia('content_imgs') as $img) {
                $game_content_imgs_info[$i]['id'] = $img->id;
                $game_content_imgs_info[$i]['src'] = $img->getUrl();
                $i++;
            }
        @endphp
        const game_content_imgs_info = @json($game_content_imgs_info);
    </script>
    {{-- ربط ملف جافاسكريبت محرر TinyMCE الخاص بالألعاب --}}
    @vite(['resources/js/components/game-trans-add-edit.js'])
@endpush