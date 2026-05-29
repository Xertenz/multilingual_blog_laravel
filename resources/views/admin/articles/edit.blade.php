@extends('layouts.admin.master')

{{-- @section('page-title', 'Update Article') --}}
@section('title', 'Edit Article')

@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
        }
        .form-card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.98);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08), 0 0 0 1px rgba(102, 126, 234, 0.1) inset;
            border-radius: 24px;
            overflow: hidden;
        }
        .input-field {
            transition: all 0.2s ease;
            background: #ffffff;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding-top: 0.75rem;
            padding-right: 1rem;
            padding-bottom: 0.75rem;
            padding-left: 1rem;
            width: 100%;
            outline: none;
            font-size: 0.95rem;
            color: #1f2937;
        }
        select.input-field {
            padding: 0.75rem 1rem;
            appearance: none;
        }
        .input-field:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
            transform: translateY(-1px);
        }
        .input-field.error {
            border-color: #ef4444;
            background-color: #fef2f2;
        }
        .input-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
        }
        .input-label .required {
            color: #ef4444;
            margin-left: 0.25rem;
        }
        .error-message {
            color: #ef4444;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
            animation: slideIn 0.3s ease;
        }
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: 600;
            padding: 0.75rem 2rem;
            border-radius: 12px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            box-shadow: 0 4px 6px -1px rgba(102, 126, 234, 0.2);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(102, 126, 234, 0.4);
        }
        .btn-primary:active {
            transform: translateY(0);
        }
        .btn-secondary {
            background: white;
            color: #4b5563;
            font-weight: 600;
            padding: 0.75rem 2rem;
            border-radius: 12px;
            border: 2px solid #e5e7eb;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        .btn-secondary:hover {
            background: #f9fafb;
            border-color: #9ca3af;
        }
        .btn-secondary:active {
            transform: translateY(0);
        }
        .section-card {
            background: linear-gradient(to right, #f9fafb, #ffffff);
            border-radius: 16px;
            padding: 1.5rem;
            border: 1px solid #e5e7eb;
            margin-bottom: 1.5rem;
        }
        .section-title {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1.125rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 1rem;
        }
        .section-icon {
            width: 1.5rem;
            height: 1.5rem;
            color: #6366f1;
        }
        .upload-area {
            border: 2px dashed #e5e7eb;
            border-radius: 16px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .upload-area:hover {
            border-color: #6366f1;
            background-color: #f5f7ff;
        }
        .upload-area.dragover {
            border-color: #6366f1;
            background-color: #eef2ff;
            transform: scale(1.02);
        }
        .image-preview {
            max-width: 200px;
            max-height: 150px;
            margin-top: 1rem;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .image-preview.show {
            display: block;
        }
        .current-image {
            display: inline-flex;
            align-items: center;
            background: #f3f4f6;
            border-radius: 12px;
            padding: 0.5rem 1rem;
            margin-top: 1rem;
            border: 1px solid #e5e7eb;
        }
        .remove-image {
            color: #ef4444;
            cursor: pointer;
            margin-left: 0.5rem;
            transition: all 0.2s ease;
        }
        .remove-image:hover {
            color: #dc2626;
            transform: scale(1.1);
        }
    </style>
@endpush

@section('content')
<div class="container mx-auto pb-8 px-4 max-w-3xl">
    
    <!-- Header with Decoration -->
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gradient-to-br from-indigo-100 to-purple-100 mb-4 shadow-inner">
            <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
        </div>
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Edit Article</h1>
        <p class="text-gray-600">Update the article information below</p>
    </div>
    
    <!-- Form Card -->
    <div class="form-card">
        
        <!-- Form Header -->
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-8 py-5">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-white flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Update Article
                    </h2>
                    <p class="text-indigo-100 text-sm mt-1">Modify the article details</p>
                </div>
                <div class="bg-white/20 backdrop-blur-sm rounded-full px-4 py-1.5 text-white text-sm font-medium">
                    ID: {{ $article->id }}
                </div>
            </div>
        </div>
        
        <!-- Form Body -->
        <div class="p-8">
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
                                    class="input-field appearance-none cursor-pointer pr-10 @error('category') error @enderror">
                                <option value="" disabled>-- Select a category --</option>
                                @foreach ($cats as $cat)
                                <option value="{{$cat['id']}}" {{ $cat['id'] == $article->category_id ? 'selected' : '' }}>
                                    {{$cat['sign']}}
                                </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                        @error('category')
                            <div class="error-message">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
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
                        <span>Article Sign</span>
                    </div>
                    
                    <div>
                        <label for="sign" class="input-label">
                            Article Sign <span class="required">*</span>
                            <span class="input-hint">(Unique identifier)</span>
                        </label>
                        <div class="relative">
                            <input type="text" 
                                   name="sign" 
                                   id="sign" 
                                   value="{{ old('sign', $article->sign) }}" 
                                   class="input-field pl-10 @error('sign') error @enderror" 
                                   placeholder="e.g., AI-2024-001" 
                                   autocomplete="off">
                        </div>
                        @error('sign')
                            <div class="error-message">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                {{$message}}
                            </div>
                        @enderror
                        
                        <!-- Sign Helper Text -->
                        <div class="mt-3 flex items-center text-xs text-gray-500">
                            <svg class="w-4 h-4 mr-1 text-indigo-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            <span>Use a unique identifier for this article (just for admins, not be shown for visitor)</span>
                        </div>
                    </div>
                </div>

                <!-- ⭐ Status Switcher Section (Edit Page Version) ⭐ -->
                <div class="section-card">
                    <div class="section-title">
                        <svg class="section-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        <span>Article Status</span>
                    </div>
                    
                    <div>
                        <label class="input-label">Publication Status</label>
                        
                        <div class="flex items-center justify-between bg-gray-50 p-4 rounded-xl">
                            <div class="flex items-center space-x-3">
                                <!-- Hidden input for default value when switch is off -->
                                <input type="hidden" name="status" value="draft">
                                
                                <!-- Toggle Switch -->
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" 
                                           name="status" 
                                           value="published" 
                                           class="sr-only peer status-checkbox"
                                           {{ old('status', $article->status) == 'published' ? 'checked' : '' }}>
                                    <div class="w-14 h-7 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-gradient-to-r peer-checked:from-indigo-600 peer-checked:to-purple-600"></div>
                                    <span class="ml-3 text-sm font-medium text-gray-700 status-label min-w-[70px]">
                                        {{ old('status', $article->status) == 'published' ? 'Published' : 'Draft' }}
                                    </span>
                                </label>
                            </div>
                            
                            <!-- Status Description with Published Date if applicable -->
                            <div class="flex items-center text-sm status-description-wrapper">
                                @if($article->status == 'published' && $article->published_at)
                                    <span class="text-green-600 flex items-center mr-3">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                        </svg>
                                        {{ $article->published_at->format('M d, Y') }}
                                    </span>
                                @endif
                                <span class="status-description text-gray-500">
                                    {{ old('status', $article->status) == 'published' 
                                        ? 'Article is visible to the public' 
                                        : 'Article is hidden from the public (draft mode)' }}
                                </span>
                            </div>
                        </div>
                        
                        <!-- Status Help Text -->
                        <div class="mt-3 flex items-center text-xs text-gray-500">
                            <svg class="w-4 h-4 mr-1 text-indigo-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            <span>Changing status will update article visibility immediately</span>
                        </div>
                        
                        @error('status')
                            <div class="error-message mt-2">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
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
                        <span>Featured Image</span>
                    </div>
                    
                    <div>
                        <label class="input-label">Update Image <span class="text-gray-400 text-xs font-normal">(Optional)</span></label>
                        
                        <!-- Current Image Display -->
                        @if($article->featured_image ?? false)
                        <div class="current-image">
                            <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-sm text-gray-600">Current image: </span>
                            <span class="text-sm font-medium text-gray-800 ml-1">featured_image.jpg</span>
                            <span class="remove-image" onclick="document.getElementById('remove_current').value = '1'; this.closest('.current-image').style.display = 'none';">✕</span>
                            <input type="hidden" name="remove_current_image" id="remove_current" value="0">
                        </div>
                        @endif
                        
                        <!-- Custom File Upload Area -->
                        <div class="upload-area mt-4" id="uploadArea">
                            <input type="file" name="featured_img" id="featured_img" class="hidden" accept="image/*">
                            
                            <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            
                            <p class="text-gray-600 font-medium">Click to upload new image or drag and drop</p>
                            <p class="text-gray-400 text-sm mt-1">PNG, JPG, GIF, WebP up to 5MB</p>
                            
                            <!-- Image Preview -->
                            <img id="imagePreview" class="image-preview mx-auto mt-4" src="#" alt="Preview" style="display: none;">
                        </div>
                        
                        @error('featured_img')
                            <div class="error-message mt-2">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                {{$message}}
                            </div>
                        @enderror
                        
                        <!-- Image Guidelines -->
                        <div class="mt-4 flex items-center text-xs text-gray-500">
                            <svg class="w-4 h-4 mr-1 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span>Leave empty to keep current image</span>
                        </div>
                    </div>
                </div>

                <!--  Article Tags -->
                <div class="section-card">
                    <div class="section-title">
                        <svg class="section-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M6 20l6.5-6.5A2.5 2.5 0 0012 10a2.5 2.5 0 00-3.5 0L2 16.5V20h4zM21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3"></path>
                        </svg>
                        <span>Article Tags</span>
                    </div>
                    
                    <div>
                        <label for="tags" class="input-label">
                            Manage Tags <span class="text-gray-400 text-xs font-normal">(Optional - Hold Ctrl/Cmd to toggle multiple selection)</span>
                        </label>
                        <div class="relative">
                            <select name="tags[]" id="tags" multiple class="input-field @error('tags') error @enderror">
                                @foreach ($tags as $tag)
                                    <option value="{{ $tag['id'] }}" 
                                        {{-- 1. التحقق في حال فشل التحقق (Validation) ورجوع البيانات المدخلة سابقاً --}}
                                        {{ is_array(old('tags')) && in_array($tag['id'], old('tags')) ? 'selected' : '' }}
                                        {{-- 2. التحقق التلقائي من الوسوم المخزنة في قاعدة البيانات للمقالة الحالية --}}
                                        {{ !is_array(old('tags')) && in_array($tag['id'], $article->tags->pluck('id')->toArray()) ? 'selected' : '' }}>
                                        {{ $tag['sign'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        @error('tags')
                            <div class="error-message mt-2">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                {{$message}}
                            </div>
                        @enderror
                        
                        <div class="mt-3 flex items-center text-xs text-gray-500">
                            <svg class="w-4 h-4 mr-1 text-indigo-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            <span>Current attached tags count: {{ $article->tags->count() }}</span>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.articles.index') }}" class="btn-secondary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Cancel
                    </a>
                    <button type="submit" class="btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Update Article
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Article Info -->
    <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 border border-indigo-100 mt-6">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-indigo-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <p class="text-sm text-gray-600">Article ID: <span class="font-medium text-gray-800">{{ $article->id }}</span></p>
                {{--<p class="text-sm text-gray-600 mt-1">Created: <span class="font-medium text-gray-800">{{ $article->created_at->format('Y-m-d H:i') }}</span></p>--}}
            </div>
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
        
        // Click on upload area
        uploadArea.addEventListener('click', () => {
            fileInput.click();
        });
        
        // Drag and drop functionality
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });
        
        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('dragover');
        });
        
        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                handleImagePreview(files[0]);
            }
        });
        
        // File input change
        fileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                handleImagePreview(this.files[0]);
            }
        });
        
        // Image preview function
        function handleImagePreview(file) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                imagePreview.src = e.target.result;
                imagePreview.style.display = 'block';
                
                // Update upload area text
                const uploadText = uploadArea.querySelector('p');
                if (uploadText) {
                    uploadText.textContent = file.name;
                }
            }
            
            reader.readAsDataURL(file);
        }
    });

    // Status Switcher Functionality for Edit Page
    const statusCheckbox = document.querySelector('.status-checkbox');
    const statusLabel = document.querySelector('.status-label');
    const statusDescription = document.querySelector('.status-description');
    
    if (statusCheckbox) {
        // Set initial state based on article status
        const updateStatusUI = function() {
            if (statusCheckbox.checked) {
                statusLabel.textContent = 'Published';
                statusDescription.textContent = 'Article is visible to the public';
            } else {
                statusLabel.textContent = 'Draft';
                statusDescription.textContent = 'Article is hidden from the public (draft mode)';
            }
        };
        
        // Update on change
        statusCheckbox.addEventListener('change', function() {
            updateStatusUI();
        });
        
        // Initial update
        updateStatusUI();
    }
</script>
@endpush