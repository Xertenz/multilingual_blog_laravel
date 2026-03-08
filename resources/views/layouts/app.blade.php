    <!-- Header -->
    @include('partials.header')

    <!-- Navigation -->
    @include('partials.navbar')
    <!-- شريط مسرح الجريمة (تأثير بصري) -->
    <div class="crime-scene-tape"></div>
    
    <!-- Main Content -->
    @yield('content')
    <!-- Footer -->
    @include('partials.footer')