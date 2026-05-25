    @extends('layouts.main')
    @section('title', __('terms.title'))

    @push('styles')
    @endpush

    @section('meta_tags')
    @vite(['resources/css/terms.css'])
    @endsection

    @section('content')
    <!-- Main Content -->
    <div class="container mx-auto px-4 py-16 max-w-4xl">
        <div class="relative">
            <div class="evidence-stamp">LEGAL BINDING</div>
            
            <div class="legal-content">
                <h1>⚖️ {{__('terms.title')}}</h1>
                
                <p class="text-lg text-gray-400 mb-8">{{ __('terms.effective_date', ['date' => date('F j, Y')])  }}</p>
                
                <div class="legal-card">
                    <h2>{{__('terms.sections.acceptance.title')}}</h2>
                    <p>{{__('terms.sections.acceptance.content')}}</p>
                </div>
                
                <div class="legal-card">
                    <h2>{{__('terms.sections.guidelines.title')}}</h2>
                    <p>{{__('terms.sections.guidelines.content')}}</p>
                    <ul>
                        @foreach (__('terms.sections.guidelines.items') as $item)
                        <li>{{$item}}</li>
                        @endforeach
                    </ul>
                </div>
                
                <div class="legal-card">
                    <h2>{{__('terms.sections.intellectual_property.title')}}</h2>
                    <p>{{__('terms.sections.intellectual_property.content')}}</p>
                </div>
                
                <div class="legal-card">
                    <h2>{{__('terms.sections.user_contributions.title')}}</h2>
                    <p>{{__('terms.sections.user_contributions.content')}}</p>
                    <ul>
                        @foreach (__('terms.sections.user_contributions.items') as $item)
                        <li>{{$item}}</li>
                        @endforeach
                    </ul>
                </div>
                
                <div class="legal-card">
                    <h2>{{trans('terms.sections.disclaimer.title')}}</h2>
                    <p>{{trans('terms.sections.disclaimer.content')}}</p>
                </div>
                
                <div class="legal-card">
                    <h2>{{trans('terms.sections.liability.title')}}</h2>
                    <p>{{trans('terms.sections.liability.content')}}</p>
                </div>
                
                <div class="legal-card">
                    <h2>{{trans('terms.sections.changes.title')}}</h2>
                    <p>{{trans('terms.sections.changes.content')}}</p>
                </div>
            </div>
        </div>
    </div>
    @endsection