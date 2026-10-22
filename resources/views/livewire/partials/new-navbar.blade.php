<?php

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\Category;
use App\Models\User;
use Illuminate\Notifications\DatabaseNotification;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

new class extends Component {
    public $cat_transes = [];
    public $notifications = [];
    public $hasMoreNotifications = false;
    public $notificationsPage = 1;
    public $unreadNotificationsCount = 0;

    public function mount()
    {
        $this->loadCategories();
        $this->loadNotificationsData();
    }

    public function loadCategories()
    {
        $current_locale = LaravelLocalization::getCurrentLocale();
        $cacheKey = "navbar_categories_{$current_locale}";

        $categories = Cache::remember($cacheKey, 3600, function () use ($current_locale) {
            return Category::with(['translations' => function ($query) use ($current_locale) {
                $query->where('locale', $current_locale);
            }])
                ->whereHas('translations', function ($query) use ($current_locale) {
                    $query->where('locale', $current_locale);
                })
                ->orderBy('label', 'asc')
                ->get();
        });

        $this->cat_transes = $categories->map(fn($category) => $category->translations->first())->filter();
    }

    public function loadNotificationsData()
    {
        if (!Auth::check()) {
            return;
        }

        $user = Auth::user();

        $this->unreadNotificationsCount = $user->notifications()
            ->where('created_at', '>', $user->notifications_last_read_at ?? '2000-01-01')
            ->count();

        $paginator = DatabaseNotification::where('notifiable_type', User::class)
            ->where('notifiable_id', $user->id)
            ->latest()
            ->paginate(5, ['*'], 'notif_page', $this->notificationsPage);

        $this->notifications = $paginator->items();
        $this->hasMoreNotifications = $paginator->hasMorePages();
    }

    public function loadMoreNotifications()
    {
        $this->notificationsPage++;
        
        $user_id = Auth::id();
        $paginator = DatabaseNotification::where('notifiable_type', User::class)
            ->where('notifiable_id', $user_id)
            ->latest()
            ->paginate(5, ['*'], 'notif_page', $this->notificationsPage);

        $this->notifications = array_merge($this->notifications, $paginator->items());
        $this->hasMoreNotifications = $paginator->hasMorePages();
    }

    public function markNotificationsAsRead()
    {
        if (!Auth::check()) return;

        $user = Auth::user();
        $user->update([
            'notifications_last_read_at' => now(),
        ]);

        $this->unreadNotificationsCount = 0;
    }
}

?>

<div>
    <!-- رفعنا نطاق x-data هنا لكي يشمل النافبار وقائمة الجوال معاً -->
    <div class="sticky top-0 z-40 w-full" x-data="{ mobileMenuOpen: false }">
        <nav class="bg-[#0a0f1f]/90 backdrop-blur-md border-b border-gray-800 transition-all duration-300">
            <div class="container mx-auto px-4">
                <div class="flex justify-between items-center h-16 gap-4">
                    
                    <!-- القسم الأيسر: الشعار والقائمة الأساسية -->
                    <div class="left flex items-center gap-4 lg:gap-6">
                        <!-- زر القائمة للجوال (Hamburger Menu) -->
                        <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden flex items-center justify-center p-2 rounded-lg bg-slate-950 border border-slate-800 text-gray-400 hover:text-white transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
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
                            <!-- قائمة منسدلة للتصنيفات في الـ Navbar -->
                            <div class="relative" x-data="{ open: false }" @mouseleave="open = false">
                                <button @mouseenter="open = true" @click="open = !open" class="flex items-center gap-1 text-gray-400 hover:text-red-500 hover:bg-slate-900/50 px-3 py-1.5 rounded-lg transition-all text-sm font-medium">
                                    <span>{{ __('nav.categories') }}</span>
                                    <svg class="w-3 h-3 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>

                                <!-- محتوى القائمة المنسدلة -->
                                <div x-show="open" 
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="opacity-100 scale-100"
                                     x-transition:leave-end="opacity-0 scale-95"
                                     class="absolute right-0 rtl:left-0 rtl:right-auto top-full pt-2 w-56 bg-[#0f1526] border border-slate-800 rounded-xl shadow-2xl shadow-black/90 z-50" x-cloak>
                                    <div class="p-2 space-y-1 max-h-64 overflow-y-auto">
                                        @forelse($cat_transes as $cat_trans)
                                            <a href="{{ route('categories.show', ['id' => $cat_trans->id, 'slug' => $cat_trans->slug]) }}" class="block px-3 py-2 text-xs text-gray-300 hover:bg-slate-800/80 hover:text-white rounded-lg transition truncate">
                                                {{ $cat_trans->name }}
                                            </a>
                                        @empty
                                            <span class="block px-3 py-2 text-xs text-gray-500 text-center">{{ __('No categories found') }}</span>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('suggestions.index') }}" class="flex items-center gap-1 text-gray-400 hover:text-red-500 hover:bg-slate-900/50 px-3 py-1.5 rounded-lg transition-all text-sm font-medium">
                            Suggestions
                        </a>
                    </div>


                    <!-- حقل البحث للشاشات المتوسطة والكبيرة -->
                    <div class="hidden md:block flex-1 max-w-xs lg:max-w-sm">
                        <form action="{{ route('search') }}" method="GET" class="relative">
                            <input 
                                type="text" 
                                name="q" 
                                value="{{ request('q') }}"
                                placeholder="{{ __('Search articles...') }}" 
                                class="w-full bg-slate-900/90 border border-slate-800 text-gray-200 placeholder-gray-500 text-xs rounded-md ltr:pl-9 ltr:pr-3 rtl:pr-9 rtl:pl-3 py-2 focus:outline-none focus:border-red-600/60 focus:ring-1 focus:ring-red-600/50 transition duration-200"
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

                            <!-- صندوق الإشعارات الذكي المستقل -->
                            <div class="notifications relative" x-data="{ open: false }" @click.outside="open = false">
                                <div @click="open = !open; if(open) { $wire.markNotificationsAsRead(); }" 
                                     class="notifications-icon bg-slate-900/80 border border-slate-800 hover:border-red-600/50 p-2 rounded-lg transition duration-200 cursor-pointer relative">
                                    <x-fas-bell class="w-3.5 h-3.5 text-gray-400 hover:text-white transition pointer-events-none" />
                                    
                                    @if($unreadNotificationsCount > 0)
                                        <span class="absolute -top-1 -right-1 bg-red-600 text-white text-[9px] font-bold w-4 h-4 rounded-full flex items-center justify-center shadow-md pointer-events-none">
                                            {{ $unreadNotificationsCount > 9 ? '9+' : $unreadNotificationsCount }}
                                        </span>
                                    @endif
                                </div>
                                
                                <div x-show="open"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 scale-95"
                                    x-transition:enter-end="opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 scale-100"
                                    x-transition:leave-end="opacity-0 scale-95"
                                    class="notifications-list-wrapper absolute right-0 rtl:left-0 rtl:right-auto top-[calc(100%+12px)] w-80 bg-[#0f1526] border border-slate-800 rounded-xl shadow-2xl shadow-black/90 select-none overflow-hidden z-50" x-cloak>
                                    
                                    <!-- رأس الصندوق -->
                                    <div class="px-4 py-2.5 bg-slate-900/80 border-b border-slate-800 flex justify-between items-center">
                                        <span class="text-xs font-bold text-gray-300 uppercase tracking-wider">Case Updates</span>
                                        <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                                    </div>

                                    <!-- الحاوي الرئيسي القابل للتمرير والذي يضم القائمة والزر معاً -->
                                    <div class="notifications-scroll-container max-h-[280px] overflow-y-auto divide-y divide-slate-800">
                                        <!-- القائمة -->
                                        <ul class="notifications-list flex flex-col">
                                            @forelse($notifications as $notification)
                                                @include('partials.notification_item', ['notification' => $notification])
                                            @empty
                                                <li class="px-4 py-6 text-center text-xs text-gray-500">لا توجد إشعارات جديدة</li>
                                            @endforelse
                                        </ul>
                                        
                                        <!-- زر التحميل أصبح يظهر هنا بنهاية المحتوى تماماً -->
                                        @if($hasMoreNotifications)
                                            <button wire:click="loadMoreNotifications" 
                                                    class="notifications-load-more bg-slate-900 hover:bg-red-900/25 text-gray-400 hover:text-red-400 text-xs uppercase w-full py-2.5 font-bold tracking-wider border-t border-slate-800 transition block">
                                                Load More Cases
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- قائمة حساب المستخدم المنسدلة المستقلة -->
                            <div class="user-menu-dropdown relative" x-data="{ open: false }" @click.outside="open = false">
                                <button @click="open = !open" class="flex items-center gap-2 bg-slate-900/80 border border-slate-800 hover:border-red-600/50 py-1.5 px-2.5 rounded-lg transition duration-200 cursor-pointer">
                                    <div class="w-6 h-6 rounded-full bg-red-950/80 border border-red-800/60 flex items-center justify-center text-red-400 font-bold text-xs pointer-events-none">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                    </div>
                                    <span class="text-xs font-medium text-gray-300 hidden sm:inline-block max-w-[100px] truncate pointer-events-none">{{ Auth::user()->name }}</span>
                                    <svg class="w-3 h-3 text-gray-400 transition-transform duration-200 pointer-events-none" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>

                                <div x-show="open"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="opacity-100 scale-100"
                                     x-transition:leave-end="opacity-0 scale-95"
                                     class="absolute right-0 rtl:left-0 rtl:right-auto top-[calc(100%+8px)] w-48 bg-[#0f1526] border border-slate-800 rounded-xl shadow-2xl shadow-black/90 overflow-hidden z-50" x-cloak>
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
                            <a href="{{ route('login') }}" class="text-xs font-semibold text-gray-300 hover:text-white bg-slate-900 border border-slate-800 hover:border-slate-700 px-3 py-1.5 rounded-md transition">
                                Log In
                            </a>
                            <a href="{{ route('register') }}" class="text-xs font-semibold text-white bg-red-600 hover:bg-red-700 px-3 py-1.5 rounded-md shadow-sm shadow-red-600/20 transition">
                                Sign Up
                            </a>
                        </div>
                        @endguest

                        <!-- محدد اللغة -->
                        <div class="relative flex-shrink-0 max-w-[110px] sm:max-w-none">
                            <select id="lang-switcher" class="lang-switcher bg-slate-900 border border-slate-800 text-gray-300 hover:border-slate-700 text-xs font-medium rounded-md pl-2.5 pr-7 py-1.5 focus:outline-none focus:ring-1 focus:ring-red-600/50 cursor-pointer capitalize appearance-none rtl:pl-7 rtl:pr-2.5" onchange="location=this.value;">
                                @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                                    <option value="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}" {{ app()->getLocale() == $localeCode ? 'selected' : '' }} class="bg-[#0a0f1f] text-gray-300">
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

            <!-- قائمة الجوال المنسدلة (Mobile Dropdown Menu) - تقع داخل النطاق وتعمل الآن بامتياز -->
            <div x-show="mobileMenuOpen" 
                 x-transition 
                 class="md:hidden border-t border-slate-800 bg-[#0a0f1f]/95 backdrop-blur-md px-4 py-3 space-y-3" x-cloak>
                <form action="{{ route('search') }}" method="GET" class="relative">
                    <input 
                        type="text" 
                        name="q" 
                        value="{{ request('q') }}"
                        placeholder="{{ __('Search articles...') }}" 
                        class="w-full bg-slate-900 border border-slate-800 text-gray-200 placeholder-gray-500 text-xs rounded-lg ltr:pl-9 ltr:pr-3 rtl:pr-9 rtl:pl-3 py-2 focus:outline-none focus:border-red-600/60 transition"
                        required
                    />
                    <button type="submit" class="absolute ltr:left-2.5 rtl:right-2.5 top-2.5 text-gray-500 hover:text-red-500 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                </form>

                <div class="space-y-1 pt-2">
                    @foreach($cat_transes as $cat_trans)
                        <a href="{{ route('categories.show', ['id' => $cat_trans->id, 'slug' => $cat_trans->slug]) }}" class="block text-gray-400 hover:text-red-500 hover:bg-slate-900/60 px-3 py-2 rounded-lg text-xs font-medium">
                            {{ $cat_trans->name }}
                        </a>
                    @endforeach
                </div>

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
        <div class="crime-scene-tape left-0 z-30 h-[3px] bg-yellow-500"></div>
    </div>
</div>

<style>
    .crime-scene-tape {
        background-image: linear-gradient(
            -45deg, 
            #eab308 25%, #000 25%, 
            #000 50%, #eab308 50%, 
            #eab308 75%, #000 75%, #000
        );
        background-size: 20px 20px;
    }
    [x-cloak] { display: none !important; }

    /* تنسيق شريط التمرير للصندوق بأكمله (القائمة + الزر) */
    .notifications-scroll-container::-webkit-scrollbar { 
        width: 4px; 
    }
    .notifications-scroll-container::-webkit-scrollbar-track { 
        background: #0f1526; 
    }
    .notifications-scroll-container::-webkit-scrollbar-thumb { 
        background: #1e293b; 
        border-radius: 4px; 
    }
    .notifications-scroll-container::-webkit-scrollbar-thumb:hover { 
        background: #dc2626; 
    }
</style>