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
@endsection