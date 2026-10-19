    {{--
    @include('partials.header')

    @include('partials.navbar')
    <!-- ==================== -->
    <!-- Main Content -->
    <!-- ==================== -->
    <main class="flex-1">
        @yield('content')
    </main>
    
    <!-- ==================== -->
    <!-- Footer -->
    <!-- ==================== -->
    @include('partials.footer')
   --}}
   
<!DOCTYPE html>
<html lang="{{ $article_trans->language_code ?? 'en' }}" dir="{{ LaravelLocalization::getCurrentLocaleDirection() }}">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title', 'Crime Chronicles')</title>
        
        @vite(['resources/css/app.css', 'resources/css/nav.css', 'resources/js/app.js'])

        @yield('meta_tags')
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Merriweather:wght@300;400;700;900&display=swap" rel="stylesheet">

        @stack('styles')
        
        <!-- متطلبات Livewire الأساسية -->
        @livewireStyles
    </head>
    <body class="flex flex-col min-h-screen bg-[#0a0f1f] text-gray-100">

        <!-- استدعاء الهيدر والنافبار -->
        @include('partials.header')
        @include('partials.navbar')

        <!-- ==================== -->
        <!-- Main Content -->
        <!-- ==================== -->
        <main class="flex-1">
            @hasSection('content')
                @yield('content')
            @endif

            @isset($slot)
                {{ $slot }}
            @endisset
        </main>
        
        <!-- ==================== -->
        <!-- Footer -->
        <!-- ==================== -->
        @include('partials.footer')

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        @stack('scripts')
        
        <!-- سكربتات Livewire الأساسية (وتتضمن Alpine.js داخلياً) -->
        @livewireScripts
    </body>
</html>