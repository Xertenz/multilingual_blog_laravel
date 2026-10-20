@extends('layouts.admin.master')

@section('title', 'Pending Articles')

@section('content')
    {{-- Header Actions --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Pending Articles Moderation</h2>
            <p class="text-sm text-slate-500">Review, approve, or reject article submissions from writers</p>
        </div>
        <a href="{{ route('admin.articles.index') }}" 
           class="bg-slate-600 hover:bg-slate-700 text-white font-medium text-sm px-4 py-2 rounded-lg shadow-sm hover:shadow transition-all flex items-center gap-2">
            <span>&larr; All Articles</span>
        </a>
    </div>
    
    {{-- Articles Table Container --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        <th class="py-3 px-6">ID</th>
                        <th class="py-3 px-6">Title</th>
                        <th class="py-3 px-6">Writer</th>
                        <th class="py-3 px-6">Internal Title</th>
                        <th class="py-3 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($articles as $article)
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        {{-- ID --}}
                        <td class="py-3.5 px-6 font-mono text-xs text-slate-500">
                            #{{ $article->id }}
                        </td>

                        {{-- Title --}}
                        <td class="py-3.5 px-6 font-semibold text-slate-800">
                            {{ $article->translations->first()->title ?? 'Untitled' }}
                        </td>

                        {{-- Writer --}}
                        <td class="py-3.5 px-6 text-slate-600">
                            {{ $article->user->name ?? 'Unknown' }}
                        </td>

                        {{-- Internal Title --}}
                        <td class="py-3.5 px-6 font-mono text-xs text-slate-500">
                            {{ $article->internal_title }}
                        </td>

                        {{-- Actions --}}
                        <td class="py-3.5 px-6 text-right">
                            <div class="flex items-center justify-end gap-2">
                                {{-- زر الموافقة والنشر --}}
                                <form method="POST" action="{{ route('admin.articles.approve', $article->id) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="bg-emerald-50 text-emerald-600 hover:bg-emerald-100 border border-emerald-200 px-2.5 py-1 rounded-lg text-xs font-medium transition-colors cursor-pointer">
                                        Approve & Publish
                                    </button>
                                </form>

                                {{-- زر الرفض وإرجاعها لمسودة --}}
                                <form method="POST" action="{{ route('admin.articles.reject', $article->id) }}" 
                                      onsubmit="return confirm('Are you sure you want to reject and move this article back to draft?');" class="inline">
                                    @csrf
                                    <button type="submit" class="bg-amber-50 text-amber-600 hover:bg-amber-100 border border-amber-200 px-2.5 py-1 rounded-lg text-xs font-medium transition-colors cursor-pointer">
                                        Reject
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                            <div class="flex flex-col items-center justify-center">
                                <span class="text-3xl mb-2">📥</span>
                                <p class="text-sm font-medium text-slate-600">No pending articles found.</p>
                                <p class="text-xs text-slate-400 mt-1">All writer submissions have been reviewed.</p>
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
        {{ $articles->links() }}
    </div>
@endsection