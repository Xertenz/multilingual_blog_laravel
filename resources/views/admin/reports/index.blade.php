{{-- resources/views/admin/reports/index.blade.php --}}
@extends('layouts.admin.master') {{-- أو التخطيط المعتمد للوحة التحكم لديك --}}

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-white">إدارة البلاغات (Reports Moderation)</h1>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-emerald-900/50 border border-emerald-700 text-emerald-300 rounded-xl text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
        <table class="w-full text-right border-collapse">
            <thead>
                <tr class="bg-slate-800/60 text-gray-400 text-xs uppercase tracking-wider border-b border-slate-800">
                    <th class="p-4">المُبْلِغ</th>
                    <th class="p-4">نوع العنصر</th>
                    <th class="p-4">سبب البلاغ</th>
                    <th class="p-4">الحالة</th>
                    <th class="p-4">التاريخ</th>
                    <th class="p-4 text-center">الإجراءات</th>
                    <th class="p-4">العقوبة</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800 text-sm text-gray-300">
                @forelse($reports as $report)
                    <tr class="hover:bg-slate-800/40 transition">
                        <td class="p-4 font-medium text-white">{{ $report->reporter->name ?? 'مستخدم محذوف' }}</td>
                        <td class="p-4">
                            <span class="px-2.5 py-1 bg-slate-800 text-red-400 rounded-md text-xs border border-slate-700">
                                {{ class_basename($report->reportable_type) }}
                            </span>
                        </td>
                        <td class="p-4 text-gray-400 max-w-xs truncate" title="{{ $report->details }}">
                            <span class="font-semibold text-white">{{ $report->reason_type }}</span>: 
                            {{ $report->details ?? 'بدون تفاصيل إضافية' }}
                        </td>
                        <td class="p-4">
                            @if($report->status === 'pending')
                                <span class="px-2.5 py-1 bg-amber-500/20 text-amber-400 rounded-full text-xs font-semibold">قيد المراجعة</span>
                            @elseif($report->status === 'resolved')
                                <span class="px-2.5 py-1 bg-emerald-500/20 text-emerald-400 rounded-full text-xs font-semibold">تم التعامل معه</span>
                            @else
                                <span class="px-2.5 py-1 bg-slate-700 text-gray-400 rounded-full text-xs font-semibold">مرفوض</span>
                            @endif
                        </td>
                        <td class="p-4 text-xs text-gray-500">{{ $report->created_at->diffForHumans() }}</td>
                        <td class="p-4 text-center space-x-2 space-x-reverse">
                            {{-- نموذج تغيير الحالة --}}
                            <form action="{{ route('admin.reports.update-status', $report->id) }}" method="POST" class="inline-block">
                                @csrf
                                @method('PATCH')
                                <select name="status" onchange="this.form.submit()" class="bg-slate-800 border border-slate-700 text-xs text-white rounded-lg px-2 py-1 focus:outline-none">
                                    <option value="pending" {{ $report->status == 'pending' ? 'selected' : '' }}>قيد المراجعة</option>
                                    <option value="resolved" {{ $report->status == 'resolved' ? 'selected' : '' }}>تم الحل</option>
                                    <option value="dismissed" {{ $report->status == 'dismissed' ? 'selected' : '' }}>رفض</option>
                                </select>
                            </form>

                            {{-- زر حذف المحتوى المبلغ عنه إن وجد --}}
                            @if($report->reportable)
                                <form action="{{ route('admin.reports.destroy-content', $report->id) }}" method="POST" class="inline-block" onsubmit="return confirm('هل أنت متأكد من حذف المحتوى المخالف؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1 bg-red-600/20 hover:bg-red-600 text-red-400 hover:text-white rounded-lg text-xs transition border border-red-800/50">
                                        حذف المحتوى
                                    </button>
                                </form>
                            @endif
                        </td>
                        


                        <td>
                            @php
                                $item = $report->reportable;
                                $hasMethod = $item ? method_exists($item, 'user') : false;
                                $hasUser = ($hasMethod && $item->user) ? true : false;
                                
                                // طبع القيم على الشاشة للاختبار فقط
                                // echo "Item: " . get_class($item) . " | Has Method: " . ($hasMethod ? 'Yes' : 'No') . " | Has User: " . ($hasUser ? 'Yes' : 'No');
                            @endphp

                            @if($item && $hasUser)
                                <button onclick="openPenaltyModal({{ $item->user->id }}, '{{ $item->user->name }}')" 
                                        class="px-3 py-1 bg-amber-600/20 hover:bg-amber-600 text-amber-400 hover:text-white rounded-lg text-xs transition border border-amber-800/50">
                                    فرض عقوبة
                                </button>
                            @else
                                <span class="text-xs text-slate-500">لا يمكن فرض عقوبة</span>
                            @endif
                        </td>



                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-gray-500">لا توجد بلاغات مسجلة حتى الآن.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $reports->links() }}
    </div>
</div>



<!-- Modal فرض العقوبة -->
<div id="penalty-modal" class="hidden fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-slate-900 border border-slate-800 w-full max-w-lg rounded-2xl p-6 shadow-2xl relative text-right" dir="rtl">
        <div class="flex justify-between items-center mb-4 pb-3 border-b border-slate-800">
            <h3 class="text-lg font-bold text-white">فرض عقوبة على المستخدم: <span id="penalty-username" class="text-red-500"></span></h3>
            <button onclick="closePenaltyModal()" class="text-gray-400 hover:text-white">✕</button>
        </div>

        <form id="penalty-form" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-gray-400 mb-1">درجة العقوبة (Penalty Level)</label>
                <select name="level" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-white text-sm focus:outline-none focus:border-red-600" required>
                    <option value="1">الدرجة 1: تنبيه وتوجيه (Warning - بدون حظر زمني)</option>
                    <option value="2">الدرجة 2: حظر مؤقت لمدة 24 ساعة</option>
                    <option value="3">الدرجة 3: حظر مؤقت لمدة أسبوع</option>
                    <option value="4">الدرجة 4: حظر مؤقت لمدة شهر</option>
                    <option value="5">الدرجة 5: حظر نهائي دائم</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-400 mb-1">سبب العقوبة / المخالفة</label>
                <textarea name="reason" rows="3" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-white text-sm focus:outline-none focus:border-red-600" placeholder="اكتب سبب العقوبة بوضوح ليظهر للمستخدم..." required></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-slate-800">
                <button type="button" onclick="closePenaltyModal()" class="px-4 py-2 bg-slate-800 text-gray-300 rounded-xl text-xs font-semibold hover:bg-slate-700">إلغاء</button>
                <button type="submit" class="px-5 py-2 bg-red-600 text-white rounded-xl text-xs font-semibold hover:bg-red-700 shadow-lg shadow-red-600/20">تأكيد وفرض العقوبة</button>
            </div>
        </form>
    </div>
</div>




@endsection


@push('scripts')
    
<script>
function openPenaltyModal(userId, userName) {
    document.getElementById('penalty-username').innerText = userName;
    // توجيه الفورم تلقائياً إلى مسار العقوبات الخاص بهذا المستخدم
    document.getElementById('penalty-form').action = `/admin/users/${userId}/penalties`;
    document.getElementById('penalty-modal').classList.remove('hidden');
}

function closePenaltyModal() {
    document.getElementById('penalty-modal').classList.add('hidden');
}
</script>
@endpush