@extends("layouts.admin.master")

@section('title', 'Create New Media Summary')

@section('content')
<div class="container mx-auto pb-8 px-4 max-w-2xl">
    
    <!-- Header -->
    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Create New Media Summary</h1>
            <p class="text-xs text-slate-500">Fill out the main media information (Featured Image, Type, Rating, Location)</p>
        </div>
        <a href="{{ route('admin.media-summaries.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 bg-white border border-slate-200 px-3 py-1.5 rounded-lg shadow-2xs">
            &larr; Back to Media Summaries
        </a>
    </div>
    
    <!-- Form Card -->
    <div class="bg-white shadow rounded-2xl border border-slate-200 overflow-hidden">
        <div class="p-6">
            <form action="{{ route('admin.media-summaries.store') }}" method="POST" enctype="multipart/form-data" id="mediaForm" class="space-y-4">
                @csrf
                
                <!-- Media Type (Movie, Series, Documentary, Podcast...) -->
                <div class="bg-white rounded-xl p-4 border border-slate-200">
                    <label for="type" class="block text-xs font-semibold text-slate-600 mb-1.5">
                        Media Type (نوع العمل) <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="type" 
                           id="type" 
                           value="{{ old('type', 'documentary') }}" 
                           class="w-full rounded-lg border border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2 px-3 outline-none transition @error('type') border-red-500 bg-red-50/50 @enderror" 
                           placeholder="e.g. movie, series, documentary, podcast" 
                           required
                           autocomplete="off">
                    @error('type')
                        <div class="text-red-500 text-xs mt-1 flex items-center gap-1">
                            {{ $message }}
                        </div>
                    @enderror
                    <p class="text-[11px] text-slate-400 mt-1">Enter media category (e.g., documentary, movie, series, podcast).</p>
                </div>

                <!-- Release Year & IMDb Rating Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Release Year -->
                    <div class="bg-white rounded-xl p-4 border border-slate-200">
                        <label for="release_year" class="block text-xs font-semibold text-slate-600 mb-1.5">
                            Release Year (سنة الإصدار)
                        </label>
                        <input type="number" 
                               name="release_year" 
                               id="release_year" 
                               value="{{ old('release_year') }}" 
                               min="1900" 
                               max="2100"
                               class="w-full rounded-lg border border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2 px-3 outline-none transition @error('release_year') border-red-500 bg-red-50/50 @enderror" 
                               placeholder="e.g. 2023">
                        @error('release_year')
                            <div class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- IMDb Rating -->
                    <div class="bg-white rounded-xl p-4 border border-slate-200">
                        <label for="imdb_rating" class="block text-xs font-semibold text-slate-600 mb-1.5">
                            IMDb Rating (تقييم IMDb)
                        </label>
                        <input type="number" 
                               step="0.1" 
                               name="imdb_rating" 
                               id="imdb_rating" 
                               value="{{ old('imdb_rating') }}" 
                               min="0" 
                               max="10"
                               class="w-full rounded-lg border border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2 px-3 outline-none transition @error('imdb_rating') border-red-500 bg-red-50/50 @enderror" 
                               placeholder="e.g. 8.5">
                        @error('imdb_rating')
                            <div class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <!-- Crime Location Selection -->
                <div class="bg-white rounded-xl p-4 border border-slate-200">
                    <label for="article_location_id" class="block text-xs font-semibold text-slate-600 mb-1.5">
                        Linked Crime Location (ربط بموقع جغرافي - اختياري)
                    </label>
                    <div class="relative">
                        <select name="article_location_id" id="article_location_id" 
                                class="w-full rounded-lg border border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2 px-3 pr-10 outline-none transition bg-white cursor-pointer @error('article_location_id') border-red-500 bg-red-50/50 @enderror">
                            <option value="" selected>-- None (بدون موقع) --</option>
                            @foreach ($locations as $location)
                                <option value="{{ $location->id }}" {{ old('article_location_id') == $location->id ? 'selected' : '' }}>
                                    {{ $location->translations->first()->name ?? 'Location #'.$location->id }}
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                    @error('article_location_id')
                        <div class="text-red-500 text-xs mt-1 flex items-center gap-1">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Publication Status Section -->
                <div class="bg-white rounded-xl p-4 border border-slate-200">
                    <label for="status" class="block text-xs font-semibold text-slate-600 mb-1.5">Publication Status (حالة النشر)</label>
                    <div class="relative">
                        <select name="status" id="status" 
                                class="w-full rounded-lg border border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2 px-3 pr-10 outline-none transition bg-white cursor-pointer">
                            <option value="published" {{ old('status', 'published') == 'published' ? 'selected' : '' }}>Published (منشور)</option>
                            <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft (مسودة)</option>
                            <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending (قيد المراجعة)</option>
                        </select>
                    </div>
                    @error('status')
                        <div class="text-red-500 text-xs mt-1 flex items-center gap-1">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Featured Image Upload (Required) -->
                <div class="bg-white rounded-xl p-4 border border-slate-200">
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                        Featured Image (بوستر العمل) <span class="text-red-500">*</span>
                    </label>
                    <div class="border-2 border-dashed border-slate-300 rounded-xl p-4 text-center hover:border-indigo-500 transition cursor-pointer bg-slate-50" id="uploadArea">
                        <input type="file" name="featured_img" id="featured_img" class="hidden" accept="image/*" required>
                        <svg class="w-7 h-7 mx-auto text-slate-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <p class="text-slate-600 text-xs font-medium">Click to upload featured image or drag & drop</p>
                        <p class="text-slate-400 text-[11px] mt-0.5">PNG, JPG, WebP up to 5MB</p>
                        <img id="imagePreview" class="max-w-[140px] max-h-[160px] mx-auto mt-2 rounded-lg shadow-2xs hidden object-cover" src="#" alt="Preview">
                    </div>
                    @error('featured_img')
                        <div class="text-red-500 text-xs mt-1 flex items-center gap-1">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('admin.media-summaries.index') }}" class="bg-white text-slate-700 font-semibold px-4 py-2 rounded-lg border border-slate-300 hover:bg-slate-50 transition text-sm">
                        Cancel
                    </a>
                    <button type="submit" class="bg-indigo-600 text-white font-semibold px-5 py-2 rounded-lg hover:bg-indigo-700 transition text-sm shadow-2xs">
                        Save Media & Continue &rarr;
                    </button>
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
        
        if(uploadArea && fileInput) {
            uploadArea.addEventListener('click', () => fileInput.click());
            
            uploadArea.addEventListener('dragover', (e) => {
                e.preventDefault();
                uploadArea.classList.add('border-indigo-500', 'bg-indigo-50/50');
            });
            
            uploadArea.addEventListener('dragleave', () => {
                uploadArea.classList.remove('border-indigo-500', 'bg-indigo-50/50');
            });
            
            uploadArea.addEventListener('drop', (e) => {
                e.preventDefault();
                uploadArea.classList.remove('border-indigo-500', 'bg-indigo-50/50');
                if (e.dataTransfer.files.length > 0) {
                    fileInput.files = e.dataTransfer.files;
                    handleImagePreview(e.dataTransfer.files[0]);
                }
            });
            
            fileInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    handleImagePreview(this.files[0]);
                }
            });
        }
        
        function handleImagePreview(file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.src = e.target.result;
                imagePreview.classList.remove('hidden');
                const uploadText = uploadArea.querySelector('p');
                if (uploadText) uploadText.textContent = file.name;
            }
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush