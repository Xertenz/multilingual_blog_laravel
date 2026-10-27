@extends("layouts.admin.master")
@section('title', 'Manage Books & Summaries')

@section('content')
<div class="container mx-auto pb-8 px-4 max-w-7xl">
    
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Manage Books & Summaries</h1>
            <p class="text-xs text-slate-500">Explore, add, and manage book summaries and affiliate links.</p>
        </div>
        <a href="{{ route('admin.books.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold px-4 py-2 rounded-lg transition shadow-2xs">
            + Add New Book
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs rounded-xl flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <!-- Table Card -->
    <div class="bg-white shadow rounded-2xl border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 text-[11px] uppercase tracking-wider text-slate-500 bg-slate-50">
                        <th class="p-4">Cover</th>
                        <th class="p-4">Internal Title</th>
                        <th class="p-4">Author</th>
                        <th class="p-4">Pages</th>
                        <th class="p-4">Year</th>
                        <th class="p-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($books as $book)
                        @php
                            $trans = $book->translate(app()->getLocale());
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="p-4">
                                @if($book->hasMedia('cover_imgs'))
                                    <img src="{{ $book->getFirstMediaUrl('cover_imgs', 'thumb') ?: $book->getFirstMediaUrl('covers') }}" alt="Cover" class="w-10 h-14 object-cover rounded-lg border border-slate-200 shadow-2xs">
                                @else
                                    <div class="w-10 h-14 bg-slate-100 rounded-lg flex items-center justify-center text-[10px] text-slate-400 font-medium">No Image</div>
                                @endif
                            </td>
                            <td class="p-4 font-mono font-semibold text-indigo-600">
                                {{ $book->internal_title }}
                            </td>
                            <td class="p-4 text-slate-600">
                                {{ $trans->author ?? 'N/A' }}
                            </td>
                            <td class="p-4 text-slate-500">
                                {{ $book->pages_count ?? '-' }}
                            </td>
                            <td class="p-4 text-slate-500">
                                {{ $book->published_year ?? '-' }}
                            </td>
                            <td class="p-4 text-center space-x-2">
                                <a href="{{ route('admin.books.edit', $book->id) }}" class="inline-block px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium rounded-lg transition">
                                    Edit
                                </a>
                                <form action="{{ route('admin.books.destroy', $book->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this book?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 font-medium rounded-lg transition">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400 text-sm">
                                No books found yet. Click "+ Add New Book" to get started.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($books->hasPages())
            <div class="p-4 border-t border-slate-200 bg-slate-50">
                {{ $books->links() }}
            </div>
        @endif
    </div>
</div>
@endsection