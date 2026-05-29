@extends('layouts.admin.master')

@section('title', 'Edit Tag')

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
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Edit Tag</h1>
        <p class="text-gray-600">Modify tag information and updating translations</p>
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
                        Tag ID: #{{ $tag->id }}
                    </h2>
                    <p class="text-indigo-100 text-sm mt-1">Update global sign and individual language entries</p>
                </div>
                <div class="bg-white/20 backdrop-blur-sm rounded-full px-4 py-1.5 text-white text-sm font-medium">
                    {{ count(LaravelLocalization::getSupportedLocales()) }} Languages Active
                </div>
            </div>
        </div>
        
        <!-- Form Body -->
        <div class="p-8">
            <form action="{{ route('admin.tags.update', $tag->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <!-- Basic Info Section (Tag Sign Only) -->
                <div class="section-card">
                    <div class="section-title">
                        <svg class="section-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Basic Information</span>
                    </div>
                    
                    <div class="mb-2">
                        <label for="sign" class="input-label">
                            Tag Sign <span class="required">*</span>
                            <span class="input-hint">(Unique identifier in English, only for admins)</span>
                        </label>
                        <input type="text" 
                               name="sign" 
                               id="sign" 
                               value="{{ old('sign', $tag->sign) }}" 
                               class="input-field @error('sign') error @enderror" 
                               placeholder="e.g., serial-killer, mystery, cold-case" 
                               autocomplete="off">
                        @error('sign')
                            <div class="error-message">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                
                <!-- Dynamic Multi-language Sections Loop -->
                @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                    @php
                        $isDefaultLang = ($localeCode === config('app.locale') || $localeCode === 'en');
                        // جلب الترجمة الحالية المخزنة في قاعدة البيانات لهذا الـ Locale بشكل آمن
                        $translation = $tag->translate($localeCode);
                    @endphp
                    
                    <div class="section-card">
                        <div class="flex items-center justify-between mb-4">
                            <div class="section-title mb-0">
                                <svg class="section-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                                </svg>
                                <span>{{ $properties['native'] }} Translation ({{ strtoupper($localeCode) }})</span>
                            </div>
                            <span class="bg-gray-200 text-gray-700 px-3 py-1 rounded-full text-xs font-medium">Optional</span>
                        </div>
                        
                        <!-- شبكة حقول الاسم والسلوج المقسمة لكل لغة -->
                        <div class="grid-2">
                            <div>
                                <label class="input-label">
                                    Name 
                                </label>
                                <input type="text" 
                                       name="translations[{{ $localeCode }}][name]" 
                                       value="{{ old("translations.$localeCode.name", $translation ? $translation->name : '') }}" 
                                       dir="{{ $localeCode == 'ar' ? 'rtl' : 'ltr' }}"
                                       class="input-field name-input @error("translations.$localeCode.name") error @enderror" 
                                       data-locale="{{ $localeCode }}"
                                       placeholder="e.g., {{ $localeCode == 'ar' ? 'قاتل متسلسل' : 'Serial Killer' }}" 
                                       autocomplete="off">
                                @error("translations.$localeCode.name")
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label class="input-label">
                                    Slug
                                </label>
                                <input type="text" 
                                       name="translations[{{ $localeCode }}][slug]" 
                                       value="{{ old("translations.$localeCode.slug", $translation ? $translation->slug : '') }}" 
                                       dir="{{ $localeCode == 'ar' ? 'rtl' : 'ltr' }}"
                                       class="input-field slug-input @error("translations.$localeCode.slug") error @enderror" 
                                       data-locale="{{ $localeCode }}"
                                       placeholder="e.g., {{ $localeCode == 'ar' ? 'قاتل-متسلسل' : 'serial-killer' }}" 
                                       autocomplete="off">
                                @error("translations.$localeCode.slug")
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                @endforeach
                
                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-4 mt-8 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.tags.index') }}" class="btn-secondary text-decoration-none">
                        Cancel
                    </a>
                    <button type="submit" class="btn-primary">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121 12h-4.5"></path>
                        </svg>
                        Update Tag
                    </button>
                </div>
                
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. الإمساك بجميع حقول الأسماء في الصفحة
    const nameInputs = document.querySelectorAll('.name-input');

    nameInputs.forEach(input => {
        input.addEventListener('input', function () {
            const locale = this.getAttribute('data-locale');
            // العثور على حقل السلوج المطابق لنفس اللغة
            const slugInput = document.querySelector(`.slug-input[data-locale="${locale}"]`);
            
            // الشرط الذكي: لا تولد تلقائياً إلا إذا كان حقل السلوج فارغاً تماماً
            // (هذا يحمي السلوج القديم المخزن في قاعدة البيانات من التغير بالخطأ)
            if (slugInput && (slugInput.value.trim() === '' || slugInput.hasAttribute('data-auto-generate'))) {
                
                // وسم علمي ليعرف الكود أن هذا الحقل تم توليده تلقائياً وليس يدوياً
                slugInput.setAttribute('data-auto-generate', 'true');
                
                // توليد السلوج ودعم الحروف العربية والعالمية (UTF-8)
                slugInput.value = generateSlug(this.value);
            }
        });
    });

    // 2. إذا قام المشرف بالتعديل داخل حقل السلوج بنفسه يدوياً، نوقف التوليد التلقائي فوراً
    const slugInputs = document.querySelectorAll('.slug-input');
    slugInputs.forEach(slugInput => {
        slugInput.addEventListener('input', function() {
            this.removeAttribute('data-auto-generate');
        });
    });

    // 3. دالة تحويل النص إلى Slug (تتوافق تماماً مع طريقة لارافل وتدعم العربية)
    function generateSlug(text) {
        return text
            .toString()
            .toLowerCase()
            .trim()
            // استبدال المسافات والرموز بشرطة واحدة (-) مع الحفاظ على الحروف العربية واللاتينية
            .replace(/[^a-z0-9\u0600-\u06FF\u0750-\u077F\uFB50-\uFDFF\uFE70-\uFEFF-]/g, '-') 
            .replace(/\s+/g, '-')           // تحويل المسافات المتعددة لشرطة واحدة
            .replace(/-+/g, '-')            // تحويل الشرطات المتكررة لشرطة واحدة
            .replace(/^-+/, '')             // إزالة الشرطات من البداية
            .replace(/-+$/, '');            // إزالة الشرطات من النهاية
    }
});
</script> 
@endpush