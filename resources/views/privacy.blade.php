@extends('layouts.app')
@section('title', __('privacy.title'))
@push('styles')
<style>
    body {
        font-family: 'Inter', sans-serif;
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        color: #f1f5f9;
        min-height: 100vh;
    }
    
    .legal-content {
        font-family: 'Inter', sans-serif;
        line-height: 1.8;
        color: #e2e8f0;
    }
    
    .legal-content h1 {
        font-size: 3rem;
        font-weight: 800;
        margin-bottom: 2rem;
        background: linear-gradient(135deg, #f87171, #ef4444);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    
    .legal-content h2 {
        font-size: 1.8rem;
        font-weight: 700;
        margin: 2.5rem 0 1.5rem 0;
        color: white;
        border-left: 4px solid #ef4444;
        padding-left: 1.5rem;
    }
    
    .legal-content h3 {
        font-size: 1.3rem;
        font-weight: 600;
        margin: 2rem 0 1rem 0;
        color: #f1f5f9;
    }
    
    .legal-content p {
        margin-bottom: 1.2rem;
        color: #94a3b8;
    }
    
    .legal-content ul, .legal-content ol {
        margin: 1rem 0 1.5rem 2rem;
        color: #94a3b8;
    }
    
    .legal-content li {
        margin-bottom: 0.5rem;
    }
    
    .legal-content strong {
        color: white;
        font-weight: 600;
    }
    
    .legal-card {
        background: rgba(30, 41, 59, 0.5);
        backdrop-filter: blur(10px);
        border: 1px solid #334155;
        border-radius: 16px;
        padding: 2rem;
        margin-bottom: 2rem;
    }
    
    .legal-card:hover {
        border-color: #ef4444;
        box-shadow: 0 10px 30px -15px rgba(239, 68, 68, 0.3);
    }

    /* تأثيرات خاصة */
    .crime-scene-tape {
        background: repeating-linear-gradient(45deg, #fbbf24 0px, #fbbf24 20px, #000000 20px, #000000 40px);
        height: 6px;
        width: 100%;
    }
    
    .evidence-stamp {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: rgba(220, 38, 38, 0.1);
        color: #ef4444;
        padding: 0.25rem 1rem;
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 2px;
        border-radius: 20px;
        border: 1px dashed #ef4444;
    }
</style> 
@endpush
<!-- Main Content -->
@section('content')
<div class="container mx-auto px-4 py-16 max-w-4xl">
    
    <div class="relative">
        <div class="evidence-stamp">CONFIDENTIAL</div>
        
        <div class="legal-content">
            <h1>🔒 {{__('footer.privacy_policy')}}</h1>
            
            <p class="text-lg text-gray-400 mb-8">{{  __('privacy.last_updated', ["date" => date('F j, Y')]) }}</p>
            
            <div class="legal-card">
                <h2>1. {{__('privacy.sections.information_collect.title')}}</h2>
                <p>{{__('privacy.sections.information_collect.content')}}</p>
                <ul>
                    @foreach (__('privacy.sections.information_collect.items') as $item)
                        <li><strong>{{$item['label']}}</strong> {{$item['text']}}</li>
                    @endforeach
                </ul>
            </div>
            
            <div class="legal-card">
                <h2>2. {{__('privacy.sections.how_we_use.title')}}</h2>
                <p>{{__('privacy.sections.how_we_use.content')}}</p>
                <ul>
                    @foreach (__('privacy.sections.how_we_use.items') as $item)
                        <li>{{$item}}</li>
                    @endforeach
                </ul>
            </div>
            
            <div class="legal-card">
                <h2>3. {{__('privacy.sections.data_protection.title')}}</h2>
                <p>{{__('privacy.sections.data_protection.content')}}</p>
                <ul>
                    @foreach (__('privacy.sections.data_protection.items') as $item)
                        <li>{{$item}}</li>
                    @endforeach
                </ul>
            </div>
            
            <div class="legal-card">
                <h2>4. {{__('privacy.sections.cookies.title')}}</h2>
                <p>{{__('privacy.sections.cookies.content')}}</p>
                <p>{{__('privacy.sections.cookies.description')}}</p>
            </div>
            
            <div class="legal-card">
                <h2>5. {{__('privacy.sections.third_party.title')}}</h2>
                <p>{{__('privacy.sections.third_party.content')}}</p>
            </div>
            
            <div class="legal-card">
                <h2>6. {{__('privacy.sections.your_rights.title')}}</h2>
                <p>{{__('privacy.sections.your_rights.content')}}</p>
                <ul>
                    @foreach (__('privacy.sections.your_rights.items') as $item)
                        <li>{{$item}}</li>
                    @endforeach
                </ul>
            </div>
            
            <div class="legal-card">
                <h2>7. {{__('privacy.sections.contact.title')}}</h2>
                <p>{{__('privacy.sections.contact.content')}}</p>
                <p class="mt-4">
                    <span class="text-red-400">{{__('privacy.sections.contact.email')}}</span> privacy@crimechronicles.com<br>
                    <span class="text-red-400">{{__('privacy.sections.contact.response_time')}}</span> Within 48 hours
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
