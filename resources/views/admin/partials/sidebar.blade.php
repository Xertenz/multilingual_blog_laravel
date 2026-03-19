{{-- resources/views/admin/partials/sidebar.blade.php --}}
<aside class="w-72 bg-gradient-to-b from-slate-900 to-slate-800 text-white fixed h-full overflow-y-auto">
    
    {{-- Logo --}}
    <div class="p-6 border-b border-slate-700">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
            </div>
            <div>
                <h1 class="text-xl font-bold bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                    Admin Panel
                </h1>
                <p class="text-xs text-slate-400 mt-0.5">Content Management</p>
            </div>
        </div>
        
        {{-- Admin Info --}}
        <div class="mt-4 flex items-center space-x-3 bg-slate-800/50 p-3 rounded-xl">
            <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                <span class="text-sm font-bold">{{ auth()->user()->initials ?? 'JD' }}</span>
            </div>
            <div class="flex-1">
                <p class="text-sm font-medium">{{ auth()->user()->name ?? 'John Doe' }}</p>
                <p class="text-xs text-slate-400">{{ auth()->user()->role ?? 'Administrator' }}</p>
            </div>
        </div>
    </div>
    
    {{-- Navigation --}}
    <nav class="p-4">
        {{-- Main Heading --}}
        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-4 px-3">
            Main Menu
        </div>
        
        {{-- Categories Section --}}
        <div class="mb-6">
            <div class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-2 px-3">
                Categories
            </div>
            
            <ul class="space-y-1">
                {{-- All Categories Link --}}
                <li>
                    <a href="{{ route('admin.categories.index') }}" 
                       class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 
                              {{ request()->routeIs('admin.categories.*') && !request()->routeIs('admin.categories.create') 
                                 ? 'bg-gradient-to-r from-blue-600/20 to-purple-600/20 text-white' 
                                 : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                        <div class="w-8 h-8 bg-slate-800 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                            </svg>
                        </div>
                        <span class="flex-1 text-sm font-medium">All Categories</span>
                        <span class="bg-slate-800 text-slate-300 text-xs px-2 py-1 rounded-full">
                            {{ \App\Models\Category::count() }}
                        </span>
                    </a>
                </li>
                
                {{-- Add Category Link --}}
                <li>
                    <a href="{{ route('admin.categories.create') }}" 
                       class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200
                              {{ request()->routeIs('admin.categories.create') 
                                 ? 'bg-gradient-to-r from-green-600/20 to-emerald-600/20 text-white' 
                                 : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                        <div class="w-8 h-8 bg-slate-800 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                        </div>
                        <span class="flex-1 text-sm font-medium">Add New Category</span>
                        @if(request()->routeIs('admin.categories.create'))
                            <span class="bg-green-600/20 text-green-400 text-xs px-2 py-1 rounded-full">Now</span>
                        @endif
                    </a>
                </li>
            </ul>
        </div>
        
        {{-- Divider --}}
        <div class="border-t border-slate-700 my-4"></div>
        
        {{-- Articles Section --}}
        <div class="mb-6">
            <div class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-2 px-3">
                Articles
            </div>
            
            <ul class="space-y-1">
                {{-- All Articles Link --}}
                <li>
                    <a href="{{ route('admin.articles.index') }}" 
                       class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200
                              {{ request()->routeIs('admin.articles.index') 
                                 ? 'bg-gradient-to-r from-blue-600/20 to-purple-600/20 text-white' 
                                 : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                        <div class="w-8 h-8 bg-slate-800 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                            </svg>
                        </div>
                        <span class="flex-1 text-sm font-medium">All Articles</span>
                        <span class="bg-slate-800 text-slate-300 text-xs px-2 py-1 rounded-full">
                            {{ \App\Models\Article::count() }}
                        </span>
                    </a>
                </li>
                
                {{-- Add Article Link --}}
                <li>
                    <a href="{{ route('admin.articles.create') }}" 
                       class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200
                              {{ request()->routeIs('admin.articles.create') 
                                 ? 'bg-gradient-to-r from-purple-600/20 to-pink-600/20 text-white' 
                                 : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                        <div class="w-8 h-8 bg-slate-800 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <span class="flex-1 text-sm font-medium">Write New Article</span>
                        <span class="bg-purple-600/20 text-purple-400 text-xs px-2 py-1 rounded-full">+</span>
                    </a>
                </li>
            </ul>
        </div>
        
        {{-- Logout --}}
        <div class="border-t border-slate-700 mt-6 pt-4">
            <form method="POST" action="{{route('logout')}}">
                @csrf
                <button type="submit" class="w-full flex items-center px-3 py-2.5 text-slate-300 hover:bg-red-600/20 hover:text-red-400 rounded-lg transition">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    <span class="text-sm font-medium">Logout</span>
                </button>
            </form>
        </div>
    </nav>
</aside>