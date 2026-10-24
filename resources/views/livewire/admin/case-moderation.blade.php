<div class="max-w-7xl mx-auto px-4 py-8 text-white">
    
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 pb-4 border-b border-slate-800 gap-4">
        <div>
            <h1 class="text-2xl font-bold flex items-center gap-2">
                🛡️ مراجعة قضايا لوحة المحققين
            </h1>
            <p class="text-xs text-gray-400 mt-1">إدارة واعتماد القضايا المرفوعة من أعضاء المجتمع.</p>
        </div>

        <!-- تبويبات الفلترة حسب حالة المراجعة -->
        <div class="flex items-center gap-2 bg-slate-900 p-1 rounded-xl border border-slate-800 text-xs">
            <button wire:click="$set('filter', 'pending')" 
                    class="px-4 py-2 rounded-lg font-semibold transition {{ $filter === 'pending' ? 'bg-amber-600 text-white' : 'text-gray-400 hover:text-white' }}">
                قيد الانتظار (Pending)
            </button>
            <button wire:click="$set('filter', 'approved')" 
                    class="px-4 py-2 rounded-lg font-semibold transition {{ $filter === 'approved' ? 'bg-emerald-600 text-white' : 'text-gray-400 hover:text-white' }}">
                المقبولة (Approved)
            </button>
            <button wire:click="$set('filter', 'rejected')" 
                    class="px-4 py-2 rounded-lg font-semibold transition {{ $filter === 'rejected' ? 'bg-rose-600 text-white' : 'text-gray-400 hover:text-white' }}">
                المرفوضة (Rejected)
            </button>
        </div>
    </div>

    <!-- رسائل النجاح -->
    @if (session()->has('message'))
        <div class="mb-6 p-3 bg-emerald-950/80 border border-emerald-800 text-emerald-400 rounded-xl text-xs">
            {{ session('message') }}
        </div>
    @endif

    <!-- جدول عرض القضايا -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
        <table class="w-full text-right text-xs">
            <thead class="bg-slate-950/80 text-gray-400 uppercase border-b border-slate-800">
                <tr>
                    <th class="px-4 py-3">القضية</th>
                    <th class="px-4 py-3">الفئة</th>
                    <th class="px-4 py-3">الناشر</th>
                    <th class="px-4 py-3">التاريخ</th>
                    <th class="px-4 py-3 text-center">الإجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60">
                @forelse($cases as $case)
                    @php
                        $catTrans = $case->category?->translations->where('locale', app()->getLocale())->first() 
                                 ?? $case->category?->translations->first();
                    @endphp
                    <tr class="hover:bg-slate-800/40 transition">
                        <!-- العنوان والتفاصيل السريعة -->
                        <td class="px-4 py-4 max-w-md">
                            <h4 class="font-bold text-slate-100 text-sm mb-1 line-clamp-1">{{ $case->title }}</h4>
                            <p class="text-gray-400 text-xs line-clamp-2">{{ Str::limit(strip_tags($case->body), 100) }}</p>
                        </td>

                        <!-- الفئة -->
                        <td class="px-4 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 rounded bg-slate-800 text-red-400 border border-slate-700/50">
                                {{ $catTrans->name ?? 'General' }}
                            </span>
                        </td>

                        <!-- الكاتب -->
                        <td class="px-4 py-4 whitespace-nowrap font-medium text-slate-300">
                            {{ $case->user->name }}
                        </td>

                        <!-- التاريخ -->
                        <td class="px-4 py-4 whitespace-nowrap text-gray-500 font-mono">
                            {{ $case->created_at->diffForHumans() }}
                        </td>

                        <!-- أزرار الإجراءات -->
                        <td class="px-4 py-4 whitespace-nowrap text-center">
                            <div class="flex items-center justify-center gap-2">
                                @if($case->moderation_status !== 'approved')
                                    <button wire:click="approveCase({{ $case->id }})" 
                                            class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg font-bold transition">
                                        قبول ✓
                                    </button>
                                @endif

                                @if($case->moderation_status !== 'rejected')
                                    <button wire:click="rejectCase({{ $case->id }})" 
                                            wire:confirm="هل أنت تأكد من رفض هذه القضية؟"
                                            class="px-3 py-1.5 bg-rose-600/80 hover:bg-rose-600 text-white rounded-lg font-bold transition">
                                        رفض ✕
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-gray-500">
                            لا توجد قضايا تنطبق عليها هذه الحالة حالياً.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $cases->links() }}
    </div>

</div>