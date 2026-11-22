@extends('layouts.admin.master')
@section('title', 'All Community Cases')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-xl font-bold text-slate-800">All Community Cases</h2>
            <p class="text-sm text-slate-500">Manage all cases published by community members</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="bg-slate-100 text-slate-700 px-3 py-1.5 rounded-lg text-xs font-semibold border border-slate-200">
                Total: {{ $cases->total() }}
            </span>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 mb-4">
        <form method="GET" action="{{ route('admin.community-cases.index') }}" class="flex flex-wrap items-center gap-3">
            {{-- Search --}}
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search by title..."
                       class="w-full rounded-lg border border-slate-300 text-sm py-2 px-3">
            </div>

            {{-- Category Filter --}}
            <select name="category" class="rounded-lg border border-slate-300 text-sm py-2 px-3">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    @php $catName = $cat->translations->firstWhere('locale', app()->getLocale())?->name ?? $cat->label; @endphp
                    <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                        {{ $catName }}
                    </option>
                @endforeach
            </select>

            {{-- Status Filter --}}
            <select name="status" class="rounded-lg border border-slate-300 text-sm py-2 px-3">
                <option value="">All Statuses</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>

            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
                Filter
            </button>
            <a href="{{ route('admin.community-cases.index') }}" class="text-sm text-slate-500 hover:text-slate-700">
                Reset
            </a>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase">
                        <th class="py-3 px-4">Title</th>
                        <th class="py-3 px-4">Author</th>
                        <th class="py-3 px-4">Category</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Stats</th>
                        <th class="py-3 px-4">Date</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($cases as $case)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            {{-- Title --}}
                            <td class="py-3 px-4 font-medium text-slate-800 max-w-xs">
                                <a href="{{ route('community.cases.show', ['id' => $case->id, 'slug' => $case->slug]) }}" target="_blank"
                                   class="hover:text-red-600 transition line-clamp-2">
                                    {{ $case->title }}
                                </a>
                            </td>

                            {{-- Author --}}
                            <td class="py-3 px-4 text-slate-600 text-xs">
                                {{ $case->author->name ?? 'Unknown' }}
                            </td>

                            {{-- Category --}}
                            <td class="py-3 px-4">
                                @php $catTrans = $case->category?->translations->firstWhere('locale', app()->getLocale()) ?? $case->category?->translations->first(); @endphp
                                <span class="bg-slate-100 text-slate-700 px-2.5 py-1 rounded-md text-xs font-semibold border border-slate-200">
                                    {{ $catTrans->name ?? 'General' }}
                                </span>
                            </td>

                            {{-- Status --}}
                            <td class="py-3 px-4">
                                @php $status = $case->moderation_status?->value ?? $case->moderation_status; @endphp
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium border
                                    {{ $status === 'approved' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : ($status === 'pending' ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-rose-50 text-rose-700 border-rose-200') }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $status === 'approved' ? 'bg-emerald-500' : ($status === 'pending' ? 'bg-amber-500' : 'bg-rose-500') }}"></span>
                                    {{ ucfirst($status) }}
                                </span>
                            </td>

                            {{-- Stats --}}
                            <td class="py-3 px-4 text-xs text-slate-500 font-mono">
                                💬 {{ $case->comments_count ?? 0 }}
                                👁️ {{ $case->views_count ?? 0 }}
                            </td>

                            {{-- Date --}}
                            <td class="py-3 px-4 text-xs text-slate-500 font-mono">
                                {{ $case->created_at->diffForHumans() }}
                            </td>

                            {{-- Actions --}}
                            <td class="py-3 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if($status === 'pending')
                                        <form action="{{ route('admin.community-cases.approve', $case->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="bg-emerald-50 text-emerald-600 hover:bg-emerald-100 border border-emerald-200 px-2.5 py-1 rounded-lg text-xs font-medium">
                                                Approve
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.community-cases.reject', $case->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="bg-rose-50 text-rose-600 hover:bg-rose-100 border border-rose-200 px-2.5 py-1 rounded-lg text-xs font-medium">
                                                Reject
                                            </button>
                                        </form>
                                    @endif

                                    <form action="{{ route('admin.community-cases.destroy', $case->id) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Are you sure you want to delete this case?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-50 text-red-600 hover:bg-red-100 border border-red-200 px-2.5 py-1 rounded-lg text-xs font-medium">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-12 text-slate-400 text-sm">
                                <div class="flex flex-col items-center">
                                    <span class="text-3xl mb-2">🕵️</span>
                                    <p class="font-medium text-slate-600">No community cases found.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($cases->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $cases->links() }}
            </div>
        @endif
    </div>
@endsection