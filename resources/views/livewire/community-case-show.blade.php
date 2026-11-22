@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #community-case-map {
            height: 360px;
            width: 100%;
            border-radius: 0.75rem;
            z-index: 1 !important;
            background-color: #1e293b !important;
        }
        #community-case-map .leaflet-pane { z-index: 1 !important; }
        #community-case-map .leaflet-top,
        #community-case-map .leaflet-bottom { z-index: 2 !important; }

        /* Night mode filter — مطابق لصفحة الخريطة */
        #community-case-map .leaflet-tile-pane {
            filter: brightness(0.6) invert(1) contrast(3) hue-rotate(200deg) saturate(0.3) brightness(0.7);
        }

        #community-case-map .leaflet-marker-icon,
        #community-case-map .leaflet-popup {
            filter: invert(1) hue-rotate(200deg) saturate(3);
        }

        #community-case-map .leaflet-control-attribution.leaflet-control {
            display: none;
        }
    </style>
@endpush

<div class="container mx-auto px-4 py-10 max-w-4xl text-white">

    {{-- رسالة النجاح --}}
    @if (session()->has('message'))
        <div class="mb-6 p-4 bg-emerald-950/80 border border-emerald-800 text-emerald-400 rounded-xl text-sm">
            {{ session('message') }}
        </div>
    @endif

    {{-- رسالة الخطأ --}}
    @if (session()->has('error'))
        <div class="mb-6 p-4 bg-rose-950/80 border border-rose-800 text-rose-300 rounded-xl text-sm">
            {{ session('error') }}
        </div>
    @endif

    <!-- زر العودة -->
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('community.cases.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-gray-400 hover:text-white transition">
            <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>Back to Detective Board</span>
        </a>

        {{-- ✅ أزرار تعديل وحذف القضية --}}
        @auth
            @if($communityCase->user_id === auth()->id() || auth()->user()->hasAnyRole(['super-admin', 'editor']))
                <div class="flex items-center gap-2">
                    <button wire:click="openEditModal"
                            class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-xs font-semibold transition shadow-lg shadow-blue-600/20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        <span>{{ __('Edit Case') }}</span>
                    </button>

                    <button wire:click="deleteCase"
                            wire:confirm="{{ __('Are you sure you want to delete this case?') }}"
                            class="inline-flex items-center gap-2 bg-rose-700 hover:bg-rose-800 text-white px-4 py-2 rounded-lg text-xs font-semibold transition shadow-lg shadow-rose-700/20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        <span>{{ __('Delete Case') }}</span>
                    </button>
                </div>
            @endif
        @endauth
    </div>

    <!-- بطاقة القضية الرئيسية -->
    <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-6 md:p-8 shadow-xl space-y-6 mb-8">

        <!-- معلومات رأس القضية -->
        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-800 pb-4">
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 text-xs font-bold bg-red-600/20 text-red-500 border border-red-600/30 rounded-lg uppercase tracking-wider">
                    {{ $categoryTrans->name ?? 'General' }}
                </span>

                @if($communityCase->case_status === App\Enums\CommunityCaseStatus::OPEN)
                    <span class="px-2.5 py-1 text-[11px] font-semibold bg-emerald-950/60 text-emerald-400 border border-emerald-800/40 rounded-lg">Active Investigation</span>
                @elseif($communityCase->case_status === App\Enums\CommunityCaseStatus::SOLVED)
                    <span class="px-2.5 py-1 text-[11px] font-semibold bg-blue-950/60 text-blue-400 border border-blue-800/40 rounded-lg">Solved Case</span>
                @else
                    <span class="px-2.5 py-1 text-[11px] font-semibold bg-slate-800 text-gray-400 border border-slate-700 rounded-lg">Closed</span>
                @endif
            </div>

            <div class="flex items-center gap-3 text-xs text-gray-400">
                @php
                    $caseScore = $communityCase->score;
                    $caseUserVote = $communityCase->user_vote;
                @endphp

                <div class="flex items-center gap-1.5 bg-slate-900 border border-slate-800 rounded-lg px-2 py-1 text-xs font-mono">
                    <button type="button"
                            wire:click="vote(1)"
                            class="px-1.5 py-0.5 rounded transition {{ $caseUserVote === 1 ? 'bg-emerald-600/30 text-emerald-400 font-bold' : 'text-gray-400 hover:text-emerald-400' }}"
                            title="{{ __('Upvote') }}">
                        ▲
                    </button>

                    <span class="{{ $caseScore > 0 ? 'text-emerald-400 font-bold' : ($caseScore < 0 ? 'text-rose-400 font-bold' : 'text-gray-300') }}">
                        {{ $caseScore }}
                    </span>

                    <button type="button"
                            wire:click="vote(-1)"
                            class="px-1.5 py-0.5 rounded transition {{ $caseUserVote === -1 ? 'bg-rose-600/30 text-rose-400 font-bold' : 'text-gray-400 hover:text-rose-400' }}"
                            title="{{ __('Downvote') }}">
                        ▼
                    </button>
                </div>
                <span>By <strong class="text-gray-200">{{ $communityCase->author->name }}</strong></span>
                <span>•</span>
                <span>{{ $communityCase->created_at->format('F j, Y') }}</span>
                <span>•</span>
                <span class="flex items-center gap-1">
                    👁️ {{ $communityCase->views_count }} Views
                </span>
                @auth
                    @if($communityCase->user_id !== auth()->id())
                        <span>•</span>
                        <button onclick="openReportModal('{{ App\Models\CommunityCase::class }}', {{ $communityCase->id }})" class="text-rose-400 hover:text-rose-300 transition flex items-center gap-1">
                            🚨 إبلاغ
                        </button>
                    @endif
                @endauth
            </div>
        </div>

        <h1 class="text-2xl md:text-3xl font-black text-white leading-tight">
            {{ $communityCase->title }}
        </h1>

        <div class="prose prose-invert max-w-none text-gray-300 leading-relaxed text-sm md:text-base space-y-4 pt-2">
            {!! $communityCase->body !!}
        </div>
    </div>

    <!-- قسم مواقع القضية والخريطة -->
    @if($communityCase->locations->isNotEmpty())
        @php
            $jsLocations = $communityCase->locations->map(function ($loc) {
                $t = $loc->translation;
                return [
                    'latitude'    => (float) $loc->latitude,
                    'longitude'   => (float) $loc->longitude,
                    'title'       => $t?->title ?? __('Location'),
                    'description' => $t?->description ?? '',
                ];
            })->values();
        @endphp

        <div class="bg-[#0f1526] border border-slate-800 rounded-2xl p-6 mb-8 space-y-4">
            <h2 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
                <span class="w-2 h-2 bg-red-600 rounded-sm"></span>
                <span>{{ __('Case Locations') }}</span>
            </h2>

            <div wire:ignore class="p-[2px] rounded-xl bg-red-600/80 shadow-sm">
                <div id="community-case-map" data-locations='@json($jsLocations)'></div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                @foreach($communityCase->locations as $loc)
                    @php $locTrans = $loc->translation; @endphp
                    <div class="bg-slate-900/80 border border-slate-800 rounded-xl p-4 space-y-1">
                        <div class="text-sm font-bold text-white">
                            {{ $locTrans->title ?? __('Location') }}
                        </div>
                        @if(!empty($locTrans?->description))
                            <p class="text-xs text-gray-400 leading-relaxed">{{ $locTrans->description }}</p>
                        @endif
                        <div class="text-[11px] font-mono text-red-400 pt-1">
                            📍 {{ $loc->latitude }}, {{ $loc->longitude }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- قسم التعليقات والنقاشات المجتمعية الموحد -->
    <livewire:comments-section :model="$communityCase" />

    {{-- ✅ Modal تعديل القضية --}}
    @if($showEditModal)
        <div class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4"
             wire:click.self="closeEditModal">

            <div class="bg-[#0f1526] border border-slate-800 w-full max-w-2xl max-h-[90vh] overflow-y-auto rounded-2xl p-6 shadow-2xl relative">

                <div class="flex justify-between items-center mb-6 pb-4 border-b border-slate-800">
                    <h3 class="text-base font-bold text-white">{{ __('Edit Case') }}</h3>
                    <button wire:click="closeEditModal" class="text-gray-400 hover:text-white text-xl leading-none">&times;</button>
                </div>

                <form wire:submit.prevent="updateCase" class="space-y-4">

                    {{-- 1. الفئة --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 mb-1">{{ __('Case Category') }}</label>
                        <select wire:model.defer="edit_community_case_category_id"
                                class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2 text-white text-sm focus:outline-none focus:border-red-600">
                            <option value="">{{ __('Choose a category...') }}</option>
                            @foreach($categories as $cat)
                                @php $t = $cat->translations->first(); @endphp
                                @if($t)
                                    <option value="{{ $cat->id }}">{{ $t->name }}</option>
                                @endif
                            @endforeach
                        </select>
                        @error('edit_community_case_category_id') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- 2. حالة القضية --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 mb-1">{{ __('Case Status') }}</label>
                        <select wire:model.defer="edit_case_status" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2 text-white text-sm focus:outline-none focus:border-red-600">
                            @foreach(\App\Enums\CommunityCaseStatus::cases() as $status)
                                <option value="{{ $status->value }}">{{ $status->label() }}</option>
                            @endforeach
                        </select>
                        @error('edit_case_status') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- 3. العنوان --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 mb-1">{{ __('Case Title') }}</label>
                        <input type="text" wire:model.defer="edit_title"
                            class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2 text-white text-sm focus:outline-none focus:border-red-600">
                        @error('edit_title') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- 4. الوصف --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 mb-1">{{ __('Case Details & Evidence') }}</label>
                        <textarea wire:model.defer="edit_body" rows="6"
                                class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2 text-white text-sm focus:outline-none focus:border-red-600"></textarea>
                        @error('edit_body') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- 5. المواقع الجغرافية (اختياري - متعدد) --}}
                    <div class="bg-slate-900/60 border border-slate-800 rounded-xl p-4 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-blue-400">📍 {{ __('Case Locations (Optional)') }}</span>
                            <button type="button" wire:click="addEditLocation"
                                    class="text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-gray-200 px-3 py-1 rounded-lg border border-slate-700 transition">
                                + {{ __('Add Location') }}
                            </button>
                        </div>

                        @foreach($edit_locations as $index => $loc)
                            <div wire:key="show-edit-loc-{{ $index }}" class="bg-slate-950/70 border border-slate-800/90 rounded-xl p-3.5 space-y-2.5">
                                <div class="flex items-center justify-between text-xs text-gray-400">
                                    <span class="font-semibold">{{ __('Location #:num', ['num' => $index + 1]) }}</span>
                                    <button type="button" wire:click="removeEditLocation({{ $index }})"
                                            class="text-rose-400 hover:text-rose-300 text-xs">
                                        {{ __('Remove') }}
                                    </button>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-gray-400 mb-1">{{ __('Location Title') }}</label>
                                    <input type="text" wire:model.defer="edit_locations.{{ $index }}.title"
                                           class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3.5 py-2 text-white text-sm focus:outline-none focus:border-red-600">
                                    @error("edit_locations.$index.title") <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-400 mb-1">{{ __('Latitude') }}</label>
                                        <input type="text" wire:model.defer="edit_locations.{{ $index }}.latitude"
                                               class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3.5 py-2 text-white text-sm font-mono focus:outline-none focus:border-red-600">
                                        @error("edit_locations.$index.latitude") <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-xs font-semibold text-gray-400 mb-1">{{ __('Longitude') }}</label>
                                        <input type="text" wire:model.defer="edit_locations.{{ $index }}.longitude"
                                               class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3.5 py-2 text-white text-sm font-mono focus:outline-none focus:border-red-600">
                                        @error("edit_locations.$index.longitude") <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-gray-400 mb-1">{{ __('Location Notes') }}</label>
                                    <textarea wire:model.defer="edit_locations.{{ $index }}.description" rows="2"
                                              class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3.5 py-2 text-white text-sm focus:outline-none focus:border-red-600"></textarea>
                                    @error("edit_locations.$index.description") <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if(!auth()->user()->hasAnyRole(['super-admin', 'editor']))
                        <div class="bg-amber-950/40 border border-amber-800/50 rounded-xl p-3 text-xs text-amber-300">
                            ⚠️ {{ __('Your edit will be submitted for review. The case will return to "pending" status.') }}
                        </div>
                    @endif

                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-800">
                        <button type="button" wire:click="closeEditModal"
                                class="px-4 py-2 bg-slate-800 text-gray-300 rounded-xl text-sm font-semibold hover:bg-slate-700 transition">
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit"
                                class="px-6 py-2 bg-blue-600 text-white rounded-xl text-sm font-semibold hover:bg-blue-700 transition shadow-lg shadow-blue-600/20">
                            {{ __('Update Case') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        (function () {
            let caseMapInstance = null;
            let markersLayer = null;

            function escapeHtml(str) {
                return String(str ?? '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            function extractLocations(payload) {
                if (Array.isArray(payload)) return payload;
                if (Array.isArray(payload?.locations)) return payload.locations;
                if (Array.isArray(payload?.[0]?.locations)) return payload[0].locations;
                if (Array.isArray(payload?.[0])) return payload[0];
                return null;
            }

            function destroyMapInstance() {
                if (caseMapInstance) {
                    caseMapInstance.remove();
                    caseMapInstance = null;
                    markersLayer = null;
                }
                const mapEl = document.getElementById('community-case-map');
                if (mapEl && mapEl._leaflet_id) {
                    delete mapEl._leaflet_id;
                    mapEl.innerHTML = '';
                }
            }

            function renderCaseMap(overrideLocations = null) {
                const mapEl = document.getElementById('community-case-map');
                if (!mapEl || typeof L === 'undefined') return;

                let locations = extractLocations(overrideLocations);
                if (!Array.isArray(locations)) {
                    try {
                        locations = JSON.parse(mapEl.dataset.locations || '[]');
                    } catch (e) {
                        locations = [];
                    }
                }

                if (!Array.isArray(locations) || locations.length === 0) return;

                // إذا كان العنصر مهيأً من قبل لكن caseMapInstance غير موجود، نعيد تهيئته بأمان
                if (!caseMapInstance && mapEl._leaflet_id) {
                    delete mapEl._leaflet_id;
                    mapEl.innerHTML = '';
                }

                if (!caseMapInstance) {
                    const firstLat = parseFloat(locations[0].latitude);
                    const firstLng = parseFloat(locations[0].longitude);
                    if (Number.isNaN(firstLat) || Number.isNaN(firstLng)) return;

                    caseMapInstance = L.map(mapEl).setView([firstLat, firstLng], 12);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '© OpenStreetMap'
                    }).addTo(caseMapInstance);

                    markersLayer = L.layerGroup().addTo(caseMapInstance);
                } else {
                    markersLayer.clearLayers();
                }

                const bounds = [];
                locations.forEach(function (loc) {
                    const lat = parseFloat(loc.latitude);
                    const lng = parseFloat(loc.longitude);
                    if (Number.isNaN(lat) || Number.isNaN(lng)) return;

                    bounds.push([lat, lng]);

                    const title = escapeHtml(loc.title || 'Location');
                    const desc = escapeHtml(loc.description || '');

                    const popupHtml = `
                        <div class="p-2 max-w-xs space-y-1.5 text-right dir-rtl">
                            <span class="text-[10px] font-bold text-red-600 uppercase bg-red-50 px-2 py-0.5 rounded">Case Location</span>
                            <h3 class="font-bold text-sm text-slate-800 leading-snug">${title}</h3>
                            ${desc ? `<p class="text-xs text-slate-600 leading-relaxed">${desc}</p>` : ''}
                            <div class="text-[11px] font-mono text-slate-500 pt-1">📍 ${lat}, ${lng}</div>
                        </div>
                    `;

                    try {
                        L.marker([lat, lng]).addTo(markersLayer).bindPopup(popupHtml);
                    } catch (e) {
                        // تجاهل أي استثناء عابر أثناء تحديث الطبقة
                    }
                });

                setTimeout(function () {
                    if (!caseMapInstance || !caseMapInstance._container || !caseMapInstance.getPanes()?.mapPane) {
                        return;
                    }

                    try {
                        caseMapInstance.invalidateSize({ animate: false });

                        if (bounds.length === 1) {
                            caseMapInstance.setView(bounds[0], 13, { animate: false });
                        } else if (bounds.length > 1) {
                            caseMapInstance.fitBounds(bounds, { padding: [40, 40], maxZoom: 13, animate: false });
                        }
                    } catch (e) {
                        // إعادة تهيئة نظيفة إذا كان الكائن الداخلي لـ Leaflet قد فسد بعد رندر Livewire
                        destroyMapInstance();
                        renderCaseMap(locations);
                    }
                }, 150);
            }

            document.addEventListener('DOMContentLoaded', function () {
                renderCaseMap();
            });

            document.addEventListener('livewire:init', function () {
                Livewire.on('case-locations-updated', function (data) {
                    renderCaseMap(extractLocations(data));
                });
            });

            document.addEventListener('livewire:navigated', function () {
                destroyMapInstance();
                renderCaseMap();
            });

            window.addEventListener('case-locations-updated', function (event) {
                renderCaseMap(extractLocations(event.detail));
            });
        })();
    </script>
@endpush