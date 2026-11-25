@extends('layouts.admin.master')
@section('title', 'Article Timelines')

@section('content')
<div class="container mx-auto pb-8 px-4">

    <div class="mb-6">
        <h1 class="text-xl font-bold text-slate-800">Article Timelines</h1>
        <p class="text-xs text-slate-500">Select an article to manage its timeline, set dates or year/month, sort order, and per-locale translations.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Left Column: Articles List -->
        <div class="lg:col-span-4 bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <h2 class="text-sm font-bold text-slate-700 mb-3 border-b border-slate-100 pb-2">Select an Article</h2>

            <div class="space-y-2 max-h-[600px] overflow-y-auto pr-1">
                @foreach($articles as $art)
                    @php
                        $artTitle = $art->translations->firstWhere('locale', app()->getLocale())->title
                            ?? $art->translations->first()->title
                            ?? $art->label;
                    @endphp
                    <a href="{{ route('admin.timelines.index', ['article_id' => $art->id]) }}"
                       class="block p-3 rounded-xl border transition-all {{ optional($selectedArticle)->id === $art->id ? 'bg-indigo-50 border-indigo-300 ring-1 ring-indigo-300' : 'bg-slate-50 border-slate-200 hover:bg-slate-100' }}">
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <span class="text-xs font-semibold text-slate-800 truncate">{{ $artTitle }}</span>
                            <span class="bg-indigo-100 text-indigo-700 text-[10px] font-bold px-2 py-0.5 rounded-full shrink-0">
                                {{ $art->timelines->count() }} {{ Str::plural('event', $art->timelines->count()) }}
                            </span>
                        </div>
                        <span class="text-[11px] text-slate-500 block truncate">{{ $art->label }} • {{ $art->category->label ?? 'Uncategorized' }}</span>
                    </a>
                @endforeach
            </div>

            <div class="mt-4">
                {{ $articles->links() }}
            </div>
        </div>

        <!-- Right Column: Selected Article Timeline & Add Event Form -->
        <div class="lg:col-span-8 space-y-6">
            @if($selectedArticle)

                <!-- Existing Timeline Events -->
                <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm space-y-4">
                    <div class="border-b border-slate-100 pb-3">
                        <h2 class="text-base font-bold text-slate-800">
                            Timeline for Article: <span class="text-indigo-600">{{ $selectedArticle->label }}</span>
                        </h2>
                        <p class="text-xs text-slate-500">Manage timeline events ordered by sort order or event date.</p>
                    </div>

                    @if($selectedArticle->timelines->count() > 0)
                        <div class="space-y-3" x-data="{ editingTimeline: null }">
                            @foreach($selectedArticle->timelines as $item)
                                @php
                                    $existingTrans = $item->translations->keyBy('locale');
                                    $currentTrans = $item->getLocalizedTranslation();
                                @endphp
                                <div class="bg-slate-50 border border-slate-200 p-3.5 rounded-xl flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                                    <div class="space-y-1">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="px-2 py-0.5 rounded bg-slate-200 text-slate-700 font-mono text-[10px]">
                                                #{{ $item->sort_order }}
                                            </span>
                                            @if($item->displayTime())
                                                <span class="px-2 py-0.5 rounded bg-indigo-100 text-indigo-700 font-semibold text-[11px]">
                                                    🕒 {{ $item->displayTime() }}
                                                </span>
                                            @endif
                                            <span class="font-bold text-slate-800 text-sm">
                                                {{ $currentTrans->title ?? '(Untitled Event)' }}
                                            </span>
                                        </div>
                                        @if(!empty($currentTrans?->description))
                                            <p class="text-slate-600 text-xs leading-relaxed">{{ $currentTrans->description }}</p>
                                        @endif
                                    </div>

                                    <div class="flex items-center gap-2 shrink-0">
                                        <button type="button"
                                                @click="editingTimeline = {{ $item->id }}"
                                                class="bg-indigo-50 text-indigo-600 hover:bg-indigo-100 px-2.5 py-1 rounded text-xs font-semibold">
                                            Edit / Locales
                                        </button>

                                        <form action="{{ route('admin.timelines.destroy', $item->id) }}"
                                              method="POST"
                                              onsubmit="return confirm('Are you sure you want to delete this timeline event?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-600 hover:text-rose-800 text-xs font-bold px-2 py-1">
                                                Delete
                                            </button>
                                        </form>
                                    </div>

                                    <!-- Edit Timeline Event Modal -->
                                    <div x-show="editingTimeline === {{ $item->id }}"
                                         x-cloak
                                         class="fixed inset-0 z-[99999] bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4 !m-0">
                                        <div @click.away="editingTimeline = null"
                                             class="bg-white rounded-2xl max-w-2xl w-full p-5 space-y-4 shadow-xl max-h-[90vh] overflow-y-auto">
                                            <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                                                <h4 class="font-bold text-slate-800 text-sm">Edit Timeline Event (ID: {{ $item->id }})</h4>
                                                <button type="button" @click="editingTimeline = null" class="text-slate-400 hover:text-slate-600 font-bold">&times;</button>
                                            </div>

                                            <form action="{{ route('admin.timelines.update', $item->id) }}" method="POST" class="space-y-4">
                                                @csrf
                                                @method('PUT')

                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                    <div>
                                                        <label class="block text-[11px] text-slate-600 font-semibold mb-1">Full Event Date (Optional)</label>
                                                        <input type="date"
                                                               name="event_date"
                                                               value="{{ optional($item->event_date)->format('Y-m-d') }}"
                                                               class="w-full rounded-lg border border-slate-300 text-xs py-1.5 px-2.5">
                                                    </div>
                                                    <div>
                                                        <label class="block text-[11px] text-slate-600 font-semibold mb-1">Sort Order</label>
                                                        <input type="number"
                                                               name="sort_order"
                                                               min="0"
                                                               value="{{ $item->sort_order }}"
                                                               class="w-full rounded-lg border border-slate-300 text-xs py-1.5 px-2.5">
                                                    </div>
                                                    <div>
                                                        <label class="block text-[11px] text-slate-600 font-semibold mb-1">Year Only (Optional)</label>
                                                        <input type="number"
                                                               name="event_year"
                                                               min="1"
                                                               max="9999"
                                                               value="{{ $item->event_year }}"
                                                               placeholder="e.g. 2005"
                                                               class="w-full rounded-lg border border-slate-300 text-xs py-1.5 px-2.5">
                                                    </div>
                                                    <div>
                                                        <label class="block text-[11px] text-slate-600 font-semibold mb-1">Month (Optional, with Year)</label>
                                                        <select name="event_month" class="w-full rounded-lg border border-slate-300 text-xs py-1.5 px-2.5 bg-white">
                                                            <option value="">-- None --</option>
                                                            @for($m = 1; $m <= 12; $m++)
                                                                <option value="{{ $m }}" {{ (int) $item->event_month === $m ? 'selected' : '' }}>
                                                                    {{ str_pad((string) $m, 2, '0', STR_PAD_LEFT) }} - {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                                                </option>
                                                            @endfor
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="space-y-3">
                                                    <span class="text-xs font-bold text-slate-700 block">Event Details by Locale:</span>
                                                    @foreach($locales as $code => $name)
                                                        <div class="bg-slate-50 p-3 rounded-xl border border-slate-200 space-y-2">
                                                            <span class="text-[11px] font-bold text-indigo-700 uppercase block">{{ $name }} ({{ $code }})</span>

                                                            <input type="text"
                                                                   name="translations[{{ $code }}][time_label]"
                                                                   value="{{ $existingTrans[$code]->time_label ?? '' }}"
                                                                   placeholder="Time label or period in this locale (e.g., Midnight / 1998)"
                                                                   class="w-full rounded-lg border border-slate-300 text-xs py-1.5 px-2.5">

                                                            <input type="text"
                                                                   name="translations[{{ $code }}][title]"
                                                                   value="{{ $existingTrans[$code]->title ?? '' }}"
                                                                   placeholder="Event title in this locale..."
                                                                   class="w-full rounded-lg border border-slate-300 text-xs py-1.5 px-2.5">

                                                            <textarea name="translations[{{ $code }}][description]"
                                                                      rows="2"
                                                                      placeholder="Event description or details in this locale..."
                                                                      class="w-full rounded-lg border border-slate-300 text-xs py-1.5 px-2.5">{{ $existingTrans[$code]->description ?? '' }}</textarea>
                                                        </div>
                                                    @endforeach
                                                </div>

                                                <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                                                    <button type="button" @click="editingTimeline = null" class="bg-slate-100 text-slate-600 px-3 py-1.5 rounded-lg text-xs font-semibold">Cancel</button>
                                                    <button type="submit" class="bg-indigo-600 text-white px-4 py-1.5 rounded-lg text-xs font-semibold hover:bg-indigo-700">Save Changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-xs text-slate-400 py-4">No timeline events recorded for this article yet.</p>
                    @endif
                </div>

                <!-- Add New Timeline Event -->
                <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                    <h3 class="text-sm font-bold text-slate-800 mb-4 pb-2 border-b border-slate-100">Add New Timeline Event</h3>

                    <form action="{{ route('admin.timelines.store', $selectedArticle->id) }}" method="POST" class="space-y-4">
                        @csrf

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 bg-indigo-50/50 p-3 rounded-xl border border-indigo-100">
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Full Event Date (Optional)</label>
                                <input type="date" name="event_date" value="{{ old('event_date') }}"
                                       class="w-full bg-white rounded-lg border border-slate-300 text-xs py-2 px-3 outline-none focus:border-indigo-500">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Sort Order</label>
                                <input type="number" name="sort_order" min="0" value="{{ old('sort_order', 0) }}"
                                       class="w-full bg-white rounded-lg border border-slate-300 text-xs py-2 px-3 outline-none focus:border-indigo-500">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Year Only (Optional)</label>
                                <input type="number" name="event_year" min="1" max="9999" value="{{ old('event_year') }}"
                                       placeholder="e.g. 2005"
                                       class="w-full bg-white rounded-lg border border-slate-300 text-xs py-2 px-3 outline-none focus:border-indigo-500">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Month (Optional, with Year)</label>
                                <select name="event_month" class="w-full bg-white rounded-lg border border-slate-300 text-xs py-2 px-3 outline-none focus:border-indigo-500">
                                    <option value="">-- None --</option>
                                    @for($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" {{ (string) old('event_month') === (string) $m ? 'selected' : '' }}>
                                            {{ str_pad((string) $m, 2, '0', STR_PAD_LEFT) }} - {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <div class="space-y-3 pt-2">
                            <span class="text-xs font-bold text-slate-700 block">Event Details per Locale:</span>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($locales as $code => $name)
                                    <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200 space-y-2">
                                        <div class="flex items-center justify-between border-b border-slate-200 pb-1">
                                            <span class="text-xs font-bold text-indigo-700 uppercase">{{ $name }} ({{ $code }})</span>
                                            <span class="text-[10px] bg-slate-200 text-slate-600 font-semibold px-2 py-0.5 rounded">Optional</span>
                                        </div>

                                        <div>
                                            <label class="block text-[11px] text-slate-500 mb-1">Time Label / Period</label>
                                            <input type="text"
                                                   name="translations[{{ $code }}][time_label]"
                                                   value="{{ old("translations.$code.time_label") }}"
                                                   class="w-full rounded-lg border border-slate-300 text-xs py-1.5 px-2.5 outline-none focus:border-indigo-500"
                                                   placeholder="e.g. 1999-12-31 or Late evening">
                                        </div>

                                        <div>
                                            <label class="block text-[11px] text-slate-500 mb-1">Event Title</label>
                                            <input type="text"
                                                   name="translations[{{ $code }}][title]"
                                                   value="{{ old("translations.$code.title") }}"
                                                   class="w-full rounded-lg border border-slate-300 text-xs py-1.5 px-2.5 outline-none focus:border-indigo-500"
                                                   placeholder="Short title for the event">
                                        </div>

                                        <div>
                                            <label class="block text-[11px] text-slate-500 mb-1">Event Description (Optional)</label>
                                            <textarea name="translations[{{ $code }}][description]"
                                                      rows="2"
                                                      class="w-full rounded-lg border border-slate-300 text-xs py-1.5 px-2.5 outline-none focus:border-indigo-500"
                                                      placeholder="Additional details about this event..."></textarea>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex justify-end pt-3 border-t border-slate-100">
                            <button type="submit" class="bg-indigo-600 text-white font-semibold px-5 py-2 rounded-xl hover:bg-indigo-700 text-xs transition shadow-sm">
                                Save Timeline Event
                            </button>
                        </div>
                    </form>
                </div>

            @else
                <div class="bg-white border border-slate-200 rounded-2xl p-12 text-center text-slate-400">
                    <p class="text-sm font-semibold text-slate-600">Please select an article from the sidebar to manage its timeline.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Show Toast
        if (typeof window.showToast === 'function') {
            @if(session('success'))
                window.showToast({
                    icon: 'success',
                    title: 'Success',
                    text: @json(session('success')),
                });
            @elseif(session('error'))
                window.showToast({
                    icon: 'error',
                    title: 'Error',
                    text: @json(session('error')),
                });
            @elseif($errors->any())
                window.showToast({
                    icon: 'error',
                    title: 'Validation Error',
                    text: @json($errors->first()),
                });
            @endif
        }
    });
</script>
@endpush