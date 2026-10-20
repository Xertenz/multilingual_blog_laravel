@extends("layouts.admin.master")
@section('title', 'Create New Article Translation')

@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    @vite(['resources/css/components/article-trans-add-edit.css'])
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
        .alert-error {
            background-color: #fef2f2;
            border-left: 4px solid #ef4444;
            border-radius: 8px;
            padding: 0.875rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #991b1b;
            font-size: 0.875rem;
        }
    </style>
@endpush

@section('content')
<div class="container mx-auto pb-8 px-4 max-w-3xl">
    
    <!-- Compact Header -->
    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Add Article Translation</h1>
            <p class="text-xs text-slate-500">Create a new localized version for this article</p>
        </div>
        <a href="{{ route('admin.articles.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 bg-white border border-slate-200 px-3 py-1.5 rounded-lg shadow-2xs">
            &larr; Back to Articles
        </a>
    </div>
    
    <!-- Form Card -->
    <div class="form-card">
        <div class="p-6">
            <form action="{{route('admin.articles-trans.store')}}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('POST')
                
                <!-- Language Error Alert -->
                @error('language_exists')
                    <div class="alert-error">
                        <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        <span>{{$message}}</span>
                    </div>
                @enderror
                
                <!-- Language & Slug Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="locale" class="input-label">
                            Language <span class="required">*</span>
                        </label>
                        <div class="relative">
                            <select name="locale" id="locale" class="input-field cursor-pointer pr-10">
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
                            <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                        @error('locale')
                            <div class="error-message">{{$message}}</div>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="slug" class="input-label">
                            Slug <span class="required">*</span>
                        </label>
                        <input type="text" name="slug" value="{{old('slug')}}" 
                               class="input-field @error('slug') error @enderror" 
                               placeholder="my-awesome-article" autocomplete="off">
                        @error('slug')
                            <div class="error-message">{{$message}}</div>
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
                        <div>
                            <label for="meta_title" class="input-label">Meta Title <span class="required">*</span></label>
                            <input type="text" name="meta_title" value="{{old('meta_title')}}" 
                                   class="input-field @error('meta_title') error @enderror" 
                                   placeholder="Enter meta title" autocomplete="off">
                            @error('meta_title')
                                <div class="error-message">{{$message}}</div>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="meta_description" class="input-label">Meta Description <span class="required">*</span></label>
                            <textarea name="meta_description" id="meta_description" rows="3"
                                      class="input-field resize-none @error('meta_description') error @enderror"
                                      placeholder="Brief description for search engines">{{old('meta_description')}}</textarea>
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
                    
                    <div class="mb-3">
                        <label for="title" class="input-label">
                            Article Title <span class="required">*</span>
                        </label>
                        <input type="text" name="title" value="{{old('title')}}" 
                               class="input-field @error('title') error @enderror" 
                               placeholder="Enter your article title" autocomplete="off">
                        @error('title')
                            <div class="error-message">{{$message}}</div>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="description" class="input-label">
                            Content <span class="required">*</span>
                        </label>
                        <textarea name="description" id="description" rows="8"
                                  class="input-field resize-y @error('description') error @enderror"
                                  placeholder="Write your article content here...">{{old('description')}}</textarea>
                        @error('description')
                            <div class="error-message">{{$message}}</div>
                        @enderror
                    </div>
                </div>
                
                <input type="hidden" name="article_id" value="{{$article_id}}" />
                
                <!-- Form Actions -->
                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('admin.articles.index') }}" class="btn-secondary">
                        Cancel
                    </a>
                    <button type="submit" class="btn-primary">
                        Create Translation &rarr;
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
        @php
            $article_content_imgs_info = [];
            $i = 0;
            foreach($article_content_imgs as $article_content_img) {
                $article_content_imgs_info[$i]['id'] = $article_content_img->id;
                $article_content_imgs_info[$i]['src'] = $article_content_img->original_url;
                $i++;
            }
        @endphp
        const article_content_imgs_info = @json($article_content_imgs_info);
    </script>
    @vite(['resources/js/components/article-trans-add-edit.js'])
@endpush