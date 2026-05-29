@extends('layouts.admin.master')

@section('title', 'Create Category')

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
            margin-right: 0.25rem;
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
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
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
        }
        .grid-2 {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }
        @media (max-width: 640px) {
            .grid-2 { grid-template-columns: 1fr; }
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
        .image-preview.show { display: block; }
    </style>
@endpush

@section('content')
<div class="container mx-auto pb-8 px-4 max-w-4xl">
    
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gradient-to-br from-indigo-100 to-purple-100 mb-4 shadow-inner">
            <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l5 5a2 2 0 01.586 1.414V19a2 2 0 01-2 2H7a3 3 0 01-3-3V6a3 3 0 013-3z"></path>
            </svg>
        </div>
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Create New Category</h1>
        <p class="text-gray-600">Add a new category with multi-language support</p>
    </div>
    
    <div class="form-card">
        
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-8 py-5">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-white flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l5 5a2 2 0 01.586 1.414V19a2 2 0 01-2 2H7a3 3 0 01-3-3V6a3 3 0 013-3z"></path>
                        </svg>
                        Category Details
                    </h2>
                    <p class="text-indigo-100 text-sm mt-1">Set up the category and its translations</p>
                </div>
                <div class="bg-white/20 backdrop-blur-sm rounded-full px-4 py-1.5 text-white text-sm font-medium">
                    {{ count(LaravelLocalization::getSupportedLocales()) }} Languages Available
                </div>
            </div>
        </div>
        
        <div class="p-8">
            <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
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
                        <input type="text" 
                               name="cat_sign" 
                               id="cat_sign" 
                               value="{{ old('cat_sign') }}" 
                               class="input-field @error('cat_sign') error @enderror" 
                               placeholder="e.g., tech, news, sports" 
                               autocomplete="off">
                        @error('cat_sign')
                            <div class="error-message">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="input-label">Featured Image <span class="required">*</span></label>
                        <div class="upload-area" id="uploadArea" onclick="document.getElementById('cat_featured_img').click()">
                            <input type="file" name="cat_featured_img" id="cat_featured_img" class="hidden" accept="image/*">
                            <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <p class="text-gray-600 font-medium">Click to upload or drag and drop</p>
                            <p class="text-gray-400 text-sm mt-1">PNG, JPG, GIF, WebP up to 5MB</p>
                            <img id="imagePreview" class="image-preview mx-auto mt-4" src="#" alt="Preview">
                        </div>
                        @error('cat_featured_img')
                            <div class="error-message mt-2">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                
                @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                    @php
                        // جعل الإنجليزية مثلاً إجبارية والباقي اختياري حسب رغبتك، أو جعل لغة التطبيق الافتراضية هي الإجبارية
                        $isDefaultLang = ($localeCode === config('app.locale') || $localeCode === 'en');
                    @endphp
                    
                    <div class="section-card">
                        <div class="flex items-center justify-between mb-4">
                            <div class="section-title mb-0">
                                <svg class="section-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                                </svg>
                                <span>{{ $properties['native'] }} Translation ({{ strtoupper($localeCode) }})</span>
                            </div>
                            @if($isDefaultLang)
                                <span class="language-badge">Required</span>
                            @else
                                <span class="bg-gray-200 text-gray-700 px-3 py-1 rounded-full text-xs font-medium">Optional</span>
                            @endif
                        </div>
                        
                        <div class="grid-2">
                            <div>
                                <label class="input-label">
                                    Slug @if($isDefaultLang)<span class="required">*</span>@endif
                                </label>
                                <input type="text" 
                                       name="translations[{{ $localeCode }}][slug]" 
                                       value="{{ old("translations.$localeCode.slug") }}" 
                                       dir="{{ $localeCode == 'ar' ? 'rtl' : 'ltr' }}"
                                       class="input-field @error("translations.$localeCode.slug") error @enderror" 
                                       placeholder="e.g., {{ $localeCode == 'ar' ? 'أخبار-التقنية' : 'technology-news' }}" 
                                       autocomplete="off">
                                @error("translations.$localeCode.slug")
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div>
                                <label class="input-label">
                                    Name @if($isDefaultLang)<span class="required">*</span>@endif
                                </label>
                                <input type="text" 
                                       name="translations[{{ $localeCode }}][name]" 
                                       value="{{ old("translations.$localeCode.name") }}" 
                                       dir="{{ $localeCode == 'ar' ? 'rtl' : 'ltr' }}"
                                       class="input-field @error("translations.$localeCode.name") error @enderror" 
                                       placeholder="e.g., {{ $localeCode == 'ar' ? 'تقنية' : 'Technology' }}" 
                                       autocomplete="off">
                                @error("translations.$localeCode.name")
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <label class="input-label">
                                Description @if($isDefaultLang)<span class="required">*</span>@endif
                            </label>
                            <textarea name="translations[{{ $localeCode }}][description]" 
                                      rows="3" 
                                      dir="{{ $localeCode == 'ar' ? 'rtl' : 'ltr' }}"
                                      class="input-field @error("translations.$localeCode.description") error @enderror" 
                                      placeholder="Enter category description in {{ $properties['name'] }}">{{ old("translations.$localeCode.description") }}</textarea>
                            @error("translations.$localeCode.description")
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="grid-2 mt-4">
                            <div>
                                <label class="input-label">
                                    Meta Title @if($isDefaultLang)<span class="required">*</span>@endif
                                </label>
                                <input type="text" 
                                       name="translations[{{ $localeCode }}][meta_title]" 
                                       value="{{ old("translations.$localeCode.meta_title") }}" 
                                       dir="{{ $localeCode == 'ar' ? 'rtl' : 'ltr' }}"
                                       class="input-field @error("translations.$localeCode.meta_title") error @enderror" 
                                       placeholder="Meta title for SEO" 
                                       autocomplete="off">
                                @error("translations.$localeCode.meta_title")
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div>
                                <label class="input-label">
                                    Meta Description @if($isDefaultLang)<span class="required">*</span>@endif
                                </label>
                                <textarea name="translations[{{ $localeCode }}][meta_description]" 
                                          rows="2" 
                                          dir="{{ $localeCode == 'ar' ? 'rtl' : 'ltr' }}"
                                          class="input-field @error("translations.$localeCode.meta_description") error @enderror" 
                                          placeholder="Meta description for SEO">{{ old("translations.$localeCode.meta_description") }}</textarea>
                                @error("translations.$localeCode.meta_description")
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                @endforeach
                
                <div class="flex items-center justify-end gap-4 mt-8 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.categories.index') }}" class="btn-secondary text-decoration-none">
                        Cancel
                    </a>
                    <button type="submit" class="btn-primary">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Create Category
                    </button>
                </div>
                
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        // الـ JavaScript الخاص برفع الصور والمعاينة
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('cat_featured_img');
        const imagePreview = document.getElementById('imagePreview');

        fileInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreview.classList.add('show');
                }
                reader.readAsDataURL(file);
            }
        });

        // Drag and drop functionalities
        ['dragenter', 'dragover'].forEach(eventName => {
            uploadArea.addEventListener(eventName, (e) => {
                e.preventDefault();
                uploadArea.classList.add('dragover');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, (e) => {
                e.preventDefault();
                uploadArea.classList.remove('dragover');
            }, false);
        });

        uploadArea.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            fileInput.files = files;
            
            if (files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreview.classList.add('show');
                }
                reader.readAsDataURL(files[0]);
            }
        });
    </script>
@endpush