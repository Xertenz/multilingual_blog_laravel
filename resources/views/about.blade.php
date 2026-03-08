@extends('layouts.app')
@section('title', __('about.title'))
@push('styles')
<style>
    body {
        font-family: 'Inter', sans-serif;
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        color: #f1f5f9;
        min-height: 100vh;
    }
    
    .about-content {
        line-height: 1.8;
        color: #e2e8f0;
    }
    
    .about-card {
        background: rgba(30, 41, 59, 0.5);
        backdrop-filter: blur(10px);
        border: 1px solid #334155;
        border-radius: 16px;
        padding: 2rem;
        transition: all 0.3s ease;
    }
    
    .about-card:hover {
        border-color: #ef4444;
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -5px rgba(239, 68, 68, 0.2);
    }
    
    .team-card {
        background: rgba(30, 41, 59, 0.5);
        backdrop-filter: blur(10px);
        border: 1px solid #334155;
        border-radius: 16px;
        padding: 1.5rem;
        transition: all 0.3s ease;
    }
    
    .team-card:hover {
        border-color: #ef4444;
        transform: translateY(-5px);
    }
    
    .stat-card {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        border: 1px solid #334155;
        border-radius: 16px;
        padding: 1.5rem;
        text-align: center;
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
        transform: rotate(2deg);
    }
    
    .crime-scene-tape {
        background: repeating-linear-gradient(45deg, #fbbf24 0px, #fbbf24 20px, #000000 20px, #000000 40px);
        height: 6px;
        width: 100%;
    }
    
    .milestone-item {
        position: relative;
        padding-left: 2rem;
        margin-bottom: 1rem;
    }
    
    .milestone-item::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0.5rem;
        width: 12px;
        height: 12px;
        background: #ef4444;
        border-radius: 50%;
        border: 2px solid #1e293b;
    }
    
    [dir="rtl"] .milestone-item {
        padding-left: 0;
        padding-right: 2rem;
    }
    
    [dir="rtl"] .milestone-item::before {
        left: auto;
        right: 0;
    }
    
    .value-icon {
        width: 3rem;
        height: 3rem;
        background: rgba(239, 68, 68, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
    }
</style>
@endpush


<!-- Hero Section -->
@section('content')
<div class="container mx-auto px-4 py-16 max-w-6xl">
    <div class="relative">
        <div class="evidence-stamp">{{ __('about.confidential_stamp') }}</div>
        
        <div class="text-center mb-16">
            <h1 class="text-5xl md:text-6xl font-bold text-white mb-6">
                {{ __('about.hero.title') }}
            </h1>
            <p class="text-xl text-gray-400 max-w-3xl mx-auto">
                {{ __('about.hero.description') }}
            </p>
        </div>
        
        <!-- Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-16">
            <div class="stat-card">
                <div class="text-3xl font-bold text-red-500 mb-2">{{ __('about.stats.values.cases') }}</div>
                <div class="text-sm text-gray-400">{{ __('about.stats.cases') }}</div>
            </div>
            <div class="stat-card">
                <div class="text-3xl font-bold text-red-500 mb-2">{{ __('about.stats.values.experts') }}</div>
                <div class="text-sm text-gray-400">{{ __('about.stats.experts') }}</div>
            </div>
            <div class="stat-card">
                <div class="text-3xl font-bold text-red-500 mb-2">{{ __('about.stats.values.readers') }}</div>
                <div class="text-sm text-gray-400">{{ __('about.stats.readers') }}</div>
            </div>
            <div class="stat-card">
                <div class="text-3xl font-bold text-red-500 mb-2">{{ __('about.stats.values.languages') }}</div>
                <div class="text-sm text-gray-400">{{ __('about.stats.languages') }}</div>
            </div>
        </div>
        
        <!-- Mission -->
        <div class="about-card mb-12">
            <h2 class="text-3xl font-bold text-white mb-6">{{ __('about.mission.title') }}</h2>
            <p class="text-gray-300 text-lg mb-6">{{ __('about.mission.content') }}</p>
            <ul class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach(__('about.mission.points') as $point)
                    <li class="flex items-center text-gray-400">
                        <svg class="w-5 h-5 text-red-500 ml-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        {{ $point }}
                    </li>
                @endforeach
            </ul>
        </div>
        
        <!-- Values -->
        <div class="mb-16">
            <h2 class="text-3xl font-bold text-white mb-8 text-center">{{ __('about.values.title') }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach(__('about.values.items') as $value)
                    <div class="about-card text-center">
                        <div class="value-icon mx-auto">
                            <svg class="w-6 h-6 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">{{ $value['title'] }}</h3>
                        <p class="text-gray-400">{{ $value['description'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
        
        <!-- History -->
        <div class="about-card mb-12">
            <h2 class="text-3xl font-bold text-white mb-6">{{ __('about.history.title') }}</h2>
            <p class="text-gray-300 mb-6">{{ __('about.history.content') }}</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach(__('about.history.milestones') as $milestone)
                    <div class="milestone-item text-gray-400">
                        {{ $milestone }}
                    </div>
                @endforeach
            </div>
        </div>
        
        <!-- Approach -->
        <div class="about-card mb-12">
            <h2 class="text-3xl font-bold text-white mb-6">{{ __('about.approach.title') }}</h2>
            <p class="text-gray-300 mb-6">{{ __('about.approach.content') }}</p>
            <div class="space-y-4">
                @foreach(__('about.approach.steps') as $step => $description)
                    <div class="border-l-4 border-red-500 pl-4">
                        <h3 class="text-lg font-bold text-white mb-1">{{ $step }}</h3>
                        <p class="text-gray-400">{{ $description }}</p>
                    </div>
                @endforeach
            </div>
        </div>
        
        <!-- Team -->
        <div class="mb-12">
            <h2 class="text-3xl font-bold text-white mb-4">{{ __('about.team.title') }}</h2>
            <p class="text-gray-400 mb-8">{{ __('about.team.description') }}</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach(__('about.team.members') as $member)
                    <div class="team-card">
                        <div class="w-20 h-20 bg-red-600/20 rounded-full mb-4 mx-auto flex items-center justify-center">
                            <span class="text-2xl font-bold text-red-500">
                                {{ substr($member['name'], 0, 1) }}
                            </span>
                        </div>
                        <h3 class="text-xl font-bold text-white text-center mb-1">{{ $member['name'] }}</h3>
                        <p class="text-red-500 text-sm text-center mb-3">{{ $member['role'] }}</p>
                        <p class="text-gray-400 text-sm text-center mb-3">{{ $member['bio'] }}</p>
                        <p class="text-gray-500 text-xs text-center italic">"{{ $member['expertise'] }}"</p>
                    </div>
                @endforeach
            </div>
        </div>
        
        <!-- Join Us -->
        <div class="about-card text-center">
            <h2 class="text-3xl font-bold text-white mb-4">{{ __('about.join.title') }}</h2>
            <p class="text-gray-300 mb-6">{{ __('about.join.content') }}</p>
            <a href="{{ route('contact') }}" 
                class="inline-block bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-lg font-semibold transition transform hover:-translate-y-1">
                {{ __('about.join.button') }}
            </a>
        </div>
    </div>
</div>
@endsection