    <!-- Footer (نفس السابق) -->
    <footer class="bg-[#0a0f1f] border-t border-gray-800 mt-16 py-8">
        <div class="container mx-auto px-4 text-center text-gray-500 text-sm">
            <p>&copy; {{ date('Y') }} {{__('footer.copyright')}}</p>
            <p class="mt-2 text-xs text-gray-600">
                <a href="{{route('privacy')}}" class="hover:text-gray-400">{{__('footer.privacy_policy')}}</a> • 
                <a href="{{route('terms')}}" class="hover:text-gray-400">{{__('footer.terms_of_service')}}</a> • 
                <a href="{{route('contact')}}" class="hover:text-gray-400">{{__('footer.contact_us')}}</a>
            </p>
        </div>
    </footer>
    
    @vite(['resources/js/app.js'])
    @stack('scripts')
</body>
</html>