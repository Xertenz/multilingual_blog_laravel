{{-- resources/views/admin/partials/header.blade.php --}}
<header class="bg-white border-b border-slate-200 px-8 py-4">
    <div class="flex justify-between items-center">
        
        {{-- Breadcrumb (يظهر المسار الحالي) --}}
        <div class="text-sm breadcrumbs">
            <span class="text-slate-400">Admin</span>
            <span class="mx-2 text-slate-400">/</span>
            <span class="text-slate-600 font-medium">@yield('page-title', 'Dashboard')</span>
        </div>
        
        {{-- Right Side Actions --}}
        <div class="flex items-center space-x-4">
            {{-- Search --}}
            <div class="relative">
                <input type="text" 
                       placeholder="Search..." 
                       class="w-64 pl-10 pr-4 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            
            {{-- Notifications --}}
            <button class="relative p-2 text-slate-600 hover:bg-slate-100 rounded-lg transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
                <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
            </button>
        </div>
    </div>
</header>