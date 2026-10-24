@extends("layouts.admin.master")
@section('title', 'Create New Article')

@section('content')
<div class="container mx-auto pb-8 px-4 max-w-2xl">
    
    <!-- Compact Header -->
    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Create New Article</h1>
            <p class="text-xs text-slate-500">Fill out the basic information to start your article</p>
        </div>
        <a href="{{ route('admin.articles.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 bg-white border border-slate-200 px-3 py-1.5 rounded-lg shadow-2xs">
            &larr; Back to Articles
        </a>
    </div>
    
    <!-- Form Card -->
    <div class="bg-white shadow rounded-2xl border border-slate-200 overflow-hidden">
        
        <!-- Form Body -->
        <div class="p-6">
            <form action="{{ route('admin.articles.store') }}" method="POST" enctype="multipart/form-data" id="articleForm" class="space-y-4">
                @csrf
                
                <!-- Category Selection -->
                <div class="bg-white rounded-xl p-4 border border-slate-200">
                    <label for="category" class="block text-xs font-semibold text-slate-600 mb-1.5">
                        Choose Category <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <select name="category_id" id="category" 
                                class="w-full rounded-lg border border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2 px-3 pr-10 outline-none transition bg-white cursor-pointer @error('category_id') border-red-500 bg-red-50/50 @enderror">
                            <option value="" disabled selected>-- Select a category --</option>
                            @foreach ($cats as $cat)
                                <option value="{{ $cat['id'] }}" {{ old('category_id') == $cat['id'] ? 'selected' : '' }}>
                                    {{ $cat['label'] }}
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                    @error('category_id')
                        <div class="text-red-500 text-xs mt-1 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Article Internal Title Section -->
                <div class="bg-white rounded-xl p-4 border border-slate-200">
                    <label for="internal_title" class="block text-xs font-semibold text-slate-600 mb-1.5">
                        Internal Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="internal_title" 
                           id="internal_title" 
                           value="{{ old('internal_title') }}" 
                           class="w-full rounded-lg border border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2 px-3 outline-none transition @error('internal_title') border-red-500 bg-red-50/50 @enderror" 
                           placeholder="e.g., roswell-mystery" 
                           autocomplete="off">
                    @error('internal_title')
                        <div class="text-red-500 text-xs mt-1 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </div>
                    @enderror
                    <p class="text-[11px] text-slate-400 mt-1">Unique internal reference identifier (not shown to visitors).</p>
                </div>

                {{--
                <!-- Status Switcher Section -->
                <div class="bg-white rounded-xl p-4 border border-slate-200">
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Publication Status</label>
                    <div class="flex items-center justify-between bg-slate-50 p-3 rounded-xl border border-slate-200">
                        <div class="flex items-center space-x-3">
                            <input type="hidden" name="status" value="draft">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" 
                                       name="status" 
                                       value="published" 
                                       class="sr-only peer status-checkbox"
                                       {{ old('status', 'published') == 'published' ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                <span class="ml-3 text-xs font-semibold text-slate-700 status-label min-w-[60px]">
                                    {{ old('status', 'published') == 'published' ? 'Published' : 'Draft' }}
                                </span>
                            </label>
                        </div>
                        <span class="text-xs text-slate-500 status-description">
                            {{ old('status', 'published') == 'published' ? 'Visible to public immediately' : 'Saved as draft' }}
                        </span>
                    </div>
                    @error('status')
                        <div class="text-red-500 text-xs mt-1 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                --}}







                <!-- Publication Status Section -->
<div class="bg-white rounded-xl p-4 border border-slate-200">
    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Publication Status</label>
    
    @can('publish-articles')
        {{-- إذا كان المستخدم مشرفاً أو محرراً، يمكنه التحكم بالحالة بشكل كامل --}}
        <div class="relative">
            <select name="status" id="status" 
                    class="w-full rounded-lg border border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2 px-3 pr-10 outline-none transition bg-white cursor-pointer">
                <option value="draft" {{ old('status', $article->status ?? 'draft') == 'draft' ? 'selected' : '' }}>Draft (مسودة)</option>
                <option value="pending" {{ old('status', $article->status ?? 'pending') == 'pending' ? 'selected' : '' }}>Pending (قيد Mراجعة)</option>
                <option value="published" {{ old('status', $article->status ?? '') == 'published' ? 'selected' : '' }}>Published (منشور)</option>
            </select>
        </div>
    @else
        {{-- إذا كان المستخدم كاتباً (Writer)، يتم إخباره بأن المقال سيُرسل للمراجعة تلقائياً --}}
        <div class="flex items-center justify-between bg-amber-50 p-3 rounded-xl border border-amber-200">
            <input type="hidden" name="status" value="pending">
            <div class="flex items-center space-x-2">
                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <span class="text-xs font-semibold text-amber-800">Status: Pending Review (قيد المراجعة)</span>
            </div>
            <span class="text-[11px] text-amber-600">Your article will be reviewed by an editor before publishing.</span>
        </div>
    @endcan
</div>













                <!-- Featured Image Upload (Required) -->
                <div class="bg-white rounded-xl p-4 border border-slate-200">
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                        Featured Image <span class="text-red-500">*</span>
                    </label>
                    <div class="border-2 border-dashed border-slate-300 rounded-xl p-4 text-center hover:border-indigo-500 transition cursor-pointer bg-slate-50" id="uploadArea">
                        <input type="file" name="featured_img" id="featured_img" class="hidden" accept="image/*">
                        <svg class="w-7 h-7 mx-auto text-slate-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <p class="text-slate-600 text-xs font-medium">Click to upload or drag & drop</p>
                        <p class="text-slate-400 text-[11px] mt-0.5">PNG, JPG, WebP up to 5MB</p>
                        <img id="imagePreview" class="max-w-[140px] max-h-[100px] mx-auto mt-2 rounded-lg shadow-2xs hidden" src="#" alt="Preview">
                    </div>
                    @error('featured_img')
                        <div class="text-red-500 text-xs mt-1 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Article Tags -->
                <div class="bg-white rounded-xl p-4 border border-slate-200">
                    <label for="tags" class="block text-xs font-semibold text-slate-600 mb-1.5">
                        Article Tags <span class="text-slate-400 font-normal">(Optional)</span>
                    </label>
                    <select name="tags[]" id="tags" multiple
                            class="w-full rounded-lg border border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm p-2 outline-none transition h-24 @error('tags') border-red-500 bg-red-50/50 @enderror">
                        @foreach ($tags as $tag)
                            <option value="{{ $tag['id'] }}" {{ is_array(old('tags')) && in_array($tag['id'], old('tags')) ? 'selected' : '' }}>
                                {{ $tag['label'] }}
                            </option>
                        @endforeach
                    </select>
                    @error('tags')
                        <div class="text-red-500 text-xs mt-1 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </div>
                    @enderror
                    <p class="text-[11px] text-slate-400 mt-1">Hold Ctrl / Cmd to select multiple tags.</p>
                </div>
                
                <!-- Form Actions -->
                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('admin.articles.index') }}" class="bg-white text-slate-700 font-semibold px-4 py-2 rounded-lg border border-slate-300 hover:bg-slate-50 transition text-sm">
                        Cancel
                    </a>
                    <button type="submit" class="bg-indigo-600 text-white font-semibold px-5 py-2 rounded-lg hover:bg-indigo-700 transition text-sm shadow-2xs">
                        Save & Continue &rarr;
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

    const statusCheckbox = document.querySelector('.status-checkbox');
    const statusLabel = document.querySelector('.status-label');
    const statusDescription = document.querySelector('.status-description');
    
    if (statusCheckbox) {
        statusCheckbox.addEventListener('change', function() {
            if (this.checked) {
                statusLabel.textContent = 'Published';
                statusDescription.textContent = 'Visible to public immediately';
            } else {
                statusLabel.textContent = 'Draft';
                statusDescription.textContent = 'Saved as draft';
            }
        });
    }
</script>
@endpush