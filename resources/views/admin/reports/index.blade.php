{{-- resources/views/admin/reports/index.blade.php --}}
@extends('layouts.admin.master')

@section('title', 'Reports Moderation')

@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }

        .custom-scrollbar::-webkit-scrollbar {
            height: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
@endpush

@section('content')
<div class="container mx-auto pb-8 px-4">

    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Reports Moderation</h1>
            <p class="text-sm text-slate-500">Review user reports, change status, remove violating content, or issue penalties</p>
        </div>
    </div>

    @if($errors->any())
        <div class="mb-4 p-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl text-xs">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Reports Table Container --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse min-w-[920px]">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        <th class="py-3 px-6 w-44">Reporter</th>
                        <th class="py-3 px-6 w-36">Target Type</th>
                        <th class="py-3 px-6">Reason & Details</th>
                        <th class="py-3 px-6 text-center w-32">Status</th>
                        <th class="py-3 px-6 w-36">Date</th>
                        <th class="py-3 px-6 text-right w-56">Actions</th>
                        <th class="py-3 px-6 text-right w-36">Penalty</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($reports as $report)
                        @php
                            $statusValue = $report->status instanceof \App\Enums\ReportStatus
                                ? $report->status->value
                                : (string) $report->status;

                            $reasonLabel = $report->reason_type instanceof \App\Enums\ReportReason
                                ? $report->reason_type->label()
                                : (string) $report->reason_type;

                            $targetUser = method_exists($report, 'targetUser') ? $report->targetUser() : null;
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3.5 px-6 text-xs whitespace-nowrap">
                                <span class="font-semibold text-slate-700 block truncate max-w-[150px]">
                                    {{ $report->reporter?->name ?? 'Deleted User' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-6 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-200 inline-block">
                                    {{ class_basename($report->reportable_type) }}
                                </span>
                            </td>
                            <td class="py-3.5 px-6 text-xs max-w-xs">
                                <span class="font-semibold text-slate-800">{{ $reasonLabel }}</span>
                                <p class="text-slate-500 line-clamp-1 mt-0.5" title="{{ $report->details }}">
                                    {{ $report->details ?? 'No additional details' }}
                                </p>
                            </td>
                            <td class="py-3.5 px-6 text-center whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-md text-xs font-semibold border inline-block
                                    {{ $statusValue === \App\Enums\ReportStatus::PENDING->value ? 'bg-amber-50 text-amber-700 border-amber-200' : '' }}
                                    {{ $statusValue === \App\Enums\ReportStatus::RESOLVED->value ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : '' }}
                                    {{ $statusValue === \App\Enums\ReportStatus::DISMISSED->value ? 'bg-slate-100 text-slate-600 border-slate-200' : '' }}">
                                    {{ $report->status instanceof \App\Enums\ReportStatus ? $report->status->label() : ucfirst($statusValue) }}
                                </span>
                            </td>
                            <td class="py-3.5 px-6 text-xs text-slate-500 whitespace-nowrap">
                                {{ $report->created_at?->diffForHumans() }}
                            </td>
                            <td class="py-3.5 px-6 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    {{-- Update Report Status --}}
                                    <form action="{{ route('admin.reports.update-status', $report->id) }}" method="POST" class="inline-flex items-center">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" onchange="this.form.submit()" class="bg-slate-50 border border-slate-200 text-slate-700 text-xs rounded-lg px-2 py-1 focus:outline-none focus:border-indigo-500 cursor-pointer">
                                            @foreach(\App\Enums\ReportStatus::cases() as $statusCase)
                                                <option value="{{ $statusCase->value }}" {{ $statusValue === $statusCase->value ? 'selected' : '' }}>
                                                    {{ $statusCase->label() }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>

                                    {{-- Delete Reported Content --}}
                                    @if($report->reportable)
                                        <form action="{{ route('admin.reports.destroy-content', $report->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this reported content?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-rose-50 text-rose-600 hover:bg-rose-100 border border-rose-200 px-2.5 py-1 rounded-lg text-xs font-medium transition-colors">
                                                Delete Content
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3.5 px-6 text-right whitespace-nowrap">
                                @if($targetUser)
                                    <button type="button"
                                            data-user-id="{{ $targetUser->id }}"
                                            data-user-name="{{ $targetUser->name }}"
                                            onclick="openPenaltyModalFromButton(this)"
                                            class="bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200 px-2.5 py-1 rounded-lg text-xs font-medium transition-colors">
                                        Issue Penalty
                                    </button>
                                @else
                                    <span class="text-xs text-slate-400">N/A</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center">
                                    <span class="text-3xl mb-2">🛡️</span>
                                    <p class="text-sm font-medium text-slate-600">No reports found.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $reports->links() }}
    </div>
</div>

{{-- Penalty Modal --}}
<div id="penalty-modal" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white border border-slate-200 w-full max-w-lg rounded-2xl p-6 shadow-xl relative text-left">
        <div class="flex justify-between items-center mb-4 pb-3 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-800">
                Issue Penalty to User: <span id="penalty-username" class="text-rose-600"></span>
            </h3>
            <button type="button" onclick="closePenaltyModal()" class="text-slate-400 hover:text-slate-700 text-lg leading-none">&times;</button>
        </div>

        <form id="penalty-form" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Penalty Level</label>
                <select name="level" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-700 text-xs focus:outline-none focus:border-indigo-500" required>
                    <option value="1">Level 1: Warning (No temporary ban)</option>
                    <option value="2">Level 2: Temporary Ban (24 hours)</option>
                    <option value="3">Level 3: Temporary Ban (7 days)</option>
                    <option value="4">Level 4: Temporary Ban (30 days)</option>
                    <option value="5">Level 5: Permanent Ban</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Reason / Violation Details</label>
                <textarea name="reason" rows="3" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-700 text-xs focus:outline-none focus:border-indigo-500" placeholder="Enter a clear reason for this penalty..." required></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="closePenaltyModal()" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-xl text-xs font-semibold hover:bg-slate-200 transition-colors">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-rose-600 text-white rounded-xl text-xs font-semibold hover:bg-rose-700 transition-colors">
                    Confirm Penalty
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
window.addEventListener('DOMContentLoaded', function() {
    @if(session('success'))
        if (typeof showToast === 'function') {
            showToast('success', @json(session('success')));
        }
    @endif
});

function openPenaltyModalFromButton(button) {
    const userId = button.dataset.userId;
    const userName = button.dataset.userName;

    document.getElementById('penalty-username').innerText = userName;
    document.getElementById('penalty-form').action = `/admin/users/${userId}/penalties`;
    document.getElementById('penalty-modal').classList.remove('hidden');
}

function closePenaltyModal() {
    document.getElementById('penalty-modal').classList.add('hidden');
}
</script>
@endpush