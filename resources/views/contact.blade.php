    <!-- Header -->
    @extends('layouts.main')
    @section('title', __('contact.title'))
    @push('styles')

    @endpush

    @section('meta_tags')
    @vite(['resources/css/contact.css'])
    @endsection
    <!-- Main Content -->
    @section('content')
    <div class="container mx-auto px-4 py-16 max-w-4xl">
        
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">{{__('contact.title')}}</h1>
            <p class="text-xl text-gray-400">{{__('contact.subtitle')}}</p>
        </div>
        
        <!-- Contact Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            <div class="contact-card">
                <div class="w-12 h-12 bg-red-600/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="text-white font-bold mb-2">{{__('contact.cards.email.title')}}</h3>
                <p class="text-gray-400 text-sm">{{__('contact.cards.email.value')}}</p>
                <p class="text-gray-500 text-xs mt-2">{{__('contact.cards.email.response')}}</p>
            </div>
            
            <div class="contact-card">
                <div class="w-12 h-12 bg-red-600/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                </div>
                <h3 class="text-white font-bold mb-2">{{__('contact.cards.tip_line.title')}}</h3>
                <p class="text-gray-400 text-sm">{{__('contact.cards.tip_line.value')}}</p>
                <p class="text-gray-500 text-xs mt-2">{{__('contact.cards.tip_line.response')}}</p>
            </div>
            
            <div class="contact-card">
                <div class="w-12 h-12 bg-red-600/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/>
                    </svg>
                </div>
                <h3 class="text-white font-bold mb-2">{{__('contact.cards.live_chat.title')}}</h3>
                <p class="text-gray-400 text-sm">{{__('contact.cards.live_chat.value')}}</p>
                <p class="text-gray-500 text-xs mt-2">{{__('contact.cards.live_chat.response')}}</p>
            </div>
        </div>
        
        <!-- Contact Form -->
        <div class="contact-form">
            <h2 class="text-2xl font-bold text-white mb-6">{{__('contact.form.title')}}</h2>
            
            @if(session('success'))
                <div class="success-message">
                    {{ session('success') }}
                </div>
            @endif
            
            @if($errors->any())
                <div class="error-message">
                    {{__('contact.error_message')}}
                </div>
            @endif
            
            <form action="{{ route('contact.submit') }}" method="POST">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="contact-label">{{__('contact.form.name')}}</label>
                        <input type="text" name="name" value="{{ old('name') }}" 
                               class="contact-input @error('name') border-red-500 @enderror" 
                               placeholder="{{__('contact.form.name')}}">
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="contact-label">{{__('contact.form.email')}}</label>
                        <input type="email" name="email" value="{{ old('email') }}" 
                               class="contact-input @error('email') border-red-500 @enderror" 
                               placeholder="{{__('contact.form.email')}}">
                        @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="mb-6">
                    <label class="contact-label">{{__('contact.form.subject')}}</label>
                    <select name="subject" class="contact-input @error('subject') border-red-500 @enderror">
                        <option value="">{{__('contact.form.subjects.select')}}</option>
                        <option value="case-tip" {{ old('subject') == 'case-tip' ? 'selected' : '' }}>{{__('contact.form.subjects.case_tip')}}</option>
                        <option value="collaboration" {{ old('subject') == 'collaboration' ? 'selected' : '' }}>{{__('contact.form.subjects.collaboration')}}</option>
                        <option value="press" {{ old('subject') == 'press' ? 'selected' : '' }}>{{__('contact.form.subjects.press')}}</option>
                        <option value="other" {{ old('subject') == 'other' ? 'selected' : '' }}>{{__('contact.form.subjects.other')}}</option>
                    </select>
                    @error('subject')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-6">
                    <label class="contact-label">{{__('contact.form.message')}}</label>
                    <textarea name="message" rows="6" 
                              class="contact-input @error('message') border-red-500 @enderror" 
                              placeholder="{{__('contact.form.message_placeholder')}}">{{ old('message') }}</textarea>
                    @error('message')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-6">
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="anonymous" value="1" {{ old('anonymous') ? 'checked' : '' }} 
                               class="form-checkbox bg-[#1e293b] border-gray-700 text-red-500">
                        <span class="text-sm text-gray-400">{{__('contact.form.anonymous')}}</span>
                    </label>
                </div>
                
                <button type="submit" class="submit-btn">
                    {{__('contact.form.title')}}
                </button>
            </form>
        </div>
        
        <!-- Disclaimer -->
        <div class="mt-8 text-center text-gray-500 text-sm">
            <p>{{__('contact.disclaimer')}}</p>
            <p class="mt-2">{{__('contact.encryption_note')}}</p>
        </div>
    </div>
    @endsection