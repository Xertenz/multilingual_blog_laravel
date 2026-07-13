<div class="flex border-b border-gray-200 mb-6 bg-white p-2 rounded-xl shadow-sm gap-2">
    <a href="{{ route('profile.favorite-articles') }}" 
        class="px-4 py-2 text-sm font-semibold rounded-lg transition-colors duration-150
        {{ request()->routeIs('profile.favorite-articles') 
            ? 'bg-indigo-55 text-indigo-600' 
            : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' 
        }}">
        {{ __('Favorite Articles') }}
    </a>

    <a href="{{ route('profile.favorites') }}" 
       class="px-4 py-2 text-sm font-semibold rounded-lg transition-colors duration-150 rtl:space-x-reverse 
       {{ request()->routeIs('profile.favorites') 
           ? 'bg-indigo-55 text-indigo-600' 
           : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' 
       }}">
        {{ __('Favorite Categories') }}
    </a>

    <a href="{{ route('profile.edit') }}" 
       class="px-4 py-2 text-sm font-semibold rounded-lg transition-colors duration-150 rtl:space-x-reverse
       {{ request()->routeIs('profile.edit') 
           ? 'bg-indigo-55 text-indigo-600' 
           : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' 
       }}">
        {{ __('Account Settings') }}
    </a>

</div>