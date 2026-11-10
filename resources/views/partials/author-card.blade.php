@props([
    'user',
    'label' => __('Written By'),
    'bgClass' => 'bg-slate-900/60 dark:bg-slate-800 border border-slate-800 dark:border-slate-700',
])

@if($user)
    <div class="{{ $bgClass }} rounded-2xl p-4 mb-8 flex items-center justify-between gap-4 shadow-sm">
        <div class="flex items-center gap-3">
            <!-- صورة البروفايل أو الشارة البديلة -->
            <a href="{{ route('authors.show', $user->id) }}" class="flex-shrink-0">
                @if($user->hasMedia('avatar_imgs'))
                    <img src="{{ $user->getFirstMediaUrl('avatar_imgs', 'thumb') }}" 
                         alt="{{ $user->name }}" 
                         class="w-11 h-11 rounded-full object-cover border border-red-600/40 shadow-sm" />
                @else
                    <div class="w-11 h-11 rounded-full bg-red-950/80 border border-red-800/60 flex items-center justify-center text-red-400 font-bold text-sm shadow-sm">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
            </a>

            <!-- تفاصيل الكاتب -->
            <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 block">{{ $label }}</span>
                <a href="{{ route('authors.show', $user->id) }}" class="text-xs sm:text-sm font-bold text-slate-200 hover:text-red-500 transition">
                    {{ $user->name }}
                </a>
            </div>
        </div>

        <!-- زر المتابعة الموحد -->
        @auth
            @if(auth()->id() !== $user->id) 
                @php
                    $isFollowingAuthor = auth()->user()->followingAuthors->contains($user->id);
                @endphp

                <button type="button" 
                        id="follow-author-btn-{{ $user->id }}"
                        onclick="toggleFollowAuthor(event, {{ $user->id }})"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-xl transition duration-200 border
                        {{ $isFollowingAuthor 
                            ? 'bg-rose-950/80 text-rose-400 border-rose-800/60' 
                            : 'bg-slate-800 text-gray-300 border-slate-700 hover:text-white' 
                        }}">
                    <span id="follow-author-text-{{ $user->id }}">
                        {{ $isFollowingAuthor ? __('Following') : __('Follow Author') }}
                    </span>
                </button>
            @endif
        @endauth
    </div>
@endif