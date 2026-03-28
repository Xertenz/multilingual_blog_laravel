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
        @if (isset($slot))
            {{$slot}}
        @else
            @yield('content')
        @endif
    </main>
    
    <!-- ==================== -->
    <!-- Footer -->
    <!-- ==================== -->
    @include('partials.footer')
    