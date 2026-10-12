{{-- resources/views/admin/users/index.blade.php --}}
@extends("layouts.admin.master")

@section('title', 'Users & Roles Management')

@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }
    </style>
@endpush

@section('content')
<div class="container mx-auto pb-8 px-4">
    
    {{-- Header Actions --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Users & Roles Management</h1>
            <p class="text-sm text-slate-500">Manage system users and their assigned permissions</p>
        </div>
    </div>

    {{-- 
    @if(session('success'))
        <div class="p-4 mb-4 text-sm text-emerald-800 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    --}}

    {{-- Users Table Container --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        <th class="py-3 px-6">ID</th>
                        <th class="py-3 px-6">Name</th>
                        <th class="py-3 px-6">Email Address</th>
                        <th class="py-3 px-6 text-center">Current Role</th>
                        <th class="py-3 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3.5 px-6 font-mono text-xs text-slate-500">
                                #{{ $user->id }}
                            </td>
                            <td class="py-3.5 px-6 font-semibold text-slate-800">
                                {{ $user->name }}
                            </td>
                            <td class="py-3.5 px-6 text-slate-600">
                                {{ $user->email }}
                            </td>
                            <td class="py-3.5 px-6 text-center">
                                <span class="px-2.5 py-1 rounded-md text-xs font-semibold border
                                    {{ $user->hasRole('super-admin') ? 'bg-red-50 text-red-700 border-red-200' : '' }}
                                    {{ $user->hasRole('editor') ? 'bg-blue-50 text-blue-700 border-blue-200' : '' }}
                                    {{ $user->hasRole('writer') ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : '' }}
                                    {{ $user->roles->isEmpty() ? 'bg-slate-100 text-slate-700 border-slate-200' : '' }}">
                                    {{ $user->formatted_role }}
                                </span>
                            </td>
                            <td class="py-3.5 px-6 text-right">
                                <a href="{{ route('admin.users.edit', $user->id) }}" 
                                   class="bg-blue-50 text-blue-600 hover:bg-blue-100 border border-blue-200 px-2.5 py-1 rounded-lg text-xs font-medium transition-colors inline-block">
                                    Change Role
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center">
                                    <span class="text-3xl mb-2">👥</span>
                                    <p class="text-sm font-medium text-slate-600">No users found.</p>
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
        {{ $users->links() }}
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