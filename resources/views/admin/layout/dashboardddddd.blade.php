<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Custom scrollbar for sidebar */
        .sidebar-scroll::-webkit-scrollbar {
            width: 4px;
        }
        .sidebar-scroll::-webkit-scrollbar-track {
            background: #1e293b;
        }
        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: #475569;
            border-radius: 4px;
        }
        .sidebar-scroll::-webkit-scrollbar-thumb:hover {
            background: #64748b;
        }
        
        /* Smooth transitions */
        .nav-item {
            transition: all 0.2s ease;
        }
        
        /* Active link indicator */
        .active-link {
            position: relative;
        }
        .active-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 3px;
            background: linear-gradient(to bottom, #3b82f6, #8b5cf6);
            border-radius: 0 3px 3px 0;
        }
    </style>
</head>
<body class="bg-slate-100">
    
    <!-- ===================================== -->
    <!-- ADMIN LAYOUT: Sidebar + Main Content -->
    <!-- ===================================== -->
    <div class="flex min-h-screen">
        
        <!-- ===================================== -->
        <!-- LEFT SIDEBAR (Navigation) -->
        <!-- ===================================== -->
        <aside class="w-72 bg-gradient-to-b from-slate-900 to-slate-800 text-white shadow-2xl fixed h-full overflow-hidden">
            
            <!-- Logo Area -->
            <div class="p-6 border-b border-slate-700">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">Admin Panel</h1>
                        <p class="text-xs text-slate-400 mt-0.5">Content Management</p>
                    </div>
                </div>
                
                <!-- Admin Info (optional) -->
                <div class="mt-4 flex items-center space-x-3 bg-slate-800/50 p-3 rounded-xl">
                    <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                        <span class="text-sm font-bold">JD</span>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium">John Doe</p>
                        <p class="text-xs text-slate-400">Administrator</p>
                    </div>
                    <button class="text-slate-400 hover:text-white transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Navigation Menu (Scrollable) -->
            <nav class="p-4 overflow-y-auto sidebar-scroll h-[calc(100%-160px)]">
                
                <!-- Main Navigation Heading -->
                <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-4 px-3">
                    Main Menu
                </div>
                
                <!-- Categories Section -->
                <div class="mb-6">
                    <div class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-2 px-3">
                        Categories
                    </div>
                    
                    <!-- Categories List -->
                    <ul class="space-y-1">
                        <!-- View Categories Link -->
                        <li>
                            <a href="/admin/categories" class="nav-item flex items-center px-3 py-2.5 rounded-lg hover:bg-slate-700/50 text-slate-300 hover:text-white transition-all duration-200 group">
                                <div class="w-8 h-8 bg-slate-800 group-hover:bg-blue-600/20 rounded-lg flex items-center justify-center mr-3 transition-all">
                                    <svg class="w-4 h-4 text-slate-400 group-hover:text-blue-400 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                                    </svg>
                                </div>
                                <span class="flex-1 text-sm font-medium">All Categories</span>
                                <span class="bg-slate-800 text-slate-300 text-xs px-2 py-1 rounded-full">12</span>
                            </a>
                        </li>
                        
                        <!-- Add Category Link -->
                        <li>
                            <a href="/admin/categories/create" class="nav-item flex items-center px-3 py-2.5 rounded-lg hover:bg-slate-700/50 text-slate-300 hover:text-white transition-all duration-200 group">
                                <div class="w-8 h-8 bg-slate-800 group-hover:bg-green-600/20 rounded-lg flex items-center justify-center mr-3 transition-all">
                                    <svg class="w-4 h-4 text-slate-400 group-hover:text-green-400 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                </div>
                                <span class="flex-1 text-sm font-medium">Add New Category</span>
                                <span class="bg-green-600/20 text-green-400 text-xs px-2 py-1 rounded-full">New</span>
                            </a>
                        </li>
                    </ul>
                    
                    <!-- Categories Quick Stats -->
                    <div class="mt-3 mx-3 p-3 bg-slate-800/30 rounded-lg">
                        <div class="flex justify-between text-xs">
                            <span class="text-slate-400">Total Categories</span>
                            <span class="text-white font-medium">24</span>
                        </div>
                        <div class="flex justify-between text-xs mt-1">
                            <span class="text-slate-400">Active</span>
                            <span class="text-green-400 font-medium">18</span>
                        </div>
                    </div>
                </div>
                
                <!-- Divider -->
                <div class="border-t border-slate-700 my-4"></div>
                
                <!-- Articles Section -->
                <div class="mb-6">
                    <div class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-2 px-3">
                        Articles
                    </div>
                    
                    <!-- Articles List -->
                    <ul class="space-y-1">
                        <!-- View Articles Link (Active State Example) -->
                        <li>
                            <a href="/admin/articles" class="nav-item active-link flex items-center px-3 py-2.5 rounded-lg bg-gradient-to-r from-blue-600/20 to-purple-600/20 text-white border-l-3 border-blue-500">
                                <div class="w-8 h-8 bg-blue-600/30 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                                    </svg>
                                </div>
                                <span class="flex-1 text-sm font-medium">All Articles</span>
                                <span class="bg-blue-600/30 text-blue-300 text-xs px-2 py-1 rounded-full">156</span>
                            </a>
                        </li>
                        
                        <!-- Add Article Link -->
                        <li>
                            <a href="/admin/articles/create" class="nav-item flex items-center px-3 py-2.5 rounded-lg hover:bg-slate-700/50 text-slate-300 hover:text-white transition-all duration-200 group">
                                <div class="w-8 h-8 bg-slate-800 group-hover:bg-purple-600/20 rounded-lg flex items-center justify-center mr-3 transition-all">
                                    <svg class="w-4 h-4 text-slate-400 group-hover:text-purple-400 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <span class="flex-1 text-sm font-medium">Write New Article</span>
                                <span class="bg-purple-600/20 text-purple-400 text-xs px-2 py-1 rounded-full">+</span>
                            </a>
                        </li>
                    </ul>
                    
                    <!-- Articles Quick Stats -->
                    <div class="mt-3 mx-3 p-3 bg-slate-800/30 rounded-lg">
                        <div class="flex justify-between text-xs mb-2">
                            <span class="text-slate-400">Published</span>
                            <span class="text-green-400 font-medium">142</span>
                        </div>
                        <div class="flex justify-between text-xs mb-2">
                            <span class="text-slate-400">Drafts</span>
                            <span class="text-yellow-400 font-medium">8</span>
                        </div>
                        <div class="flex justify-between text-xs">
                            <span class="text-slate-400">Pending Review</span>
                            <span class="text-orange-400 font-medium">6</span>
                        </div>
                    </div>
                </div>
                
                <!-- Additional Quick Links (Optional) -->
                <div class="border-t border-slate-700 my-4"></div>
                
                <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-4 px-3">
                    Quick Actions
                </div>
                
                <!-- Quick Action Buttons -->
                <div class="grid grid-cols-2 gap-2 px-3">
                    <button class="bg-slate-800 hover:bg-slate-700 p-3 rounded-lg text-center transition">
                        <svg class="w-4 h-4 text-blue-400 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <span class="text-xs">New Post</span>
                    </button>
                    <button class="bg-slate-800 hover:bg-slate-700 p-3 rounded-lg text-center transition">
                        <svg class="w-4 h-4 text-purple-400 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l5 5a2 2 0 01.586 1.414V19a2 2 0 01-2 2H7a3 3 0 01-3-3V6a3 3 0 013-3z"></path>
                        </svg>
                        <span class="text-xs">Category</span>
                    </button>
                </div>
            </nav>
            
            <!-- Footer Links -->
            <div class="absolute bottom-0 left-0 right-0 p-4 bg-slate-900 border-t border-slate-800">
                <div class="flex justify-around text-xs text-slate-500">
                    <a href="#" class="hover:text-slate-300 transition">Settings</a>
                    <span>|</span>
                    <a href="#" class="hover:text-slate-300 transition">Help</a>
                    <span>|</span>
                    <a href="#" class="hover:text-red-400 transition">Logout</a>
                </div>
            </div>
        </aside>
        
        <!-- ===================================== -->
        <!-- RIGHT SIDEBAR (Additional Info Panel) - You requested right side for pages -->
        <!-- ===================================== -->
        <div class="ml-72 flex-1 p-8">
            
            <!-- Header with Right Sidebar Toggle -->
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-2xl font-bold text-slate-800">Dashboard</h2>
                
                <div class="flex items-center space-x-4">
                    <!-- Search Bar -->
                    <div class="relative">
                        <input type="text" 
                               placeholder="Search..." 
                               class="w-64 pl-10 pr-4 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    
                    <!-- Notifications -->
                    <button class="relative p-2 text-slate-600 hover:bg-slate-100 rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                    </button>
                </div>
            </div>
            
            <!-- Main Content Area (Your pages go here) -->
            <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-200 min-h-[600px]">
                <!-- Your page content will be injected here -->
                <div class="text-center text-slate-400 py-20">
                    <svg class="w-16 h-16 mx-auto mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="text-lg">Select an option from the sidebar to get started</p>
                    <p class="text-sm mt-2">Categories | Add Category | Articles | Add Article</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>