@extends('layouts.admin.master')

{{-- @section('page-title', 'Edit Category') --}}
@section('title', 'Edit Category')

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
            padding: 0.75rem 1rem;
            width: 100%;
            outline: none;
            font-size: 0.95rem;
            color: #1f2937;
        }
        textarea.input-field {
            resize: vertical;
            min-height: 80px;
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
        .input-hint {
            font-size: 0.75rem;
            color: #9ca3af;
            margin-top: 0.25rem;
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
            padding-bottom: 0.75rem;
            border-bottom: 1px solid #e5e7eb;
        }
        .section-icon {
            width: 1.5rem;
            height: 1.5rem;
            color: #6366f1;
        }
        .language-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 0.25rem 1rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 1rem;
        }
        .grid-2 {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }
        @media (max-width: 640px) {
            .grid-2 {
                grid-template-columns: 1fr;
            }
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
            display: none;
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
            margin-top: 0.5rem;
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
<div class="container mx-auto pb-8 px-4 max-w-4xl">
    
    <!-- Header with Decoration -->
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gradient-to-br from-indigo-100 to-purple-100 mb-4 shadow-inner">
            <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
        </div>
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Edit Category</h1>
        <p class="text-gray-600">Update category information and translations</p>
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
                        Edit Category
                    </h2>
                    <p class="text-indigo-100 text-sm mt-1">Modify the category and its translations</p>
                </div>
                <div class="bg-white/20 backdrop-blur-sm rounded-full px-4 py-1.5 text-white text-sm font-medium">
                    ID: {{ $cat->id }}
                </div>
            </div>
        </div>
        
        <!-- Form Body -->
        <div class="p-8">
            <form action="{{route('admin.categories.update', $cat->id)}}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <!-- Basic Info Section -->
                <div class="section-card">
                    <div class="section-title">
                        <svg class="section-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Basic Information</span>
                    </div>
                    
                    <div class="mb-4">
                        <label for="cat_sign" class="input-label">
                            Category Sign <span class="required">*</span>
                            <span class="input-hint">(Unique identifier in English, only for admins, not shown by visitors)</span>
                        </label>
                        <div class="relative">
                            <input type="text" 
                                   name="cat_sign" 
                                   id="cat_sign" 
                                   value="{{old('cat_sign', $cat->sign)}}" 
                                   class="input-field pl-10 @error('cat_sign') error @enderror" 
                                   placeholder="e.g., tech, news, sports" 
                                   autocomplete="off">
                        </div>
                        @error('cat_sign')
                            <div class="error-message">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                {{$message}}
                            </div>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="input-label">Featured Image <span class="text-gray-400 text-xs font-normal">(Optional)</span></label>
                        
                        <!-- Current Image Display (if exists) -->
                        @if($cat->featured_image ?? false)
                        <div class="current-image mb-3">
                            <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-sm text-gray-600">Current image exists</span>
                            <span class="remove-image" onclick="document.getElementById('remove_current').value = '1'; this.closest('.current-image').style.display = 'none';">✕</span>
                            <input type="hidden" name="remove_current_image" id="remove_current" value="0">
                        </div>
                        @endif
                        
                        <!-- Custom File Upload Area -->
                        <div class="upload-area" id="uploadArea">
                            <input type="file" name="cat_featured_img" id="cat_featured_img" class="hidden" accept="image/*">
                            
                            <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            
                            <p class="text-gray-600 font-medium">Click to upload new image or drag and drop</p>
                            <p class="text-gray-400 text-sm mt-1">PNG, JPG, GIF, WebP up to 5MB</p>
                            
                            <!-- Image Preview -->
                            <img id="imagePreview" class="image-preview mx-auto mt-4" src="#" alt="Preview">
                        </div>
                        
                        @error('cat_featured_img')
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
                
                <!-- English Translation Section (Required) -->
                <div class="section-card">
                    @php
                        $enTranslation = $cat->translations->where('language_code', 'en')->first();
                    @endphp
                    <div class="flex items-center justify-between mb-4">
                        <div class="section-title mb-0">
                            <svg class="section-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                            </svg>
                            <span>English Translation</span>
                        </div>
                        <span class="language-badge">Required</span>
                    </div>
                    
                    <div class="grid-2">
                        <div>
                            <label for="english_slug" class="input-label">
                                Slug <span class="required">*</span>
                            </label>
                            <input type="text" 
                                   name="english_slug" 
                                   id="english_slug" 
                                   value="{{old('english_slug', $enTranslation->slug ?? '')}}" 
                                   class="input-field @error('english_slug') error @enderror" 
                                   placeholder="e.g., technology-news" 
                                   autocomplete="off">
                            @error('english_slug')
                                <div class="error-message">{{$message}}</div>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="english_name" class="input-label">
                                Name <span class="required">*</span>
                            </label>
                            <input type="text" 
                                   name="english_name" 
                                   id="english_name" 
                                   value="{{old('english_name', $enTranslation->name ?? '')}}" 
                                   class="input-field @error('english_name') error @enderror" 
                                   placeholder="e.g., Technology" 
                                   autocomplete="off">
                            @error('english_name')
                                <div class="error-message">{{$message}}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <label for="english_description" class="input-label">
                            Description <span class="required">*</span>
                        </label>
                        <textarea name="english_description" 
                                  id="english_description" 
                                  rows="3" 
                                  class="input-field @error('english_description') error @enderror" 
                                  placeholder="Enter category description in English">{{old('english_description', $enTranslation->description ?? '')}}</textarea>
                        @error('english_description')
                            <div class="error-message">{{$message}}</div>
                        @enderror
                    </div>
                    
                    <div class="grid-2 mt-4">
                        <div>
                            <label for="english_meta_title" class="input-label">
                                Meta Title <span class="required">*</span>
                            </label>
                            <input type="text" 
                                   name="english_meta_title" 
                                   id="english_meta_title" 
                                   value="{{old('english_meta_title', $enTranslation->meta_title ?? '')}}" 
                                   class="input-field @error('english_meta_title') error @enderror" 
                                   placeholder="Meta title for SEO" 
                                   autocomplete="off">
                            @error('english_meta_title')
                                <div class="error-message">{{$message}}</div>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="english_meta_description" class="input-label">
                                Meta Description <span class="required">*</span>
                            </label>
                            <textarea name="english_meta_description" 
                                      id="english_meta_description" 
                                      rows="2" 
                                      class="input-field @error('english_meta_description') error @enderror" 
                                      placeholder="Meta description for SEO">{{old('english_meta_description', $enTranslation->meta_description ?? '')}}</textarea>
                            @error('english_meta_description')
                                <div class="error-message">{{$message}}</div>
                            @enderror
                        </div>
                    </div>
                    <input type="hidden" name="english_language_code" value="en">
                </div>
                
                <!-- Arabic Translation Section -->
                <div class="section-card">
                    @php
                        $arTranslation = $cat->translations->where('language_code', 'ar')->first();
                    @endphp
                    <div class="flex items-center justify-between mb-4">
                        <div class="section-title mb-0">
                            <svg class="section-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                            </svg>
                            <span>Arabic Translation</span>
                        </div>
                        <span class="bg-gray-200 text-gray-700 px-3 py-1 rounded-full text-xs font-medium">Optional</span>
                    </div>
                    
                    <div class="grid-2">
                        <div>
                            <label for="arabic_slug" class="input-label">Slug</label>
                            <input type="text" 
                                   name="arabic_slug" 
                                   id="arabic_slug" 
                                   dir="rtl"
                                   value="{{old('arabic_slug', $arTranslation->slug ?? '')}}" 
                                   class="input-field @error('arabic_slug') error @enderror" 
                                   placeholder="مثال: أخبار-التقنية" 
                                   autocomplete="off">
                            @error('arabic_slug')
                                <div class="error-message">{{$message}}</div>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="arabic_name" class="input-label">Name</label>
                            <input type="text" 
                                   name="arabic_name" 
                                   id="arabic_name" 
                                   dir="rtl"
                                   value="{{old('arabic_name', $arTranslation->name ?? '')}}" 
                                   class="input-field @error('arabic_name') error @enderror" 
                                   placeholder="مثال: تقنية" 
                                   autocomplete="off">
                            @error('arabic_name')
                                <div class="error-message">{{$message}}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <label for="arabic_description" class="input-label">Description</label>
                        <textarea name="arabic_description" 
                                  id="arabic_description" 
                                   dir="rtl"
                                  rows="3" 
                                  class="input-field @error('arabic_description') error @enderror" 
                                  placeholder="وصف الفئة باللغة العربية">{{old('arabic_description', $arTranslation->description ?? '')}}</textarea>
                        @error('arabic_description')
                            <div class="error-message">{{$message}}</div>
                        @enderror
                    </div>
                    
                    <div class="grid-2 mt-4">
                        <div>
                            <label for="arabic_meta_title" class="input-label">Meta Title</label>
                            <input type="text" 
                                   name="arabic_meta_title" 
                                   id="arabic_meta_title" 
                                   dir="rtl"
                                   value="{{old('arabic_meta_title', $arTranslation->meta_title ?? '')}}" 
                                   class="input-field @error('arabic_meta_title') error @enderror" 
                                   placeholder="العنوان لتحسين محركات البحث" 
                                   autocomplete="off">
                            @error('arabic_meta_title')
                                <div class="error-message">{{$message}}</div>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="arabic_meta_description" class="input-label">Meta Description</label>
                            <textarea name="arabic_meta_description" 
                                      id="arabic_meta_description" 
                                      dir="rtl"
                                      rows="2" 
                                      class="input-field @error('arabic_meta_description') error @enderror" 
                                      placeholder="الوصف لتحسين محركات البحث">{{old('arabic_meta_description', $arTranslation->meta_description ?? '')}}</textarea>
                            @error('arabic_meta_description')
                                <div class="error-message">{{$message}}</div>
                            @enderror
                        </div>
                    </div>
                    <input type="hidden" name="arabic_language_code" value="ar">
                </div>
                
                <!-- German Translation Section -->
                <div class="section-card">
                    @php
                        $grTranslation = $cat->translations->where('language_code', 'de')->first();
                    @endphp
                    <div class="flex items-center justify-between mb-4">
                        <div class="section-title mb-0">
                            <svg class="section-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                            </svg>
                            <span>German Translation</span>
                        </div>
                        <span class="bg-gray-200 text-gray-700 px-3 py-1 rounded-full text-xs font-medium">Optional</span>
                    </div>
                    
                    <div class="grid-2">
                        <div>
                            <label for="german_slug" class="input-label">Slug</label>
                            <input type="text" 
                                   name="german_slug" 
                                   id="german_slug" 
                                   value="{{old('german_slug', $grTranslation->slug ?? '')}}" 
                                   class="input-field @error('german_slug') error @enderror" 
                                   placeholder="e.g., technologie-nachrichten" 
                                   autocomplete="off">
                            @error('german_slug')
                                <div class="error-message">{{$message}}</div>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="german_name" class="input-label">Name</label>
                            <input type="text" 
                                   name="german_name" 
                                   id="german_name" 
                                   value="{{old('german_name', $grTranslation->name ?? '')}}" 
                                   class="input-field @error('german_name') error @enderror" 
                                   placeholder="e.g., Technologie" 
                                   autocomplete="off">
                            @error('german_name')
                                <div class="error-message">{{$message}}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <label for="german_description" class="input-label">Description</label>
                        <textarea name="german_description" 
                                  id="german_description" 
                                  rows="3" 
                                  class="input-field @error('german_description') error @enderror" 
                                  placeholder="Category description in German">{{old('german_description', $grTranslation->description ?? '')}}</textarea>
                        @error('german_description')
                            <div class="error-message">{{$message}}</div>
                        @enderror
                    </div>
                    
                    <div class="grid-2 mt-4">
                        <div>
                            <label for="german_meta_title" class="input-label">Meta Title</label>
                            <input type="text" 
                                   name="german_meta_title" 
                                   id="german_meta_title" 
                                   value="{{old('german_meta_title', $grTranslation->meta_title ?? '')}}" 
                                   class="input-field @error('german_meta_title') error @enderror" 
                                   placeholder="Meta title in German" 
                                   autocomplete="off">
                            @error('german_meta_title')
                                <div class="error-message">{{$message}}</div>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="german_meta_description" class="input-label">Meta Description</label>
                            <textarea name="german_meta_description" 
                                      id="german_meta_description" 
                                      rows="2" 
                                      class="input-field @error('german_meta_description') error @enderror" 
                                      placeholder="Meta description in German">{{old('german_meta_description', $grTranslation->meta_description ?? '')}}</textarea>
                            @error('german_meta_description')
                                <div class="error-message">{{$message}}</div>
                            @enderror
                        </div>
                    </div>
                    <input type="hidden" name="german_language_code" value="de">
                </div>
                
                <!-- Spanish Translation Section -->
                <div class="section-card">
                    @php
                        $spTranslation = $cat->translations->where('language_code', 'es')->first();
                    @endphp
                    <div class="flex items-center justify-between mb-4">
                        <div class="section-title mb-0">
                            <svg class="section-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                            </svg>
                            <span>Spanish Translation</span>
                        </div>
                        <span class="bg-gray-200 text-gray-700 px-3 py-1 rounded-full text-xs font-medium">Optional</span>
                    </div>
                    
                    <div class="grid-2">
                        <div>
                            <label for="spanish_slug" class="input-label">Slug</label>
                            <input type="text" 
                                   name="spanish_slug" 
                                   id="spanish_slug" 
                                   value="{{old('spanish_slug', $spTranslation->slug ?? '')}}" 
                                   class="input-field @error('spanish_slug') error @enderror" 
                                   placeholder="e.g., noticias-tecnologia" 
                                   autocomplete="off">
                            @error('spanish_slug')
                                <div class="error-message">{{$message}}</div>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="spanish_name" class="input-label">Name</label>
                            <input type="text" 
                                   name="spanish_name" 
                                   id="spanish_name" 
                                   value="{{old('spanish_name', $spTranslation->name ?? '')}}" 
                                   class="input-field @error('spanish_name') error @enderror" 
                                   placeholder="e.g., Tecnología" 
                                   autocomplete="off">
                            @error('spanish_name')
                                <div class="error-message">{{$message}}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <label for="spanish_description" class="input-label">Description</label>
                        <textarea name="spanish_description" 
                                  id="spanish_description" 
                                  rows="3" 
                                  class="input-field @error('spanish_description') error @enderror" 
                                  placeholder="Category description in Spanish">{{old('spanish_description', $spTranslation->description ?? '')}}</textarea>
                        @error('spanish_description')
                            <div class="error-message">{{$message}}</div>
                        @enderror
                    </div>
                    
                    <div class="grid-2 mt-4">
                        <div>
                            <label for="spanish_meta_title" class="input-label">Meta Title</label>
                            <input type="text" 
                                   name="spanish_meta_title" 
                                   id="spanish_meta_title" 
                                   value="{{old('spanish_meta_title', $spTranslation->meta_title ?? '')}}" 
                                   class="input-field @error('spanish_meta_title') error @enderror" 
                                   placeholder="Meta title in Spanish" 
                                   autocomplete="off">
                            @error('spanish_meta_title')
                                <div class="error-message">{{$message}}</div>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="spanish_meta_description" class="input-label">Meta Description</label>
                            <textarea name="spanish_meta_description" 
                                      id="spanish_meta_description" 
                                      rows="2" 
                                      class="input-field @error('spanish_meta_description') error @enderror" 
                                      placeholder="Meta description in Spanish">{{old('spanish_meta_description', $spTranslation->meta_description ?? '')}}</textarea>
                            @error('spanish_meta_description')
                                <div class="error-message">{{$message}}</div>
                            @enderror
                        </div>
                    </div>
                    <input type="hidden" name="spanish_language_code" value="es">
                </div>
                
                <!-- French Translation Section -->
                <div class="section-card">
                    @php
                        $frTranslation = $cat->translations->where('language_code', 'fr')->first();
                    @endphp
                    <div class="flex items-center justify-between mb-4">
                        <div class="section-title mb-0">
                            <svg class="section-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                            </svg>
                            <span>French Translation</span>
                        </div>
                        <span class="bg-gray-200 text-gray-700 px-3 py-1 rounded-full text-xs font-medium">Optional</span>
                    </div>
                    
                    <div class="grid-2">
                        <div>
                            <label for="french_slug" class="input-label">Slug</label>
                            <input type="text" 
                                   name="french_slug" 
                                   id="french_slug" 
                                   value="{{old('french_slug', $frTranslation->slug ?? '')}}" 
                                   class="input-field @error('french_slug') error @enderror" 
                                   placeholder="e.g., actualites-technologie" 
                                   autocomplete="off">
                            @error('french_slug')
                                <div class="error-message">{{$message}}</div>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="french_name" class="input-label">Name</label>
                            <input type="text" 
                                   name="french_name" 
                                   id="french_name" 
                                   value="{{old('french_name', $frTranslation->name ?? '')}}" 
                                   class="input-field @error('french_name') error @enderror" 
                                   placeholder="e.g., Technologie" 
                                   autocomplete="off">
                            @error('french_name')
                                <div class="error-message">{{$message}}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <label for="french_description" class="input-label">Description</label>
                        <textarea name="french_description" 
                                  id="french_description" 
                                  rows="3" 
                                  class="input-field @error('french_description') error @enderror" 
                                  placeholder="Category description in French">{{old('french_description', $frTranslation->description ?? '')}}</textarea>
                        @error('french_description')
                            <div class="error-message">{{$message}}</div>
                        @enderror
                    </div>
                    
                    <div class="grid-2 mt-4">
                        <div>
                            <label for="french_meta_title" class="input-label">Meta Title</label>
                            <input type="text" 
                                   name="french_meta_title" 
                                   id="french_meta_title" 
                                   value="{{old('french_meta_title', $frTranslation->meta_title ?? '')}}" 
                                   class="input-field @error('french_meta_title') error @enderror" 
                                   placeholder="Meta title in French" 
                                   autocomplete="off">
                            @error('french_meta_title')
                                <div class="error-message">{{$message}}</div>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="french_meta_description" class="input-label">Meta Description</label>
                            <textarea name="french_meta_description" 
                                      id="french_meta_description" 
                                      rows="2" 
                                      class="input-field @error('french_meta_description') error @enderror" 
                                      placeholder="Meta description in French">{{old('french_meta_description', $frTranslation->meta_description ?? '')}}</textarea>
                            @error('french_meta_description')
                                <div class="error-message">{{$message}}</div>
                            @enderror
                        </div>
                    </div>
                    <input type="hidden" name="french_language_code" value="fr">
                </div>
                
                <!-- Russian Translation Section -->
                <div class="section-card">
                    @php
                        $ruTranslation = $cat->translations->where('language_code', 'ru')->first();
                    @endphp
                    <div class="flex items-center justify-between mb-4">
                        <div class="section-title mb-0">
                            <svg class="section-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                            </svg>
                            <span>Russian Translation</span>
                        </div>
                        <span class="bg-gray-200 text-gray-700 px-3 py-1 rounded-full text-xs font-medium">Optional</span>
                    </div>
                    
                    <div class="grid-2">
                        <div>
                            <label for="russian_slug" class="input-label">Slug</label>
                            <input type="text" 
                                   name="russian_slug" 
                                   id="russian_slug" 
                                   value="{{old('russian_slug', $ruTranslation->slug ?? '')}}" 
                                   class="input-field @error('russian_slug') error @enderror" 
                                   placeholder="e.g., tehnologiya-novosti" 
                                   autocomplete="off">
                            @error('russian_slug')
                                <div class="error-message">{{$message}}</div>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="russian_name" class="input-label">Name</label>
                            <input type="text" 
                                   name="russian_name" 
                                   id="russian_name" 
                                   value="{{old('russian_name', $ruTranslation->name ?? '')}}" 
                                   class="input-field @error('russian_name') error @enderror" 
                                   placeholder="e.g., Технология" 
                                   autocomplete="off">
                            @error('russian_name')
                                <div class="error-message">{{$message}}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <label for="russian_description" class="input-label">Description</label>
                        <textarea name="russian_description" 
                                  id="russian_description" 
                                  rows="3" 
                                  class="input-field @error('russian_description') error @enderror" 
                                  placeholder="Category description in Russian">{{old('russian_description', $ruTranslation->description ?? '')}}</textarea>
                        @error('russian_description')
                            <div class="error-message">{{$message}}</div>
                        @enderror
                    </div>
                    
                    <div class="grid-2 mt-4">
                        <div>
                            <label for="russian_meta_title" class="input-label">Meta Title</label>
                            <input type="text" 
                                   name="russian_meta_title" 
                                   id="russian_meta_title" 
                                   value="{{old('russian_meta_title', $ruTranslation->meta_title ?? '')}}" 
                                   class="input-field @error('russian_meta_title') error @enderror" 
                                   placeholder="Meta title in Russian" 
                                   autocomplete="off">
                            @error('russian_meta_title')
                                <div class="error-message">{{$message}}</div>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="russian_meta_description" class="input-label">Meta Description</label>
                            <textarea name="russian_meta_description" 
                                      id="russian_meta_description" 
                                      rows="2" 
                                      class="input-field @error('russian_meta_description') error @enderror" 
                                      placeholder="Meta description in Russian">{{old('russian_meta_description', $ruTranslation->meta_description ?? '')}}</textarea>
                            @error('russian_meta_description')
                                <div class="error-message">{{$message}}</div>
                            @enderror
                        </div>
                    </div>
                    <input type="hidden" name="russian_language_code" value="ru">
                </div>
                
                <!-- Portuguese Translation Section -->
                <div class="section-card">
                    @php
                        $ptTranslation = $cat->translations->where('language_code', 'pt')->first();
                    @endphp
                    <div class="flex items-center justify-between mb-4">
                        <div class="section-title mb-0">
                            <svg class="section-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                            </svg>
                            <span>Portuguese Translation</span>
                        </div>
                        <span class="bg-gray-200 text-gray-700 px-3 py-1 rounded-full text-xs font-medium">Optional</span>
                    </div>
                    
                    <div class="grid-2">
                        <div>
                            <label for="portuguese_slug" class="input-label">Slug</label>
                            <input type="text" 
                                   name="portuguese_slug" 
                                   id="portuguese_slug" 
                                   value="{{old('portuguese_slug', $ptTranslation->slug ?? '')}}" 
                                   class="input-field @error('portuguese_slug') error @enderror" 
                                   placeholder="e.g., noticias-tecnologia" 
                                   autocomplete="off">
                            @error('portuguese_slug')
                                <div class="error-message">{{$message}}</div>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="portuguese_name" class="input-label">Name</label>
                            <input type="text" 
                                   name="portuguese_name" 
                                   id="portuguese_name" 
                                   value="{{old('portuguese_name', $ptTranslation->name ?? '')}}" 
                                   class="input-field @error('portuguese_name') error @enderror" 
                                   placeholder="e.g., Tecnologia" 
                                   autocomplete="off">
                            @error('portuguese_name')
                                <div class="error-message">{{$message}}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <label for="portuguese_description" class="input-label">Description</label>
                        <textarea name="portuguese_description" 
                                  id="portuguese_description" 
                                  rows="3" 
                                  class="input-field @error('portuguese_description') error @enderror" 
                                  placeholder="Category description in Portuguese">{{old('portuguese_description', $ptTranslation->description ?? '')}}</textarea>
                        @error('portuguese_description')
                            <div class="error-message">{{$message}}</div>
                        @enderror
                    </div>
                    
                    <div class="grid-2 mt-4">
                        <div>
                            <label for="portuguese_meta_title" class="input-label">Meta Title</label>
                            <input type="text" 
                                   name="portuguese_meta_title" 
                                   id="portuguese_meta_title" 
                                   value="{{old('portuguese_meta_title', $ptTranslation->meta_title ?? '')}}" 
                                   class="input-field @error('portuguese_meta_title') error @enderror" 
                                   placeholder="Meta title in Portuguese" 
                                   autocomplete="off">
                            @error('portuguese_meta_title')
                                <div class="error-message">{{$message}}</div>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="portuguese_meta_description" class="input-label">Meta Description</label>
                            <textarea name="portuguese_meta_description" 
                                      id="portuguese_meta_description" 
                                      rows="2" 
                                      class="input-field @error('portuguese_meta_description') error @enderror" 
                                      placeholder="Meta description in Portuguese">{{old('portuguese_meta_description', $ptTranslation->meta_description ?? '')}}</textarea>
                            @error('portuguese_meta_description')
                                <div class="error-message">{{$message}}</div>
                            @enderror
                        </div>
                    </div>
                    <input type="hidden" name="portuguese_language_code" value="pt">
                </div>

                <!-- Dutch Translation Section -->
                <div class="section-card">
                    @php
                        $nlTranslation = $cat->translations->where('language_code', 'nl')->first();
                    @endphp
                    <div class="flex items-center justify-between mb-4">
                        <div class="section-title mb-0">
                            <svg class="section-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                            </svg>
                            <span>Dutch Translation</span>
                        </div>
                        <span class="bg-gray-200 text-gray-700 px-3 py-1 rounded-full text-xs font-medium">Optional</span>
                    </div>
                    
                    <div class="grid-2">
                        <div>
                            <label for="dutch_slug" class="input-label">Slug</label>
                            <input type="text" 
                                name="dutch_slug" 
                                id="dutch_slug" 
                                value="{{old('dutch_slug', $nlTranslation->slug ?? '')}}" 
                                class="input-field @error('dutch_slug') error @enderror" 
                                placeholder="bijv. technologie-nieuws" 
                                autocomplete="off">
                            @error('dutch_slug')
                                <div class="error-message">{{$message}}</div>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="dutch_name" class="input-label">Name</label>
                            <input type="text" 
                                name="dutch_name" 
                                id="dutch_name" 
                                value="{{old('dutch_name', $nlTranslation->name ?? '')}}" 
                                class="input-field @error('dutch_name') error @enderror" 
                                placeholder="bijv. Technologie" 
                                autocomplete="off">
                            @error('dutch_name')
                                <div class="error-message">{{$message}}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <label for="dutch_description" class="input-label">Description</label>
                        <textarea name="dutch_description" 
                                id="dutch_description" 
                                rows="3" 
                                class="input-field @error('dutch_description') error @enderror" 
                                placeholder="Categorie beschrijving in het Nederlands">{{old('dutch_description', $nlTranslation->description ?? '')}}</textarea>
                        @error('dutch_description')
                            <div class="error-message">{{$message}}</div>
                        @enderror
                    </div>
                    
                    <div class="grid-2 mt-4">
                        <div>
                            <label for="dutch_meta_title" class="input-label">Meta Title</label>
                            <input type="text" 
                                name="dutch_meta_title" 
                                id="dutch_meta_title" 
                                value="{{old('dutch_meta_title', $nlTranslation->meta_title ?? '')}}" 
                                class="input-field @error('dutch_meta_title') error @enderror" 
                                placeholder="Meta titel in het Nederlands" 
                                autocomplete="off">
                            @error('dutch_meta_title')
                                <div class="error-message">{{$message}}</div>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="dutch_meta_description" class="input-label">Meta Description</label>
                            <textarea name="dutch_meta_description" 
                                    id="dutch_meta_description" 
                                    rows="2" 
                                    class="input-field @error('dutch_meta_description') error @enderror" 
                                    placeholder="Meta beschrijving in het Nederlands">{{old('dutch_meta_description', $nlTranslation->meta_description ?? '')}}</textarea>
                            @error('dutch_meta_description')
                                <div class="error-message">{{$message}}</div>
                            @enderror
                        </div>
                    </div>
                    <input type="hidden" name="dutch_language_code" value="nl">
                </div>

                <!-- Italian Translation Section -->
                <div class="section-card">
                    @php
                        $itTranslation = $cat->translations->where('language_code', 'it')->first();
                    @endphp
                    <div class="flex items-center justify-between mb-4">
                        <div class="section-title mb-0">
                            <svg class="section-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                            </svg>
                            <span>Italian Translation</span>
                        </div>
                        <span class="bg-gray-200 text-gray-700 px-3 py-1 rounded-full text-xs font-medium">Optional</span>
                    </div>
                    
                    <div class="grid-2">
                        <div>
                            <label for="italian_slug" class="input-label">Slug</label>
                            <input type="text" 
                                name="italian_slug" 
                                id="italian_slug" 
                                value="{{old('italian_slug', $itTranslation->slug ?? '')}}" 
                                class="input-field @error('italian_slug') error @enderror" 
                                placeholder="es. notizie-tecnologia" 
                                autocomplete="off">
                            @error('italian_slug')
                                <div class="error-message">{{$message}}</div>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="italian_name" class="input-label">Name</label>
                            <input type="text" 
                                name="italian_name" 
                                id="italian_name" 
                                value="{{old('italian_name', $itTranslation->name ?? '')}}" 
                                class="input-field @error('italian_name') error @enderror" 
                                placeholder="es. Tecnologia" 
                                autocomplete="off">
                            @error('italian_name')
                                <div class="error-message">{{$message}}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <label for="italian_description" class="input-label">Description</label>
                        <textarea name="italian_description" 
                                id="italian_description" 
                                rows="3" 
                                class="input-field @error('italian_description') error @enderror" 
                                placeholder="Descrizione della categoria in italiano">{{old('italian_description', $itTranslation->description ?? '')}}</textarea>
                        @error('italian_description')
                            <div class="error-message">{{$message}}</div>
                        @enderror
                    </div>
                    
                    <div class="grid-2 mt-4">
                        <div>
                            <label for="italian_meta_title" class="input-label">Meta Title</label>
                            <input type="text" 
                                name="italian_meta_title" 
                                id="italian_meta_title" 
                                value="{{old('italian_meta_title', $itTranslation->meta_title ?? '')}}" 
                                class="input-field @error('italian_meta_title') error @enderror" 
                                placeholder="Meta titolo in italiano" 
                                autocomplete="off">
                            @error('italian_meta_title')
                                <div class="error-message">{{$message}}</div>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="italian_meta_description" class="input-label">Meta Description</label>
                            <textarea name="italian_meta_description" 
                                    id="italian_meta_description" 
                                    rows="2" 
                                    class="input-field @error('italian_meta_description') error @enderror" 
                                    placeholder="Meta descrizione in italiano">{{old('italian_meta_description', $itTranslation->meta_description ?? '')}}</textarea>
                            @error('italian_meta_description')
                                <div class="error-message">{{$message}}</div>
                            @enderror
                        </div>
                    </div>
                    <input type="hidden" name="italian_language_code" value="it">
                </div>

                <!-- Swedish Translation Section -->
                <div class="section-card">
                    @php
                        $svTranslation = $cat->translations->where('language_code', 'sv')->first();
                    @endphp
                    <div class="flex items-center justify-between mb-4">
                        <div class="section-title mb-0">
                            <svg class="section-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                            </svg>
                            <span>Swedish Translation</span>
                        </div>
                        <span class="bg-gray-200 text-gray-700 px-3 py-1 rounded-full text-xs font-medium">Optional</span>
                    </div>
                    
                    <div class="grid-2">
                        <div>
                            <label for="swedish_slug" class="input-label">Slug</label>
                            <input type="text" 
                                name="swedish_slug" 
                                id="swedish_slug" 
                                value="{{old('swedish_slug', $svTranslation->slug ?? '')}}" 
                                class="input-field @error('swedish_slug') error @enderror" 
                                placeholder="t.ex. teknik-nyheter" 
                                autocomplete="off">
                            @error('swedish_slug')
                                <div class="error-message">{{$message}}</div>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="swedish_name" class="input-label">Name</label>
                            <input type="text" 
                                name="swedish_name" 
                                id="swedish_name" 
                                value="{{old('swedish_name', $svTranslation->name ?? '')}}" 
                                class="input-field @error('swedish_name') error @enderror" 
                                placeholder="t.ex. Teknik" 
                                autocomplete="off">
                            @error('swedish_name')
                                <div class="error-message">{{$message}}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <label for="swedish_description" class="input-label">Description</label>
                        <textarea name="swedish_description" 
                                id="swedish_description" 
                                rows="3" 
                                class="input-field @error('swedish_description') error @enderror" 
                                placeholder="Kategoribeskrivning på svenska">{{old('swedish_description', $svTranslation->description ?? '')}}</textarea>
                        @error('swedish_description')
                            <div class="error-message">{{$message}}</div>
                        @enderror
                    </div>
                    
                    <div class="grid-2 mt-4">
                        <div>
                            <label for="swedish_meta_title" class="input-label">Meta Title</label>
                            <input type="text" 
                                name="swedish_meta_title" 
                                id="swedish_meta_title" 
                                value="{{old('swedish_meta_title', $svTranslation->meta_title ?? '')}}" 
                                class="input-field @error('swedish_meta_title') error @enderror" 
                                placeholder="Meta titel på svenska" 
                                autocomplete="off">
                            @error('swedish_meta_title')
                                <div class="error-message">{{$message}}</div>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="swedish_meta_description" class="input-label">Meta Description</label>
                            <textarea name="swedish_meta_description" 
                                    id="swedish_meta_description" 
                                    rows="2" 
                                    class="input-field @error('swedish_meta_description') error @enderror" 
                                    placeholder="Meta beskrivning på svenska">{{old('swedish_meta_description', $svTranslation->meta_description ?? '')}}</textarea>
                            @error('swedish_meta_description')
                                <div class="error-message">{{$message}}</div>
                            @enderror
                        </div>
                    </div>
                    <input type="hidden" name="swedish_language_code" value="sv">
                </div>

                <!-- Danish Translation Section -->
                <div class="section-card">
                    @php
                        $daTranslation = $cat->translations->where('language_code', 'da')->first();
                    @endphp
                    <div class="flex items-center justify-between mb-4">
                        <div class="section-title mb-0">
                            <svg class="section-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                            </svg>
                            <span>Danish Translation</span>
                        </div>
                        <span class="bg-gray-200 text-gray-700 px-3 py-1 rounded-full text-xs font-medium">Optional</span>
                    </div>
                    
                    <div class="grid-2">
                        <div>
                            <label for="danish_slug" class="input-label">Slug</label>
                            <input type="text" 
                                name="danish_slug" 
                                id="danish_slug" 
                                value="{{old('danish_slug', $daTranslation->slug ?? '')}}" 
                                class="input-field @error('danish_slug') error @enderror" 
                                placeholder="f.eks. teknologi-nyheder" 
                                autocomplete="off">
                            @error('danish_slug')
                                <div class="error-message">{{$message}}</div>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="danish_name" class="input-label">Name</label>
                            <input type="text" 
                                name="danish_name" 
                                id="danish_name" 
                                value="{{old('danish_name', $daTranslation->name ?? '')}}" 
                                class="input-field @error('danish_name') error @enderror" 
                                placeholder="f.eks. Teknologi" 
                                autocomplete="off">
                            @error('danish_name')
                                <div class="error-message">{{$message}}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <label for="danish_description" class="input-label">Description</label>
                        <textarea name="danish_description" 
                                id="danish_description" 
                                rows="3" 
                                class="input-field @error('danish_description') error @enderror" 
                                placeholder="Kategoribeskrivelse på dansk">{{old('danish_description', $daTranslation->description ?? '')}}</textarea>
                        @error('danish_description')
                            <div class="error-message">{{$message}}</div>
                        @enderror
                    </div>
                    
                    <div class="grid-2 mt-4">
                        <div>
                            <label for="danish_meta_title" class="input-label">Meta Title</label>
                            <input type="text" 
                                name="danish_meta_title" 
                                id="danish_meta_title" 
                                value="{{old('danish_meta_title', $daTranslation->meta_title ?? '')}}" 
                                class="input-field @error('danish_meta_title') error @enderror" 
                                placeholder="Meta titel på dansk" 
                                autocomplete="off">
                            @error('danish_meta_title')
                                <div class="error-message">{{$message}}</div>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="danish_meta_description" class="input-label">Meta Description</label>
                            <textarea name="danish_meta_description" 
                                    id="danish_meta_description" 
                                    rows="2" 
                                    class="input-field @error('danish_meta_description') error @enderror" 
                                    placeholder="Meta beskrivelse på dansk">{{old('danish_meta_description', $daTranslation->meta_description ?? '')}}</textarea>
                            @error('danish_meta_description')
                                <div class="error-message">{{$message}}</div>
                            @enderror
                        </div>
                    </div>
                    <input type="hidden" name="danish_language_code" value="da">
                </div>
            
                <!-- Norwegian Translation Section (Bokmål) -->
                <div class="section-card">
                    @php
                        $nbTranslation = $cat->translations->where('language_code', 'nb')->first();
                    @endphp
                    <div class="flex items-center justify-between mb-4">
                        <div class="section-title mb-0">
                            <svg class="section-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                            </svg>
                            <span>Norwegian Translation (Bokmål)</span>
                        </div>
                        <span class="bg-gray-200 text-gray-700 px-3 py-1 rounded-full text-xs font-medium">Optional</span>
                    </div>
                    
                    <div class="grid-2">
                        <div>
                            <label for="norwegian_slug" class="input-label">Slug</label>
                            <input type="text" 
                                name="norwegian_slug" 
                                id="norwegian_slug" 
                                value="{{old('norwegian_slug', $nbTranslation->slug ?? '')}}" 
                                class="input-field @error('norwegian_slug') error @enderror" 
                                placeholder="f.eks. teknologi-nyheter" 
                                autocomplete="off">
                            @error('norwegian_slug')
                                <div class="error-message">{{$message}}</div>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="norwegian_name" class="input-label">Name</label>
                            <input type="text" 
                                name="norwegian_name" 
                                id="norwegian_name" 
                                value="{{old('norwegian_name', $nbTranslation->name ?? '')}}" 
                                class="input-field @error('norwegian_name') error @enderror" 
                                placeholder="f.eks. Teknologi" 
                                autocomplete="off">
                            @error('norwegian_name')
                                <div class="error-message">{{$message}}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <label for="norwegian_description" class="input-label">Description</label>
                        <textarea name="norwegian_description" 
                                id="norwegian_description" 
                                rows="3" 
                                class="input-field @error('norwegian_description') error @enderror" 
                                placeholder="Kategoribeskrivelse på norsk">{{old('norwegian_description', $nbTranslation->description ?? '')}}</textarea>
                        @error('norwegian_description')
                            <div class="error-message">{{$message}}</div>
                        @enderror
                    </div>
                    
                    <div class="grid-2 mt-4">
                        <div>
                            <label for="norwegian_meta_title" class="input-label">Meta Title</label>
                            <input type="text" 
                                name="norwegian_meta_title" 
                                id="norwegian_meta_title" 
                                value="{{old('norwegian_meta_title', $nbTranslation->meta_title ?? '')}}" 
                                class="input-field @error('norwegian_meta_title') error @enderror" 
                                placeholder="Meta tittel på norsk" 
                                autocomplete="off">
                            @error('norwegian_meta_title')
                                <div class="error-message">{{$message}}</div>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="norwegian_meta_description" class="input-label">Meta Description</label>
                            <textarea name="norwegian_meta_description" 
                                    id="norwegian_meta_description" 
                                    rows="2" 
                                    class="input-field @error('norwegian_meta_description') error @enderror" 
                                    placeholder="Meta beskrivelse på norsk">{{old('norwegian_meta_description', $nbTranslation->meta_description ?? '')}}</textarea>
                            @error('norwegian_meta_description')
                                <div class="error-message">{{$message}}</div>
                            @enderror
                        </div>
                    </div>
                    <input type="hidden" name="norwegian_language_code" value="nb">
                </div>
                
                <!-- Form Actions -->
                <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.categories.index') }}" class="btn-secondary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Cancel
                    </a>
                    <button type="submit" class="btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Update Category
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Languages Supported -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
        <div class="bg-white/80 backdrop-blur-sm rounded-xl p-3 text-center border border-indigo-100">
            <span class="text-lg font-bold text-gray-800">🇬🇧</span>
            <p class="text-xs text-gray-600 mt-1">English (Required)</p>
        </div>
        <div class="bg-white/80 backdrop-blur-sm rounded-xl p-3 text-center border border-purple-100">
            <span class="text-lg font-bold text-gray-800">🇸🇦</span>
            <p class="text-xs text-gray-600 mt-1">Arabic</p>
        </div>
        <div class="bg-white/80 backdrop-blur-sm rounded-xl p-3 text-center border border-pink-100">
            <span class="text-lg font-bold text-gray-800">🇩🇪</span>
            <p class="text-xs text-gray-600 mt-1">German</p>
        </div>
        <div class="bg-white/80 backdrop-blur-sm rounded-xl p-3 text-center border border-blue-100">
            <span class="text-lg font-bold text-gray-800">🇪🇸</span>
            <p class="text-xs text-gray-600 mt-1">Spanish</p>
        </div>
        <div class="bg-white/80 backdrop-blur-sm rounded-xl p-3 text-center border border-green-100">
            <span class="text-lg font-bold text-gray-800">🇫🇷</span>
            <p class="text-xs text-gray-600 mt-1">French</p>
        </div>
        <div class="bg-white/80 backdrop-blur-sm rounded-xl p-3 text-center border border-yellow-100">
            <span class="text-lg font-bold text-gray-800">🇷🇺</span>
            <p class="text-xs text-gray-600 mt-1">Russian</p>
        </div>
        <div class="bg-white/80 backdrop-blur-sm rounded-xl p-3 text-center border border-red-100">
            <span class="text-lg font-bold text-gray-800">🇵🇹</span>
            <p class="text-xs text-gray-600 mt-1">Portuguese</p>
        </div>
        <div class="bg-white/80 backdrop-blur-sm rounded-xl p-3 text-center border border-orange-100">
            <span class="text-lg font-bold text-gray-800">🇳🇱</span>
            <p class="text-xs text-gray-600 mt-1">Dutch</p>
        </div>
        <div class="bg-white/80 backdrop-blur-sm rounded-xl p-3 text-center border border-emerald-100">
            <span class="text-lg font-bold text-gray-800">🇮🇹</span>
            <p class="text-xs text-gray-600 mt-1">Italian</p>
        </div>
        <div class="bg-white/80 backdrop-blur-sm rounded-xl p-3 text-center border border-cyan-100">
            <span class="text-lg font-bold text-gray-800">🇸🇪</span>
            <p class="text-xs text-gray-600 mt-1">Swedish</p>
        </div>
        <div class="bg-white/80 backdrop-blur-sm rounded-xl p-3 text-center border border-teal-100">
            <span class="text-lg font-bold text-gray-800">🇩🇰</span>
            <p class="text-xs text-gray-600 mt-1">Danish</p>
        </div>
        <div class="bg-white/80 backdrop-blur-sm rounded-xl p-3 text-center border border-amber-100">
            <span class="text-lg font-bold text-gray-800">🇳🇴</span>
            <p class="text-xs text-gray-600 mt-1">Norwegian (Bokmål)</p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('cat_featured_img');
        const imagePreview = document.getElementById('imagePreview');
        
        if (uploadArea && fileInput) {
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
                    imagePreview.classList.add('show');
                    
                    // Update upload area text
                    const uploadText = uploadArea.querySelector('p');
                    if (uploadText) {
                        uploadText.textContent = file.name;
                    }
                }
                
                reader.readAsDataURL(file);
            }
        }
        
        // Auto-generate English slug from name
        const englishName = document.getElementById('english_name');
        const englishSlug = document.getElementById('english_slug');
        
        if (englishName && englishSlug) {
            englishName.addEventListener('input', function() {
                let slug = this.value
                    .toLowerCase()
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/^-+|-+$/g, '');
                englishSlug.value = slug;
            });
        }
    });
</script>
@endpush