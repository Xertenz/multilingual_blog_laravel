@extends('layouts.main')
@section('title', __('privacy.title'))
@push('styles')
<style>

</style> 
@endpush

@section('meta_tags')
@vite(['resources/css/privacy.css'])
@endsection

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
