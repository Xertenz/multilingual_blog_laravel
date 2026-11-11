@props([
    'threshold' => 300,        // ارتفاع التمرير (px) قبل ظهور الزر
    'position'  => 'right',    // right | left (يُعكس تلقائياً في RTL)
    'color'     => 'dark',     // dark | red
])

@php
    // تحديد الموضع مع دعم RTL
    $positionClasses = $position === 'left'
        ? 'left-6 rtl:left-auto rtl:right-6'
        : 'right-6 rtl:right-auto rtl:left-6';

    // تحديد الألوان
    $colorClasses = $color === 'red'
        ? 'bg-red-600 hover:bg-red-700 text-white border border-red-500'
        : 'bg-[#0f1526] border border-slate-800 text-gray-400 hover:text-white hover:border-red-600/60';
@endphp

{{-- زر العودة إلى الأعلى --}}
<button id="scrollToTopBtn"
        type="button"
        title="{{ __('Back to top') }}"
        aria-label="{{ __('Back to top') }}"
        data-threshold="{{ $threshold }}"
        class="fixed bottom-6 {{ $positionClasses }} z-40
               w-11 h-11 rounded-full
               {{ $colorClasses }}
               shadow-lg shadow-black/40
               flex items-center justify-center
               opacity-0 invisible translate-y-2
               transition-all duration-300
               cursor-pointer">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5"/>
    </svg>
</button>

@once
    @push('scripts')
    <script>
        (function () {
            const btn = document.getElementById('scrollToTopBtn');
            if (!btn) return;

            const threshold = parseInt(btn.dataset.threshold, 10) || 300;
            const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

            function toggleScrollBtn() {
                if (window.scrollY > threshold) {
                    btn.classList.remove('opacity-0', 'invisible', 'translate-y-2');
                    btn.classList.add('opacity-100', 'visible', 'translate-y-0');
                } else {
                    btn.classList.add('opacity-0', 'invisible', 'translate-y-2');
                    btn.classList.remove('opacity-100', 'visible', 'translate-y-0');
                }
            }

            btn.addEventListener('click', function () {
                window.scrollTo({
                    top: 0,
                    behavior: prefersReducedMotion ? 'auto' : 'smooth'
                });
            });

            window.addEventListener('scroll', toggleScrollBtn, { passive: true });
            toggleScrollBtn();
        })();
    </script>
    @endpush
@endonce