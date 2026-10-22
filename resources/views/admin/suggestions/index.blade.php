@extends("layouts.admin.master")

@section('title', 'Case Suggestions Moderation')

@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }
        
        /* تنسيق شريط التمرير الأفقي للجدول */
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
    
    {{-- Header Actions --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Case Suggestions Moderation</h1>
            <p class="text-sm text-slate-500">Review community-submitted crime cases, change status, or remove inappropriate topics</p>
        </div>
    </div>

    {{-- Suggestions Table Container --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse min-w-[850px]">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        <th class="py-3 px-6 w-28">ID & Locale</th>
                        <th class="py-3 px-6">Title & Description</th>
                        <th class="py-3 px-6 w-44">Suggested By</th>
                        <th class="py-3 px-6 text-center w-24">Votes</th>
                        <th class="py-3 px-6 text-center w-32">Status</th>
                        <th class="py-3 px-6 text-right w-48">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($suggestions as $suggestion)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3.5 px-6 font-mono text-xs text-slate-500 whitespace-nowrap">
                                <span class="font-bold text-slate-700">#{{ $suggestion->id }}</span>
                                <div class="mt-1">
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-600 uppercase border border-slate-200">
                                        {{ $suggestion->locale }}
                                    </span>
                                </div>
                            </td>
                            <td class="py-3.5 px-6">
                                <p class="font-semibold text-slate-800">{{ $suggestion->title }}</p>
                                @if($suggestion->description)
                                    <p class="text-xs text-slate-500 line-clamp-1 mt-0.5 max-w-md">{{ $suggestion->description }}</p>
                                @endif
                            </td>
                            <td class="py-3.5 px-6 text-slate-600 text-xs whitespace-nowrap">
                                <span class="font-medium text-slate-700 block truncate max-w-[140px]">{{ $suggestion->user->name ?? 'Unknown' }}</span>
                                <span class="block text-slate-400 text-[11px]">{{ $suggestion->created_at->diffForHumans() }}</span>
                            </td>
                            <td class="py-3.5 px-6 text-center whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-md text-xs font-bold bg-red-50 text-red-600 border border-red-200 inline-block">
                                    🔥 {{ $suggestion->votes_count }}
                                </span>
                            </td>
                            <td class="py-3.5 px-6 text-center whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-md text-xs font-semibold border inline-block
                                    {{ $suggestion->status === 'pending' ? 'bg-amber-50 text-amber-700 border-amber-200' : '' }}
                                    {{ $suggestion->status === 'approved' ? 'bg-blue-50 text-blue-700 border-blue-200' : '' }}
                                    {{ $suggestion->status === 'written' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : '' }}">
                                    {{ ucfirst($suggestion->status) }}
                                </span>
                            </td>
                            <td class="py-3.5 px-6 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    {{-- تحديث الحالة عبر Form سريع --}}
                                    <form action="{{ route('admin.suggestions.status', $suggestion->id) }}" method="POST" class="inline-flex items-center">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" onchange="this.form.submit()" class="bg-slate-50 border border-slate-200 text-slate-700 text-xs rounded-lg px-2 py-1 focus:outline-none focus:border-indigo-500 cursor-pointer">
                                            <option value="pending" {{ $suggestion->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="approved" {{ $suggestion->status == 'approved' ? 'selected' : '' }}>In Research</option>
                                            <option value="written" {{ $suggestion->status == 'written' ? 'selected' : '' }}>Investigated</option>
                                        </select>
                                    </form>

                                    {{-- زر الحذف --}}
                                    <form action="{{ route('admin.suggestions.destroy', $suggestion->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this suggestion?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-rose-50 text-rose-600 hover:bg-rose-100 border border-rose-200 px-2 py-1 rounded-lg text-xs font-medium transition-colors">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center">
                                    <span class="text-3xl mb-2">📂</span>
                                    <p class="text-sm font-medium text-slate-600">No case suggestions found.</p>
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
        {{ $suggestions->links() }}
    </div>
</div>
@endsection

@push('scripts')
    <script>
        window.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                showToast('success', @json(session('success')));
            @endif
        });
    </script>
@endpush