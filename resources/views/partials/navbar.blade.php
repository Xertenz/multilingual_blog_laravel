<nav class="bg-[#0a0f1f]/90 backdrop-blur-md border-b border-b-gray-500 border-gray-850 sticky top-0 z-40 transition-all duration-300">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center h-16 gap-4">
            
            <!-- القسم الأيسر: الشعار والقائمة الأساسية -->
            <div class="left flex items-center gap-4 lg:gap-6">
                <!-- زر القائمة للجوال (Hamburger Menu) -->
                <button id="mobile-menu-btn" class="md:hidden flex items-center justify-center p-2 rounded-lg bg-slate-955 border border-slate-800 text-gray-400 hover:text-white transition">
                    <svg class="w-5 h-5" id="hamburger-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                <!-- Logo -->
                <a href="/" class="flex items-center space-x-2 group flex-shrink-0">
                    <span class="text-xl md:text-2xl font-black tracking-wider transition duration-300">
                        <span class="text-red-600 drop-shadow-[0_0_10px_rgba(220,38,38,0.3)] group-hover:text-red-500">CRIME</span>
                        <span class="text-gray-300 font-light group-hover:text-white transition duration-300 text-base md:text-lg ml-1">CHRONICLES</span>
                    </span>
                </a>

                <!-- القائمة الرئيسية للشاشات الكبيرة -->
                <div class="hidden lg:flex items-center space-x-1 rtl:space-x-reverse">
                    <a href="/cases" class="text-gray-400 hover:text-red-500 hover:bg-slate-900/50 px-3 py-1.5 rounded-lg transition-all text-sm font-medium">{{__('nav.cases')}}</a>
                    <a href="/categories" class="text-gray-400 hover:text-red-500 hover:bg-slate-900/50 px-3 py-1.5 rounded-lg transition-all text-sm font-medium">{{__('nav.categories')}}</a>
                    <a href="/cold-cases" class="text-gray-400 hover:text-red-500 hover:bg-slate-900/50 px-3 py-1.5 rounded-lg transition-all text-sm font-medium">{{__('nav.cold-cases')}}</a>
                    <a href="/about" class="text-gray-400 hover:text-red-500 hover:bg-slate-900/50 px-3 py-1.5 rounded-lg transition-all text-sm font-medium">{{__('nav.about')}}</a>
                </div>
            </div>

            <!-- حقل البحث للشاشات المتوسطة والكبيرة -->
            <div class="hidden md:block flex-1 max-w-xs lg:max-w-sm">
                <form action="{{ route('search') }}" method="GET" class="relative">
                    <input 
                        type="text" 
                        name="q" 
                        value="{{ request('q') }}"
                        placeholder="{{ __('Search articles...') }}" 
                        class="w-full bg-slate-900/90 border border-slate-800 text-gray-200 placeholder-gray-500 text-xs rounded-lg ltr:pl-9 ltr:pr-3 rtl:pr-9 rtl:pl-3 py-2 focus:outline-none focus:border-red-600/60 focus:ring-1 focus:ring-red-600/50 transition duration-200"
                        required
                    />
                    <button type="submit" class="absolute ltr:left-2.5 rtl:right-2.5 top-2.5 text-gray-500 hover:text-red-500 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                </form>
            </div>
            
            <!-- القسم الأيمن: أدوات المستخدم، أزرار الدخول، واللغة -->
            <div class="right flex items-center gap-3 md:gap-4">

                <!-- 1. حالة المستخدم مسجل الدخول -->
                @auth
                <div class="user-actions flex items-center gap-2 md:gap-3">
                    
                    <!-- صندوق الإشعارات الذكي -->
                    <div class="notifications relative" id="notifications">
                        <div class="notifications-icon bg-slate-900/80 border border-slate-800 hover:border-red-600/50 p-2 rounded-lg transition duration-200 cursor-pointer" id="notifications-icon">
                            <x-fas-bell class="w-3.5 h-3.5 text-gray-400 hover:text-white transition" id="notifications-icon-svg" />
                        </div>
                        
                        <div class="notifications-list-wrapper fixed md:absolute top-16 md:top-[calc(100%+12px)] left-4 right-4 md:left-auto md:right-0 md:rtl:left-0 md:rtl:right-auto bg-[#0f1526] border border-slate-800 rounded-xl shadow-2xl shadow-black/90 select-none md:w-80 overflow-hidden opacity-0 pointer-events-none transition-all duration-250 transform scale-95 z-50"
                            id="notifications-list-wrapper">
                            
                            <div class="px-4 py-2.5 bg-slate-900/80 border-b border-slate-800 flex justify-between items-center">
                                <span class="text-xs font-bold text-gray-300 uppercase tracking-wider">Case Updates</span>
                                <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                            </div>

                            <ul class="notifications-list flex flex-col max-h-[240px] md:max-h-[280px] overflow-y-auto divide-y divide-slate-850" id="notifications-list">
                                {{-- ستُحقن الإشعارات هنا عبر الـ JavaScript --}}
                            </ul>
                            
                            <button class="notifications-load-more bg-slate-900 hover:bg-red-900/20 text-gray-400 hover:text-red-400 text-xs uppercase w-full py-2.5 font-bold tracking-wider border-t border-slate-800 transition duration-150" 
                                    id="notifications-load-more">
                                Load More Cases
                            </button>
                        </div>
                    </div>

                    <!-- قائمة حساب المستخدم المنسدلة -->
                    <div class="user-menu-dropdown relative" id="user-menu-container">
                        <button id="user-menu-btn" class="flex items-center gap-2 bg-slate-900/80 border border-slate-800 hover:border-red-600/50 py-1.5 px-2.5 rounded-lg transition duration-200 cursor-pointer">
                            <div class="w-6 h-6 rounded-full bg-red-950/80 border border-red-800/60 flex items-center justify-center text-red-400 font-bold text-xs">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <span class="text-xs font-medium text-gray-300 hidden sm:inline-block max-w-[100px] truncate">{{ Auth::user()->name }}</span>
                            <svg class="w-3 h-3 text-gray-400 transition-transform duration-200" id="user-menu-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div id="user-dropdown-list" class="absolute right-0 rtl:left-0 rtl:right-auto top-[calc(100%+8px)] w-48 bg-[#0f1526] border border-slate-800 rounded-xl shadow-2xl shadow-black/90 overflow-hidden opacity-0 pointer-events-none transition-all duration-200 transform scale-95 z-50">
                            <div class="px-3 py-2.5 bg-slate-900/90 border-b border-slate-800">
                                <p class="text-xs font-bold text-gray-200 truncate">{{ Auth::user()->name }}</p>
                                <p class="text-[10px] text-gray-400 truncate">{{ Auth::user()->email }}</p>
                            </div>
                            
                            <div class="py-1">
                                <a href="/profile" class="flex items-center gap-2 px-3 py-2 text-xs text-gray-300 hover:bg-slate-800/80 hover:text-white transition">
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    <span>Profile Settings</span>
                                </a>

                                @if(Auth::user()->hasAnyRole(['super-admin', 'editor']))
                                <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs text-gray-300 hover:bg-slate-800/80 hover:text-white transition">
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
                                    <span>Admin Dashboard</span>
                                </a>
                                @endif
                            </div>

                            <div class="border-t border-slate-800 py-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 text-xs text-rose-400 hover:bg-rose-950/30 transition text-left rtl:text-right">
                                        <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                        <span>Log Out</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endauth

                <!-- 2. حالة المستخدم زائر (Guest) -->
                @guest
                <div class="auth-buttons hidden sm:flex items-center gap-2">
                    <a href="{{ route('login') }}" class="text-xs font-semibold text-gray-300 hover:text-white bg-slate-900 border border-slate-800 hover:border-slate-700 px-3 py-1.5 rounded-lg transition duration-200">
                        Log In
                    </a>
                    <a href="{{ route('register') }}" class="text-xs font-semibold text-white bg-red-600 hover:bg-red-700 px-3 py-1.5 rounded-lg shadow-sm shadow-red-600/20 transition duration-200">
                        Sign Up
                    </a>
                </div>
                @endguest

                <!-- محدد اللغة -->
                <div class="relative flex-shrink-0 max-w-[110px] sm:max-w-none">
                    <select id="lang-switcher" class="lang-switcher bg-slate-900 border border-slate-800 text-gray-300 hover:border-slate-700 text-xs font-medium rounded-lg pl-2.5 pr-7 py-1.5 focus:outline-none focus:ring-1 focus:ring-red-600/50 cursor-pointer capitalize appearance-none rtl:pl-7 rtl:pr-2.5" onchange="location=this.value;">
                        @foreach(LaravelLocalization::getSupportedLocales() as $localeCode =>$properties)
                            <option value="{{LaravelLocalization::getLocalizedURL($localeCode, null, [], true)}}" {{ app()->getLocale() == $localeCode ? 'selected' : '' }} class="bg-[#0a0f1f] text-gray-300">
                                {{ $properties['native'] }}
                            </option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-2 rtl:left-2 rtl:right-auto flex items-center text-gray-500">
                        <svg class="fill-current h-3 w-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                        </svg>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- قائمة الجوال المنسدلة (Mobile Dropdown Menu) -->
    <div id="mobile-menu" class="hidden md:hidden border-t border-slate-850 bg-[#0a0f1f]/95 backdrop-blur-md px-4 py-3 space-y-3 transition-all duration-300">
        <!-- حقل البحث للجوال -->
        <form action="{{ route('search') }}" method="GET" class="relative">
            <input 
                type="text" 
                name="q" 
                value="{{ request('q') }}"
                placeholder="{{ __('Search articles...') }}" 
                class="w-full bg-slate-900 border border-slate-800 text-gray-200 placeholder-gray-500 text-xs rounded-lg ltr:pl-9 ltr:pr-3 rtl:pr-9 rtl:pl-3 py-2 focus:outline-none focus:border-red-600/60 transition duration-200"
                required
            />
            <button type="submit" class="absolute ltr:left-2.5 rtl:right-2.5 top-2.5 text-gray-500 hover:text-red-500 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </button>
        </form>

        <a href="/cases" class="block text-gray-400 hover:text-red-500 hover:bg-slate-900/60 px-3 py-2 rounded-lg text-sm font-medium">{{__('nav.cases')}}</a>
        <a href="/categories" class="block text-gray-400 hover:text-red-500 hover:bg-slate-900/60 px-3 py-2 rounded-lg text-sm font-medium">{{__('nav.categories')}}</a>
        <a href="/cold-cases" class="block text-gray-400 hover:text-red-500 hover:bg-slate-900/60 px-3 py-2 rounded-lg text-sm font-medium">{{__('nav.cold-cases')}}</a>
        <a href="/about" class="block text-gray-400 hover:text-red-500 hover:bg-slate-900/60 px-3 py-2 rounded-lg text-sm font-medium">{{__('nav.about')}}</a>

        <!-- أزرار الدخول والتسجيل تظهر في القائمة الجوالة أيضاً إذا لم يكن مسجلاً -->
        @guest
        <div class="pt-2 border-t border-slate-800 flex gap-2">
            <a href="{{ route('login') }}" class="flex-1 text-center text-xs font-semibold text-gray-300 bg-slate-900 border border-slate-800 py-2 rounded-lg">
                Log In
            </a>
            <a href="{{ route('register') }}" class="flex-1 text-center text-xs font-semibold text-white bg-red-600 py-2 rounded-lg">
                Sign Up
            </a>
        </div>
        @endguest
    </div>
</nav>

<!-- شريط مسرح الجريمة التكتيكي -->
<div class="crime-scene-tape sticky left-0 top-[64px] z-30 h-[3px] bg-yellow-500"></div>

<style>
    #user-dropdown-list.active {
        opacity: 1;
        pointer-events: auto;
        transform: scale(100%) translateY(0);
    }
    #notifications-list-wrapper.active {
        opacity: 1;
        pointer-events: auto;
        transform: scale(100%) translateY(0);
    }
    .crime-scene-tape {
        background-image: linear-gradient(
            -45deg, 
            #eab308 25%, #000 25%, 
            #000 50%, #eab308 50%, 
            #eab308 75%, #000 75%, #000
        );
        background-size: 20px 20px;
    }
    #notifications-list::-webkit-scrollbar { width: 4px; }
    #notifications-list::-webkit-scrollbar-track { background: #0f1526; }
    #notifications-list::-webkit-scrollbar-thumb { background: #1e293b; border-radius: 4px; }
</style>

@push('scripts')
<script>
    const notifications = document.getElementById('notifications'),
     notifications_icon = document.getElementById('notifications-icon'),
     notifications_icon_svg = document.getElementById('notifications-icon-svg'),
     notifications_list_wrapper = document.getElementById('notifications-list-wrapper'),
     notifications_list = document.getElementById('notifications-list'),
     notifications_load_more = document.getElementById('notifications-load-more');

    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');

    mobileMenuBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        mobileMenu.classList.toggle('hidden');
    });

    if(notifications_icon) {
        notifications_icon.addEventListener('click', function(e) {
            e.stopPropagation();
            mobileMenu.classList.add('hidden'); 
            notifications_list_wrapper.classList.toggle('active');
        });
    }

    // إدارة قائمة حساب المستخدم المنسدلة (إن وجدت للمسجلين)
    const userMenuBtn = document.getElementById('user-menu-btn');
    const userDropdownList = document.getElementById('user-dropdown-list');
    const userMenuArrow = document.getElementById('user-menu-arrow');

    if (userMenuBtn) {
        userMenuBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            if(notifications_list_wrapper) {
                notifications_list_wrapper.classList.remove('active');
            }
            userDropdownList.classList.toggle('active');
            userMenuArrow.classList.toggle('rotate-180');
        });
    }

    window.addEventListener('click', function(event) {
        if (!mobileMenu.classList.contains('hidden') && !mobileMenu.contains(event.target) && event.target != mobileMenuBtn) {
            mobileMenu.classList.add('hidden');
        }

        if (notifications_list_wrapper && notifications_list_wrapper.classList.contains('active')) {
            if(
                event.target != notifications &&
                event.target != notifications_icon &&
                event.target != notifications_icon_svg &&
                (!notifications_icon_svg.contains(event.target)) &&
                event.target != notifications_load_more &&
                (!notifications_list_wrapper.contains(event.target))
            ) {
                notifications_list_wrapper.classList.remove('active');
            }
        }

        if (userDropdownList && userDropdownList.classList.contains('active')) {
            if (!userDropdownList.contains(event.target) && event.target !== userMenuBtn && !userMenuBtn.contains(event.target)) {
                userDropdownList.classList.remove('active');
                userMenuArrow.classList.remove('rotate-180');
            }
        }
    });

    // جلب الإشعارات
    let page = 1;
    const url = '{{ route("notifications.fetch") }}';
    async function fetchNotifications() {
        if(!notifications_list) return;
        try {
            let res = await fetch(url + `?page=${page}`);
            let notification_item = await res.json();
            
            notifications_list.insertAdjacentHTML('beforeend', notification_item.html);
            if(notification_item.hasMorePages == false && notifications_load_more) {
                notifications_load_more.remove();
            }
        } catch(err) {
            console.error('Error in fetching notifications list', err);
        }  
    }
    fetchNotifications();

    if(notifications_load_more) {
        notifications_load_more.addEventListener('click', function(e) {
            e.stopPropagation();
            page++;
            fetchNotifications();
        });
    }
</script>
@endpush