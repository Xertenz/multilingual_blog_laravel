{{-- resources/views/layouts/admin/master.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Adding session values to use them by javascript --}}
    @if(session('success'))
        <meta name="session-success" content="{{ session('success') }}">
    @endif
    @if(session('error'))
        <meta name="session-error" content="{{ session('error') }}">
    @endif
    
    <title>@yield('title', 'Admin Panel') - {{ config('app.name') }}</title>
    
    {{-- Tailwind CSS via Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    {{-- Additional Styles --}}
    @stack('styles')
</head>
<body class="bg-slate-100 font-sans antialiased">
    
    <div class="flex min-h-screen">
        
        {{-- ================= LEFT SIDEBAR ================= --}}
        @include('admin.partials.sidebar')
        
        {{-- ================= RIGHT CONTENT AREA ================= --}}
        <div class="flex-1 ml-72" style="width: calc(0.2rem * 75)"> {{-- ml-72 يترك مسافة للـ sidebar --}}
            
            {{-- Top Header --}}
            @include('admin.partials.header')
            
            {{-- Main Content (Your pages go here) --}}
            <main class="p-8">
                <div class="bg-white/90 rounded-2xl shadow-sm p-6 border border-slate-200 min-h-[600px]">
                    
                    {{-- Page Title --}}
                    @hasSection('page-title')
                        <h1 class="text-2xl font-bold text-slate-800 mb-6">
                            @yield('page-title')
                        </h1>
                    @endif
                    
                    {{-- Content Area --}}
                    @yield('content')

                    @isset($slot)
                        {{ $slot }}
                    @endisset
                    
                </div>
            </main>
            
            {{-- Footer --}}
            @include('admin.partials.footer')
        </div>
    </div>
    
    {{-- Scripts --}}
    @stack('scripts')
    @livewireScripts
</body>
</html>