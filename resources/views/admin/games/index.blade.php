@extends('layouts.admin.master')
@section('title', 'Games Management')

@push('styles')
    @vite(['resources/css/components/article-gallery.css'])
@endpush

@section('content')
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Games Management</h2>
            <p class="text-sm text-slate-500">Manage game summaries, rich reviews, and multi-language translations</p>
        </div>

        @hasanyrole('super-admin|editor')
        <a href="{{ route('admin.games.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm px-4 py-2 rounded-lg shadow-sm">
            + Create Game
        </a>
        @endhasanyrole
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase">
                        <th class="py-3 px-4">Category</th>
                        <th class="py-3 px-4">Cover Img</th>
                        <th class="py-3 px-4">Internal Title</th>
                        <th class="py-3 px-4">Developer</th>
                        <th class="py-3 px-4">Translations</th>
                        <th class="py-3 px-4">Gallery</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @foreach ($games as $game)
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <td class="py-3.5 px-4 font-medium text-slate-700">
                            <span class="bg-slate-100 text-slate-700 px-2.5 py-1 rounded-md text-xs font-semibold border border-slate-200">
                                {{ optional($game->gameCategory->translations->where('locale', app()->getLocale())->first())->name ?? $game->gameCategory->label }}
                            </span>
                        </td>

                        <td class="py-3.5 px-4">
                            <div class="w-12 h-16 rounded-lg overflow-hidden bg-slate-100 border border-slate-200 shrink-0">
                                @if($game->getFirstMediaUrl('cover_imgs'))
                                    <img src="{{ $game->getFirstMediaUrl('cover_imgs') }}" class="w-full h-full object-cover"/>
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-slate-400 text-[10px]">No Cover</div>
                                @endif
                            </div>
                        </td>

                        <td class="py-3.5 px-4 text-slate-600 font-mono text-xs">
                            {{ $game->internal_title }}
                        </td>

                        <td class="py-3.5 px-4 text-slate-600 text-xs">
                            {{ $game->developer ?? '-' }} ({{ $game->published_year ?? '-' }})
                        </td>

                        <td class="py-3.5 px-4">
                            <div class="flex items-center gap-2 flex-wrap">
                                @foreach ($game->translations as $trans)
                                    <div class="inline-flex items-center bg-slate-50 border border-slate-200 rounded-lg p-0.5">
                                        <a href="{{ route('admin.games-trans.edit', $trans->id) }}" class="px-2 py-0.5 text-xs font-semibold text-blue-600 hover:underline">
                                            {{ strtoupper($trans->locale) }}
                                        </a>
                                        <form action="{{ route('admin.games-trans.destroy', $trans->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete translation?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-slate-400 hover:text-red-600 px-1 text-xs font-bold">&times;</button>
                                        </form>
                                    </div>
                                @endforeach

                                <a href="{{ route('admin.games-trans.create', $game->id) }}" class="inline-flex items-center bg-amber-50 border border-amber-200 text-amber-700 px-2 py-1 rounded-lg text-xs font-medium">
                                    + Add Trans
                                </a>
                            </div>
                        </td>


                        <td class="py-3.5 px-4">
                            <button class="show-gallery inline-flex items-center gap-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 font-medium px-2.5 py-1 rounded-lg text-xs transition-colors cursor-pointer" 
                                    data-game-id="{{ $game->id }}">
                                <span>📷</span>
                                <span class="game-content-imgs-count font-semibold">{{ $game->getMedia('content_imgs')->count() }}</span>
                            </button>
                        </td>


                        <td class="py-3.5 px-4 text-right">
                            @hasanyrole('super-admin|editor')
                                <a href="{{ route('admin.games.edit', $game->id) }}" class="bg-blue-50 text-blue-600 hover:bg-blue-100 border border-blue-200 px-2.5 py-1 rounded-lg text-xs font-medium mr-2">
                                    Edit Main
                                </a>
                                <form action="{{ route('admin.games.destroy', $game->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete main game?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-50 text-red-600 border border-red-200 px-2.5 py-1 rounded-lg text-xs font-medium">
                                        Delete
                                    </button>
                                </form>
                            @endhasanyrole
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection


@push('scripts')
    <script>
        window.upload_url = "{{ route('admin.games.upload.content') }}";
        window.game_content_imgs_url = "{{ route('admin.games.get.content') }}";
        window.delete_content_img_url = "{{ route('admin.games.delete.content_img') }}";
    </script>
    @vite(['resources/js/components/game-gallery.js'])
@endpush