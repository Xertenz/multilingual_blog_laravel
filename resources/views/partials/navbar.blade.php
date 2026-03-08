    <nav class="bg-[#0a0f1f] border-b border-gray-800 sticky top-0 z-40">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                
                <!-- Logo -->
                <a href="/" class="flex items-center space-x-2">
                    <span class="text-2xl font-bold">
                        <span class="text-red-500">CRIME</span>
                        <span class="text-gray-300">CHRONICLES</span>
                    </span>
                </a>
                
                <!-- Navigation -->
                <div class="hidden md:flex items-center space-x-6">
                    <a href="/cases" class="text-gray-300 hover:text-red-500 transition text-sm font-medium">{{__('nav.cases')}}</a>
                    <a href="/categories" class="text-gray-300 hover:text-red-500 transition text-sm font-medium">{{__('nav.categories')}}</a>
                    <a href="/cold-cases" class="text-gray-300 hover:text-red-500 transition text-sm font-medium">{{__('nav.cold-cases')}}</a>
                    <a href="/about" class="text-gray-300 hover:text-red-500 transition text-sm font-medium">{{__('nav.about')}}</a>
                </div>
                
                <!-- Language Switcher (إذا كان متاح) -->
                @if(isset($otherLanguages) && count($otherLanguages) > 0)
                <div class="flex items-center space-x-2">
                    @foreach($otherLanguages as $langCode => $langUrl)
                    <a href="{{ $langUrl }}" 
                       class="px-2 py-1 text-sm {{ $article_trans->language_code == $langCode ? 'bg-red-600 text-white' : 'text-gray-400 hover:text-white' }} rounded transition">
                        {{ strtoupper($langCode) }}
                    </a>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </nav>
