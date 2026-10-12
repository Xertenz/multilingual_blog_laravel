@extends('layouts.admin.master')
@section('title', 'Edit Article')

@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }
        .form-card {
            background: #ffffff;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            overflow: hidden;
        }
        .input-field {
            transition: all 0.2s ease;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            padding: 0.6rem 0.875rem;
            width: 100%;
            outline: none;
            font-size: 0.875rem;
            color: #1e293b;
        }
        select.input-field {
            appearance: none;
        }
        .input-field:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12);
        }
        .input-field.error {
            border-color: #ef4444;
            background-color: #fef2f2;
        }
        .input-label {
            display: block;
            font-size: 0.8125rem;
            font-weight: 600;
            color: #475569;
            margin-bottom: 0.35rem;
        }
        .input-label .required {
            color: #ef4444;
            margin-left: 0.15rem;
        }
        .error-message {
            color: #ef4444;
            font-size: 0.75rem;
            margin-top: 0.25rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }
        .btn-primary {
            background: #4f46e5;
            color: white;
            font-weight: 600;
            padding: 0.6rem 1.5rem;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-size: 0.875rem;
        }
        .btn-primary:hover {
            background: #4338ca;
        }
        .btn-secondary {
            background: white;
            color: #475569;
            font-weight: 600;
            padding: 0.6rem 1.5rem;
            border-radius: 10px;
            border: 1px solid #cbd5e1;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-size: 0.875rem;
        }
        .btn-secondary:hover {
            background: #f1f5f9;
            border-color: #94a3b8;
        }
        .section-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 1.25rem;
            border: 1px solid #e2e8f0;
            margin-bottom: 1rem;
        }
        .section-title {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.95rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 0.75rem;
        }
        .section-icon {
            width: 1.25rem;
            height: 1.25rem;
            color: #6366f1;
        }
        .upload-area {
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            padding: 1.25rem;
            text-align: center;
            transition: all 0.2s ease;
            cursor: pointer;
            background: #f8fafc;
        }
        .upload-area:hover {
            border-color: #6366f1;
            background-color: #f1f5f9;
        }
        .image-preview {
            max-width: 150px;
            max-height: 110px;
            margin-top: 0.75rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }
        .image-preview.show {
            display: block;
        }
        .current-image {
            display: inline-flex;
            align-items: center;
            background: #f1f5f9;
            border-radius: 8px;
            padding: 0.4rem 0.75rem;
            margin-top: 0.5rem;
            border: 1px solid #e2e8f0;
        }
        .remove-image {
            color: #ef4444;
            cursor: pointer;
            margin-left: 0.5rem;
            font-weight: bold;
            transition: all 0.2s ease;
        }
        .remove-image:hover {
            color: #dc2626;
        }
    </style>
@endpush

@section('content')
<div class="container mx-auto pb-8 px-4 max-w-2xl">
    
    <!-- Compact Header -->
    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-xl font-bold text-slate-950">Edit Article</h1>
            <p class="text-xs text-slate-500">Update article information (ID: {{ $article->id }})[cite: 5]</p>
        </div>
        <a href="{{ route('admin.articles.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 bg-white border border-slate-200 px-3 py-1.5 rounded-lg shadow-2xs">
            &larr; Back to Articles
        </a>
    </div>
    
    <!-- Form Card -->
    <div class="form-card">
        <div class="p-6">
            <form action="{{route('admin.articles.update', $article->id)}}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <!-- Category Section -->
                <div class="section-card">
                    <div class="section-title">
                        <svg class="section-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l5 5a2 2 0 01.586 1.414V19a2 2 0 01-2 2H7a3 3 0 01-3-3V6a3 3 0 013-3z"></path>
                        </svg>
                        <span>Category</span>
                    </div>
                    
                    <div>
                        <label for="category" class="input-label">
                            Select Category <span class="required">*</span>
                        </label>
                        <div class="relative">
                            <select name="category_id" id="category" 
                                    class="input-field cursor-pointer pr-10 @error('category') error @enderror">
                                <option value="" disabled>-- Select a category --</option>
                                @foreach ($cats as $cat)
                                <option value="{{$cat['id']}}" {{ $cat['id'] == $article->category->id ? 'selected' : '' }}>
                                    {{$cat['sign']}}
                                </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                        @error('category')
                            <div class="error-message">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                {{$message}}
                            </div>
                        @enderror
                    </div>
                </div>

                <!-- Article Sign Section -->
                <div class="section-card">
                    <div class="section-title">
                        <svg class="section-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l5 5a2 2 0 01.586 1.414V19a2 2 0 01-2 2H7a3 3 0 01-3-3V6a3 3 0 013-3z"></path>
                        </svg>
                        <span>Article Sign (Unique Identifier)</span>
                    </div>
                    
                    <div>
                        <label for="sign" class="input-label">
                            Sign <span class="required">*</span>
                        </label>
                        <input type="text" 
                               name="sign" 
                               id="sign" 
                               value="{{ old('sign', $article->sign) }}" 
                               class="input-field @error('sign') error @enderror" 
                               placeholder="e.g., roswell-mystery" 
                               autocomplete="off">
                        @error('sign')
                            <div class="error-message">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                {{$message}}
                            </div>
                        @enderror
                        <p class="text-[11px] text-slate-400 mt-1">Unique internal reference identifier (not shown to visitors).</p>
                    </div>
                </div>

                <!-- Status Switcher Section -->
                <div class="section-card">
                    <div class="section-title">
                        <svg class="section-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        <span>Publication Status</span>
                    </div>
                    
                    <div>
                        <div class="flex items-center justify-between bg-slate-50 p-3 rounded-xl border border-slate-200">
                            <div class="flex items-center space-x-3">
                                <input type="hidden" name="status" value="draft">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" 
                                           name="status" 
                                           value="published" 
                                           class="sr-only peer status-checkbox"
                                           {{ old('status', $article->status) == 'published' ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                    <span class="ml-3 text-xs font-semibold text-slate-700 status-label min-w-[60px]">
                                        {{ old('status', $article->status) == 'published' ? 'Published' : 'Draft' }}
                                    </span>
                                </label>
                            </div>
                            <span class="text-xs text-slate-500 status-description">
                                {{ old('status', $article->status) == 'published' ? 'Visible to public' : 'Saved as draft' }}
                            </span>
                        </div>
                        @error('status')
                            <div class="error-message mt-1">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                {{$message}}
                            </div>
                        @enderror
                    </div>
                </div>
                
                <!-- Featured Image Section -->
                <div class="section-card">
                    <div class="section-title">
                        <svg class="section-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span>Featured Image <span class="text-slate-400 text-xs font-normal">(Optional)</span></span>
                    </div>
                    
                    <div>
                        @if($article->hasMedia('featured_imgs'))
                        <div class="current-image mb-3">
                            <span class="text-xs text-slate-600">Current image exists</span>
                            <span class="remove-image" title="Remove image" onclick="document.getElementById('remove_current').value = '1'; this.closest('.current-image').style.display = 'none';">✕</span>
                            <input type="hidden" name="remove_current_image" id="remove_current" value="0">
                        </div>
                        @endif
                        
                        <div class="upload-area" id="uploadArea">
                            <input type="file" name="featured_img" id="featured_img" class="hidden" accept="image/*">
                            <svg class="w-8 h-8 mx-auto text-slate-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <p class="text-slate-600 text-xs font-medium">Click to upload new image or drag & drop</p>
                            <p class="text-slate-400 text-[11px] mt-0.5">PNG, JPG, WebP up to 5MB</p>
                            <img id="imagePreview" class="image-preview mx-auto" src="#" alt="Preview" style="display: none;">
                        </div>
                        @error('featured_img')
                            <div class="error-message mt-1">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                {{$message}}
                            </div>
                        @enderror
                    </div>
                </div>

                <!-- Article Tags -->
                <div class="section-card">
                    <div class="section-title">
                        <svg class="section-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M6 20l6.5-6.5A2.5 2.5 0 0012 10a2.5 2.5 0 00-3.5 0L2 16.5V20h4zM21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3"></path>
                        </svg>
                        <span>Article Tags <span class="text-slate-400 text-xs font-normal">(Optional)</span></span>
                    </div>
                    
                    <div>
                        <select name="tags[]" id="tags" multiple
                                class="input-field h-24 @error('tags') error @enderror">
                            @foreach ($tags as $tag)
                                <option value="{{ $tag['id'] }}" 
                                    {{ is_array(old('tags')) && in_array($tag['id'], old('tags')) ? 'selected' : '' }}
                                    {{ !is_array(old('tags')) && in_array($tag['id'], $article->tags->pluck('id')->toArray()) ? 'selected' : '' }}>
                                    {{ $tag['sign'] }}
                                </option>
                            @endforeach
                        </select>
                        @error('tags')
                            <div class="error-message">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                {{$message}}
                            </div>
                        @enderror
                        <p class="text-[11px] text-slate-400 mt-1">Hold Ctrl / Cmd to toggle multiple tags.</p>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('admin.articles.index') }}" class="btn-secondary">
                        Cancel
                    </a>
                    <button type="submit" class="btn-primary">
                        Update Article &rarr;
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
        
        function handleImagePreview(file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.src = e.target.result;
                imagePreview.style.display = 'block';
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
        const updateStatusUI = function() {
            if (statusCheckbox.checked) {
                statusLabel.textContent = 'Published';
                statusDescription.textContent = 'Visible to public';
            } else {
                statusLabel.textContent = 'Draft';
                statusDescription.textContent = 'Saved as draft';
            }
        };
        
        statusCheckbox.addEventListener('change', updateStatusUI);
        updateStatusUI();
    }
</script>
@endpush