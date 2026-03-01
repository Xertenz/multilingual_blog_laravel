@extends("layouts.admin.master")

{{-- @section('page-title', 'Edit Article')--}}
@section('title', 'Edit Article')

@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    @vite(['resources/css/components/article-trans-add-edit.css'])
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
        select.input-field {
            padding: 0.75rem 1rem;
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
        .alert-error {
            background-color: #fef2f2;
            border-left: 4px solid #ef4444;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #991b1b;
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
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Edit Article</h1>
        <p class="text-gray-600">Update your article content and settings</p>
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
                        Edit Article
                    </h2>
                    <p class="text-indigo-100 text-sm mt-1">Update the information below to modify your article</p>
                </div>
                <div class="bg-white/20 backdrop-blur-sm rounded-full px-4 py-1.5 text-white text-sm font-medium">
                    ID: {{$article_trans->id}}
                </div>
            </div>
        </div>
        
        <!-- Form Body -->
        <div class="p-8">
            <form action="{{route('admin.articles-trans.update', $article_trans->id)}}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <!-- Language Error Alert -->
                @error('language_exists')
                    <div class="alert-error">
                        <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        <span>{{$message}}</span>
                    </div>
                @enderror
                
                <!-- Language & Slug Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    
                    <!-- Language Selection -->
                    <div>
                        <label for="language_code" class="input-label">
                            Language <span class="required">*</span>
                        </label>
                        <div class="relative">
                            <select name="language_code" id="language_code" 
                                    class="input-field appearance-none cursor-pointer pr-10">
                                <option value="en" {{$article_trans->language_code == 'en' ? 'selected' : ''}}>🇬🇧 English</option>
                                <option value="ar" {{$article_trans->language_code == 'ar' ? 'selected' : ''}}>🇸🇦 Arabic</option>
                                <option value="de" {{$article_trans->language_code == 'de' ? 'selected' : ''}}>🇩🇪 German</option>
                                <option value="es" {{$article_trans->language_code == 'es' ? 'selected' : ''}}>🇪🇸 Spanish</option>
                                <option value="fr" {{$article_trans->language_code == 'fr' ? 'selected' : ''}}>🇫🇷 French</option>
                                <option value="ru" {{$article_trans->language_code == 'ru' ? 'selected' : ''}}>🇷🇺 Russian</option>
                                <option value="pt" {{$article_trans->language_code == 'pt' ? 'selected' : ''}}>🇵🇹 Portuguese</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                        @error('language_code')
                            <div class="error-message">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                {{$message}}
                            </div>
                        @enderror
                    </div>
                    
                    <!-- Slug Field -->
                    <div>
                        <label for="slug" class="input-label">
                            Slug <span class="required">*</span>
                            <span class="input-hint">(URL-friendly version)</span>
                        </label>
                        <div class="relative">
                            <input type="text" name="slug" value="{{old('slug', $article_trans->slug)}}" 
                                   class="input-field pl-8 @error('slug') error @enderror" 
                                   placeholder="my-awesome-article" autocomplete="off">
                        </div>
                        @error('slug')
                            <div class="error-message">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                {{$message}}
                            </div>
                        @enderror
                    </div>
                </div>
                
                <!-- SEO Meta Data Section -->
                <div class="section-card">
                    <div class="section-title">
                        <svg class="section-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <span>SEO Meta Data</span>
                    </div>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <!-- Meta Title -->
                        <div>
                            <label for="meta_title" class="input-label">Meta Title</label>
                            <div class="relative">
                                <input type="text" name="meta_title" value="{{old('meta_title', $article_trans->meta_title)}}" 
                                       class="input-field pl-8 @error('meta_title') error @enderror" 
                                       placeholder="Enter meta title" autocomplete="off">
                            </div>
                            @error('meta_title')
                                <div class="error-message">{{$message}}</div>
                            @enderror
                        </div>
                        
                        <!-- Meta Description -->
                        <div>
                            <label for="meta_description" class="input-label">Meta Description</label>
                            <div class="relative">
                                <textarea name="meta_description" id="meta_description" rows="3"
                                          class="input-field pl-8 pt-3 resize-none @error('meta_description') error @enderror"
                                          placeholder="Brief description for search engines">{{old('meta_description', $article_trans->meta_description)}}</textarea>
                            </div>
                            @error('meta_description')
                                <div class="error-message">{{$message}}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <!-- Content Section -->
                <div class="section-card">
                    <div class="section-title">
                        <svg class="section-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span>Content</span>
                    </div>
                    
                    <!-- Title -->
                    <div class="mb-4">
                        <label for="title" class="input-label">
                            Article Title <span class="required">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" name="title" value="{{old('title', $article_trans->title)}}" 
                                   class="input-field pl-8 @error('title') error @enderror" 
                                   placeholder="Enter your article title" autocomplete="off">
                        </div>
                        @error('title')
                            <div class="error-message">{{$message}}</div>
                        @enderror
                    </div>
                    
                    <!-- Description (Content) -->
                    <div>
                        <label for="description" class="input-label">
                            Content <span class="required">*</span>
                            <span class="input-hint">(Will be edited with TinyMCE)</span>
                        </label>
                        <div class="relative">
                            <textarea name="description" id="description" rows="10"
                                      class="input-field pl-8 pt-3 resize-y @error('description') error @enderror"
                                      placeholder="Write your article content here...">{{old('description', $article_trans->description)}}</textarea>
                        </div>
                        @error('description')
                            <div class="error-message">{{$message}}</div>
                        @enderror
                    </div>
                </div>
                
                <!-- Hidden Input -->
                <input type="hidden" name="article_id" value="{{$article_trans->article_id}}" />
                
                <!-- Form Actions -->
                <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.articles-trans.index') }}" class="btn-secondary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
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
</div>
@endsection

@push('scripts')
    <script src="{{asset('js/tinymce/tinymce.min.js')}}"></script>
    <script>
        /*
        $article_content_imgs_info = [
            [ 'id' => 1, 'src' => 'http://localhost/23443.jpg' ],
            [ 'id' => 2, 'src' => 'http://localhost/2a58f.jpg' ],
        ]
        */
        @php
            $article_content_imgs_info = [];
            $i = 0;
            foreach($article_content_imgs as $article_content_img) {
                $article_content_imgs_info[$i]['id'] = $article_content_img->id;
                $article_content_imgs_info[$i]['src'] = $article_content_img->original_url;
                $i++;
            }
        @endphp

        // Getting main article images with it's info (src + content_img_id)
        const article_content_imgs_info = @json($article_content_imgs_info);

    </script>
    @vite(['resources/js/components/article-trans-add-edit.js'])
@endpush