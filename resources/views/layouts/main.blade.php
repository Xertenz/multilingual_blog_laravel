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
    