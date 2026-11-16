@extends('layouts.admin.master')

@section('title', 'Game Categories')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Game Categories</h2>
            <p class="text-sm text-slate-500">Manage and organize game categories</p>
        </div>
        <a href="{{ route('admin.game-categories.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
            + Add New Category
        </a>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg mb-4 text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase">
                    <th class="py-3 px-4">Image</th>
                    <th class="py-3 px-4">Label</th>
                    <th class="py-3 px-4">Name (Default)</th>
                    <th class="py-3 px-4">Games Count</th>
                    <th class="py-3 px-4 w-32">Status</th>
                    <th class="py-3 px-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 text-sm">
                @forelse($cats as $cat)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="py-3 px-4">
                            @if($cat->hasMedia('featured_imgs'))
                                <img src="{{ $cat->getFirstMediaUrl('featured_imgs', 'thumb') }}" class="w-10 h-10 object-cover rounded-lg border">
                            @else
                                <span class="text-xs text-slate-400">No Image</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 font-medium text-slate-800">{{ $cat->label }}</td>
                        <td class="py-3 px-4 text-slate-700">
                            {{ $cat->translations->where('locale', app()->getLocale())->first()->name ?? ($cat->translations->first()->name ?? 'N/A') }}
                        </td>
                        <td class="py-3 px-4">
                            <span class="bg-slate-100 text-slate-700 px-2.5 py-1 rounded-full text-xs font-semibold">
                                {{ $cat->games_count }} Games
                            </span>
                        </td>
                        {{-- ✅ Status --}}
                        <td class="py-3 px-4">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium border
                                {{ $cat->is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-600 border-slate-200' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $cat->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                {{ $cat->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-right space-x-2">
                            <a href="{{ route('admin.game-categories.edit', $cat->id) }}" class="text-indigo-600 hover:text-indigo-900 font-medium text-xs">Edit</a>
                            <form action="{{ route('admin.game-categories.destroy', $cat->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this category?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-rose-600 hover:text-rose-900 font-medium text-xs">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-8 text-slate-400 text-sm">No game categories found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4 border-t border-slate-200">
            {{ $cats->links() }}
        </div>
    </div>
</div>
@endsection