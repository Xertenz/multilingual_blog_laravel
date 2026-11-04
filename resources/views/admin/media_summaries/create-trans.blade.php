@extends("layouts.admin.master")
@section('title', 'Create Media Summary Translation')

@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    @vite(['resources/css/components/article-trans-add-edit.css'])
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        .form-card { background: #ffffff; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); border-radius: 16px; border: 1px solid #e2e8f0; overflow: hidden; }
        .input-field { transition: all 0.2s ease; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 10px; padding: 0.6rem 0.875rem; width: 100%; outline: none; font-size: 0.875rem; color: #1e293b; }
        .input-field:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12); }
        .input-label { display: block; font-size: 0.8125rem; font-weight: 600; color: #475569; margin-bottom: 0.35rem; }
        .input-label .required { color: #ef4444; margin-left: 0.15rem; }
        .error-message { color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem; }
        .section-card { background: #ffffff; border-radius: 12px; padding: 1.25rem; border: 1px solid #e2e8f0; margin-bottom: 1rem; }
    </style>
@endpush

@section('content')
<div class="container mx-auto pb-8 px-4 max-w-3xl">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Add Media Summary Translation</h1>
            <p class="text-xs text-slate-500">Create a localized version for this media item</p>
        </div>
        <a href="{{ route('admin.media-summaries.index') }}" class="text-xs font-semibold text-slate-600 bg-white border border-slate-200 px-3 py-1.5 rounded-lg">
            &larr; Back to Media List
        </a>
    </div>
    
    <div class="form-card">
        <div class="p-6">
            <form action="{{ route('admin.media-summaries-trans.store') }}" method="POST">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="locale" class="input-label">Language <span class="required">*</span></label>
                        <select name="locale" id="locale" class="input-field cursor-pointer">
                            <option value="en">🇬🇧 English</option>
                            <option value="ar">🇸🇦 Arabic</option>
                            <option value="de">🇩🇪 German</option>
                            <option value="es">🇪🇸 Spanish</option>
                            <option value="fr">🇫🇷 French</option>
                            <option value="ru">🇷🇺 Russian</option>
                            <option value="pt">🇵🇹 Portuguese</option>
                            <option value="it">🇮🇹 Italian</option>
                        </select>
                        @error('locale') <div class="error-message">{{ $message }}</div> @enderror
                    </div>
                    
                    <div>
                        <label for="slug" class="input-label">Slug <span class="required">*</span></label>
                        <input type="text" name="slug" value="{{ old('slug') }}" class="input-field" placeholder="work-title-slug" autocomplete="off">
                        @error('slug') <div class="error-message">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="section-card">
                    <label for="status" class="input-label">Publication Status <span class="required">*</span></label>
                    <select name="status" id="status" class="input-field">
                        <option value="published">Published (منشور)</option>
                        <option value="draft">Draft (مسودة)</option>
                        <option value="pending">Pending (قيد المراجعة)</option>
                    </select>
                </div>

                <!-- قسم إعدادات الـ SEO -->
                <div class="section-card">
                    <h3 class="text-sm font-bold text-slate-800 mb-3">SEO Settings</h3>
                    
                    <div class="mb-3">
                        <label for="meta_title" class="input-label">Meta Title <span class="required">*</span></label>
                        <input type="text" name="meta_title" value="{{ old('meta_title') }}" class="input-field" placeholder="SEO title for search engines" autocomplete="off">
                        @error('meta_title') <div class="error-message">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label for="meta_description" class="input-label">Meta Description <span class="required">*</span></label>
                        <textarea name="meta_description" rows="3" class="input-field" placeholder="Brief description for search engines...">{{ old('meta_description') }}</textarea>
                        @error('meta_description') <div class="error-message">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="section-card">
                    <div class="mb-3">
                        <label for="title" class="input-label">Title <span class="required">*</span></label>
                        <input type="text" name="title" value="{{ old('title') }}" class="input-field" placeholder="Enter work title" autocomplete="off">
                        @error('title') <div class="error-message">{{ $message }}</div> @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="synopsis" class="input-label">Synopsis (ملخص القصة - يدعم محرر النصوص)</label>
                        <textarea name="synopsis" id="synopsis" rows="8" class="input-field">{{ old('synopsis') }}</textarea>
                    </div>

                    <div>
                        <label for="review_analysis" class="input-label">Review & Analysis (التحليل الجنائي - يدعم محرر النصوص)</label>
                        <textarea name="review_analysis" id="review_analysis" rows="8" class="input-field">{{ old('review_analysis') }}</textarea>
                    </div>
                </div>
                
                <input type="hidden" name="media_summary_id" value="{{ $media_summary_id }}" />
                
                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('admin.media-summaries.index') }}" class="bg-white text-slate-700 font-semibold px-4 py-2 rounded-lg border border-slate-300 text-sm">Cancel</a>
                    <button type="submit" class="bg-indigo-600 text-white font-semibold px-5 py-2 rounded-lg hover:bg-indigo-700 text-sm">Create Translation &rarr;</button>
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
            $content_imgs_info = [];
            $i = 0;
            if(isset($mediaSummary)) {
                foreach($mediaSummary->getMedia('content_imgs') as $img) {
                    $content_imgs_info[$i]['id'] = $img->id;
                    $content_imgs_info[$i]['src'] = $img->original_url;
                    $i++;
                }
            }
        @endphp
        const article_content_imgs_info = @json($content_imgs_info);
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            tinymce.init({
                selector: "#synopsis, #review_analysis",
                license_key: "gpl",
                object_resizing: true,
                plugins: [
                    "advlist", "autolink", "lists", "link", "image", "charmap", "preview",
                    "anchor", "searchreplace", "visualblocks", "code", "fullscreen",
                    "insertdatetime", "media", "table", "help", "wordcount"
                ],
                toolbar: "bold italic underline undo redo | add | fontfamily fontsize table | forecolor backcolor link unlink | image media code | alignleft aligncenter alignright | bullist numlist fullscreen",
                setup: function (editor) {
                    editor.ui.registry.addButton("add", {
                        text: "+ Add Image",
                        icon: "image",
                        tooltip: "Add an image from gallery",
                        onAction() {
                            let html = `<div style="padding: 10px; font-family: Inter, sans-serif;"><h3 style="font-size: 16px; font-weight: 600; color: #1e293b; margin-bottom: 12px;">Available Images</h3><div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(110px, 1fr)); gap: 12px; max-height: 350px; overflow-y: auto; padding: 4px;">`;
                            for (let img_info of article_content_imgs_info) {
                                html += `<div onclick="addImage('${img_info["src"]}', '${img_info["id"]}')" style="cursor: pointer; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 8px; overflow: hidden;"><div style="width: 100%; height: 90px; background: #e2e8f0;"><img src="${img_info["src"]}" style="width: 100%; height: 100%; object-fit: cover;" /></div><div style="padding: 6px; text-align: center; font-size: 11px; color: #64748b;">ID: ${img_info["id"]}</div></div>`;
                            }
                            html += `</div></div>`;
                            editor.windowManager.open({
                                title: "Media Photo Gallery",
                                size: "normal",
                                body: { type: "panel", items: [{ type: "htmlpanel", html: html }] },
                                buttons: [{ text: "Close", type: "cancel" }]
                            });
                        }
                    });
                }
            });
        });

        window.addImage = function (src, content_img_id) {
            tinymce.activeEditor.insertContent(`
                <div class="article-image-wrapper my-4" style="text-align: center;">
                    <figure style="display: inline-block; max-width: 100%; margin: 0 auto; vertical-align: middle;">
                        <img src="${src}" data-image-id="${content_img_id}" decoding="async" loading="lazy" class="content_img rounded-lg shadow-sm" alt="Image" style="display: block; margin: 0 auto; height: auto;" />
                        <figcaption class="image-caption mt-2 text-sm text-gray-550 italic" contenteditable="true" style="margin-top: 8px; text-align: center;">اكتب وصف الصورة هنا...</figcaption>
                    </figure>
                </div>
                <p>&nbsp;</p>
            `);
            tinymce.activeEditor.windowManager.close();
        };
    </script>
@endpush