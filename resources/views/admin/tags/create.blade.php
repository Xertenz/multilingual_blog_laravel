@extends('layouts.admin.master')

@section('title', 'Create Tag')

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
            margin-right: 0.15rem;
        }
        .input-hint {
            font-size: 0.75rem;
            color: #94a3b8;
            margin-top: 0.25rem;
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
    </style>
@endpush

@section('content')
<div class="container mx-auto pb-8 px-4 max-w-2xl">
    
    {{-- Compact Header --}}
    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Create New Tag</h1>
            <p class="text-xs text-slate-500">Add a new tag with multi-language support</p>
        </div>
        <a href="{{ route('admin.tags.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 bg-white border border-slate-200 px-3 py-1.5 rounded-lg shadow-2xs">
            &larr; Back to Tags
        </a>
    </div>
    
    <div class="form-card">
        <div class="p-6">
            <form action="{{ route('admin.tags.store') }}" method="POST">
                @csrf
                
                {{-- Basic Information Section --}}
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
                        </label>
                        <input type="text" 
                               name="sign" 
                               id="sign" 
                               value="{{ old('sign') }}" 
                               class="w-full rounded-lg border border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2 px-3 outline-none transition @error('sign') border-red-500 bg-red-50/50 @enderror" 
                               placeholder="e.g., serial-killer, mystery, cold-case" 
                               autocomplete="off">
                        <p class="input-hint">Unique identifier in English, only for admins, not shown to visitors.</p>
                        @error('sign')
                            <div class="error-message">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                
                {{-- Translations Sections --}}
                @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                    @php
                        $isDefaultLang = ($localeCode === config('app.locale') || $localeCode === 'en');
                    @endphp
                    
                    <div class="section-card">
                        <div class="flex items-center justify-between mb-3">
                            <div class="section-title mb-0 border-0 pb-0">
                                <svg class="section-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                                </svg>
                                <span class="text-xs">{{ $properties['native'] }} ({{ strtoupper($localeCode) }})</span>
                            </div>
                            @if($isDefaultLang)
                                <span class="bg-indigo-50 text-indigo-700 px-2.5 py-0.5 rounded-full text-[10px] font-semibold border border-indigo-100">Required</span>
                            @else
                                <span class="bg-slate-100 text-slate-600 px-2.5 py-0.5 rounded-full text-[10px] font-medium">Optional</span>
                            @endif
                        </div>
                        
                        <div>
                            <label class="input-label">
                                Name @if($isDefaultLang)<span class="required">*</span>@endif
                            </label>
                            <input type="text" 
                                   name="translations[{{ $localeCode }}][name]" 
                                   value="{{ old("translations.$localeCode.name") }}" 
                                   dir="{{ $localeCode == 'ar' ? 'rtl' : 'ltr' }}"
                                   class="w-full rounded-lg border border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2 px-3 outline-none transition @error("translations.$localeCode.name") border-red-500 bg-red-50/50 @enderror" 
                                   placeholder="e.g., {{ $localeCode == 'ar' ? 'قاتل متسلسل' : 'Serial Killer' }}" 
                                   autocomplete="off">
                            @error("translations.$localeCode.name")
                                <div class="error-message">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                @endforeach
                
                {{-- Form Actions --}}
                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('admin.tags.index') }}" class="btn-secondary text-decoration-none">
                        Cancel
                    </a>
                    <button type="submit" class="btn-primary">
                        Create Tag &rarr;
                    </button>
                </div>
                
            </form>
        </div>
    </div>
</div>
@endsection