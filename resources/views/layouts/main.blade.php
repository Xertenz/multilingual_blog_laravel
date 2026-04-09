    <!-- ==================== -->
    <!-- Header / Navigation -->
    <!-- ==================== -->
    @include('partials.header')

    @include('partials.navbar')
    <div class="crime-scene-tape"></div>
    
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
    