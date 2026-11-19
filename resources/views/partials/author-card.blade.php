@props([
    'uploader',
    'label' => __('Written By'),
    'bgClass' => 'bg-slate-900/60 dark:bg-slate-800 border border-slate-800 dark:border-slate-700',
])

@if($uploader)
    <div class="{{ $bgClass }} rounded-2xl p-4 mb-8 flex items-center justify-between gap-4 shadow-sm">
        <div class="flex items-center gap-3">
            <!-- صورة البروفايل أو الشارة البديلة -->
            <a href="{{ route('authors.show', $uploader->id) }}" class="flex-shrink-0">
                @if($uploader->hasMedia('avatar_imgs'))
                    <img src="{{ $uploader->getFirstMediaUrl('avatar_imgs', 'thumb') }}"
                         alt="{{ $uploader->name }}"
                         class="w-11 h-11 rounded-full object-cover border border-red-600/40 shadow-sm" />
                @else
                    <div class="w-11 h-11 rounded-full bg-red-950/80 border border-red-800/60 flex items-center justify-center text-red-400 font-bold text-sm shadow-sm">
                        {{ strtoupper(substr($uploader->name, 0, 1)) }}
                    </div>
                @endif
            </a>

            <!-- تفاصيل الكاتب -->
            <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 block">{{ $label }}</span>
                <a href="{{ route('authors.show', $uploader->id) }}" class="text-xs sm:text-sm font-bold text-slate-200 hover:text-red-500 transition">
                    {{ $uploader->name }}
                </a>
            </div>
        </div>

        <!-- زر المتابعة الموحد -->
        @auth
            @if(auth()->id() !== $uploader->id)
                <x-follow-button type="author" :model="$uploader" />
            @endif
        @endauth
    </div>
@endif