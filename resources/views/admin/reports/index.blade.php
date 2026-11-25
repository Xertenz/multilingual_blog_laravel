{{-- resources/views/admin/reports/index.blade.php --}}
@extends('layouts.admin.master')

@section('title', 'Reports Moderation')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">Reports Moderation</h1>
            <p class="text-sm text-gray-400 mt-1">Review user reports, update statuses, remove violating content, or issue penalties.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-emerald-900/50 border border-emerald-700 text-emerald-300 rounded-xl text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 p-4 bg-rose-900/50 border border-rose-700 text-rose-300 rounded-xl text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[900px]">
                <thead>
                    <tr class="bg-slate-800/60 text-gray-400 text-xs uppercase tracking-wider border-b border-slate-800">
                        <th class="p-4">Reporter</th>
                        <th class="p-4">Target Type</th>
                        <th class="p-4">Reason & Details</th>
                        <th class="p-4">Status</th>
                        <th class="p-4">Date</th>
                        <th class="p-4 text-center">Actions</th>
                        <th class="p-4">Penalty</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800 text-sm text-gray-300">
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
                        <tr class="hover:bg-slate-800/40 transition">
                            <td class="p-4 font-medium text-white whitespace-nowrap">
                                {{ $report->reporter?->name ?? 'Deleted User' }}
                            </td>
                            <td class="p-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 bg-slate-800 text-red-400 rounded-md text-xs border border-slate-700">
                                    {{ class_basename($report->reportable_type) }}
                                </span>
                            </td>
                            <td class="p-4 text-gray-400 max-w-xs truncate" title="{{ $report->details }}">
                                <span class="font-semibold text-white">{{ $reasonLabel }}</span>:
                                {{ $report->details ?? 'No additional details' }}
                            </td>
                            <td class="p-4 whitespace-nowrap">
                                @if($statusValue === \App\Enums\ReportStatus::PENDING->value)
                                    <span class="px-2.5 py-1 bg-amber-500/20 text-amber-400 rounded-full text-xs font-semibold">
                                        Pending
                                    </span>
                                @elseif($statusValue === \App\Enums\ReportStatus::RESOLVED->value)
                                    <span class="px-2.5 py-1 bg-emerald-500/20 text-emerald-400 rounded-full text-xs font-semibold">
                                        Resolved
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 bg-slate-700 text-gray-400 rounded-full text-xs font-semibold">
                                        Dismissed
                                    </span>
                                @endif
                            </td>
                            <td class="p-4 text-xs text-gray-500 whitespace-nowrap">
                                {{ $report->created_at?->diffForHumans() }}
                            </td>
                            <td class="p-4 text-center whitespace-nowrap space-x-2">
                                {{-- Update Report Status --}}
                                <form action="{{ route('admin.reports.update-status', $report->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" onchange="this.form.submit()" class="bg-slate-800 border border-slate-700 text-xs text-white rounded-lg px-2 py-1 focus:outline-none">
                                        @foreach(\App\Enums\ReportStatus::cases() as $statusCase)
                                            <option value="{{ $statusCase->value }}" {{ $statusValue === $statusCase->value ? 'selected' : '' }}>
                                                {{ $statusCase->label() }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>

                                {{-- Delete Reported Content --}}
                                @if($report->reportable)
                                    <form action="{{ route('admin.reports.destroy-content', $report->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this reported content?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1 bg-red-600/20 hover:bg-red-600 text-red-400 hover:text-white rounded-lg text-xs transition border border-red-800/50">
                                            Delete Content
                                        </button>
                                    </form>
                                @endif
                            </td>
                            <td class="p-4 whitespace-nowrap">
                                @if($targetUser)
                                    <button type="button"
                                            data-user-id="{{ $targetUser->id }}"
                                            data-user-name="{{ $targetUser->name }}"
                                            onclick="openPenaltyModalFromButton(this)"
                                            class="px-3 py-1 bg-amber-600/20 hover:bg-amber-600 text-amber-400 hover:text-white rounded-lg text-xs transition border border-amber-800/50">
                                        Issue Penalty
                                    </button>
                                @else
                                    <span class="text-xs text-slate-500">N/A</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-gray-500">
                                No reports found yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $reports->links() }}
    </div>
</div>

<!-- Penalty Modal -->
<div id="penalty-modal" class="hidden fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-slate-900 border border-slate-800 w-full max-w-lg rounded-2xl p-6 shadow-2xl relative text-left">
        <div class="flex justify-between items-center mb-4 pb-3 border-b border-slate-800">
            <h3 class="text-lg font-bold text-white">Issue Penalty to User: <span id="penalty-username" class="text-red-500"></span></h3>
            <button type="button" onclick="closePenaltyModal()" class="text-gray-400 hover:text-white">✕</button>
        </div>

        <form id="penalty-form" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-gray-400 mb-1">Penalty Level</label>
                <select name="level" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-white text-sm focus:outline-none focus:border-red-600" required>
                    <option value="1">Level 1: Warning (No temporary ban)</option>
                    <option value="2">Level 2: Temporary Ban (24 hours)</option>
                    <option value="3">Level 3: Temporary Ban (7 days)</option>
                    <option value="4">Level 4: Temporary Ban (30 days)</option>
                    <option value="5">Level 5: Permanent Ban</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-400 mb-1">Reason / Violation Details</label>
                <textarea name="reason" rows="3" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-white text-sm focus:outline-none focus:border-red-600" placeholder="Enter a clear reason for this penalty..." required></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-slate-800">
                <button type="button" onclick="closePenaltyModal()" class="px-4 py-2 bg-slate-800 text-gray-300 rounded-xl text-xs font-semibold hover:bg-slate-700">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-red-600 text-white rounded-xl text-xs font-semibold hover:bg-red-700 shadow-lg shadow-red-600/20">Confirm Penalty</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
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