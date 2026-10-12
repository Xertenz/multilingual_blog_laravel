<div class="flex items-center border border-slate-800 mb-6 bg-slate-900/90 backdrop-blur-md p-1.5 rounded-xl shadow-xl gap-1 overflow-x-auto">

    <a href="{{ route('profile.favorite-articles') }}" 
        class="flex-1 text-center px-4 py-2 text-xs font-semibold rounded-lg transition-all duration-150 whitespace-nowrap
        {{ request()->routeIs('profile.favorite-articles') 
            ? 'bg-red-950/60 text-red-400 border border-red-900/50 shadow-sm' 
            : 'text-slate-400 hover:text-white hover:bg-slate-800/60' 
        }}">
        {{ __('Saved Articles') }}
    </a>

    <a href="{{ route('profile.favorites') }}" 
       class="flex-1 text-center px-4 py-2 text-xs font-semibold rounded-lg transition-all duration-150 whitespace-nowrap
       {{ request()->routeIs('profile.favorites') 
           ? 'bg-red-950/60 text-red-400 border border-red-900/50 shadow-sm' 
           : 'text-slate-400 hover:text-white hover:bg-slate-800/60' 
       }}">
        {{ __('Followed Categories') }}
    </a>

    <a href="{{ route('profile.edit') }}" 
       class="flex-1 text-center px-4 py-2 text-xs font-semibold rounded-lg transition-all duration-150 whitespace-nowrap
       {{ request()->routeIs('profile.edit') 
           ? 'bg-red-950/60 text-red-400 border border-red-900/50 shadow-sm' 
           : 'text-slate-400 hover:text-white hover:bg-slate-800/60' 
       }}">
        {{ __('Account Settings') }}
    </a>

</div>