    @extends('layouts.app')
    @section('title', __('terms.title'))
    @push('styles')
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #f1f5f9;
            min-height: 100vh;
        }
        
        .legal-content {
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
            /*
            border-left: 4px solid #ef4444;
            padding-left: 1.5rem;
            */
            border-inline-start: 4px solid #ef4444;
            padding-inline-start: 1.5rem;
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

    @section('content')
    <!-- Main Content -->
    <div class="container mx-auto px-4 py-16 max-w-4xl">


        @guest
        <a href="{{route('login')}}">login</a>
        @endguest
        @auth
        <a href="{{route('dashboard')}}">dashboard</a>
        @endauth

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