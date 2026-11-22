@extends('layouts.admin.master')
@section('title', 'Article & Case Locations Management')

@push('styles')
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #map, #public-investigation-map { 
            height: 420px; 
            width: 100%; 
            border-radius: 0.75rem; 
            z-index: 1 !important; 
            background-color: #1e293b !important; /* Dark background while loading */
        }
        .leaflet-pane { z-index: 1 !important; }
        .leaflet-top, .leaflet-bottom { z-index: 2 !important; }

        /* Free 100% dark mode tile filter without API key */
        .leaflet-tile-pane {
            filter: brightness(0.6) invert(1) contrast(3) hue-rotate(200deg) saturate(0.3) brightness(0.7);
        }
        
        /* Keep markers and popups in their natural colors */
        .leaflet-marker-icon, .leaflet-popup {
            filter: invert(1) hue-rotate(200deg) saturate(3);
        }
    </style>
@endpush

@section('content')
<div class="container mx-auto pb-8 px-4">
    
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-xl font-bold text-slate-800">Article & Case Locations Management</h1>
        <p class="text-xs text-slate-500">Select an article, click on the map to automatically capture coordinates, and add translations for each language.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Left Column: Articles List -->
        <div class="lg:col-span-4 bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <h2 class="text-sm font-bold text-slate-700 mb-3 border-b border-slate-100 pb-2">Select an Article to Assign Location</h2>
            
            <div class="space-y-2 max-h-[600px] overflow-y-auto pr-1">
                @foreach($articles as $art)
                    <a href="{{ route('admin.locations.index', ['article_id' => $art->id]) }}" 
                       class="block p-3 rounded-xl border transition-all {{ optional($selectedArticle)->id === $art->id ? 'bg-indigo-50 border-indigo-300 ring-1 ring-indigo-300' : 'bg-slate-50 border-slate-200 hover:bg-slate-100' }}">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-semibold text-slate-800 truncate">{{ $art->label }}</span>
                            <span class="bg-indigo-100 text-indigo-700 text-[10px] font-bold px-2 py-0.5 rounded-full">
                                {{ $art->locations->count() }} {{ Str::plural('location', $art->locations->count()) }}
                            </span>
                        </div>
                        <span class="text-[11px] text-slate-500 block">Category: {{ $art->category->label ?? 'Uncategorized' }}</span>
                    </a>
                @endforeach
            </div>

            <div class="mt-4">
                {{ $articles->links() }}
            </div>
        </div>

        <!-- Right Column: Map & Locations for Selected Article -->
        <div class="lg:col-span-8 space-y-6">
            @if($selectedArticle)
                
                <!-- Selected Article Details & Existing Locations -->
                <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm space-y-4">
                    <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                        <div>
                            <h2 class="text-base font-bold text-slate-800">Selected Article: <span class="text-indigo-600">{{ $selectedArticle->label }}</span></h2>
                            <p class="text-xs text-slate-500">Click anywhere on the map below to capture coordinates directly.</p>
                        </div>
                    </div>

                    <!-- Interactive Map -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-2">Interactive Map (Click to pin a location)</label>
                        <div id="map"></div>
                    </div>

                    @if($selectedArticle->locations->count() > 0)
                        <div class="pt-3 border-t border-slate-100" x-data="{ editingLocation: null }">
                            <h3 class="text-xs font-bold text-slate-700 mb-2">Currently Recorded Locations for This Article:</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @foreach($selectedArticle->locations as $loc)
                                    @php
                                        // Group existing translations by locale
                                        $existingTrans = $loc->translations->keyBy('locale');
                                    @endphp
                                    <div class="bg-slate-50 border border-slate-200 p-3 rounded-xl space-y-2 text-xs">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <span class="font-bold text-slate-800 block">
                                                    {{ $loc->translation->title ?? '📍 (Untitled Location)' }}
                                                </span>
                                                <span class="text-[10px] font-mono text-slate-400">Lat: {{ $loc->latitude }}, Lng: {{ $loc->longitude }}</span>
                                            </div>
                                            <div class="flex items-center gap-1">
                                                <button type="button" @click="editingLocation = {{ $loc->id }}" class="bg-indigo-50 text-indigo-600 hover:bg-indigo-100 px-2 py-1 rounded text-xs font-semibold">
                                                    Edit / Locales
                                                </button>
                                                <form action="{{ route('admin.locations.destroy', $loc->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this location?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-bold px-1.5 py-1">&times;</button>
                                                </form>
                                            </div>
                                        </div>

                                        <!-- Edit Location Modal -->
                                        <div x-show="editingLocation === {{ $loc->id }}" x-cloak class="fixed inset-0 z-[99999] bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4 !m-0">
                                            <div @click.away="editingLocation = null" class="bg-white rounded-2xl max-w-xl w-full p-5 space-y-4 shadow-xl max-h-[90vh] overflow-y-auto">
                                                <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                                                    <h4 class="font-bold text-slate-800 text-sm">Edit Location Details (ID: {{ $loc->id }})</h4>
                                                    <button type="button" @click="editingLocation = null" class="text-slate-400 hover:text-slate-600 font-bold">&times;</button>
                                                </div>

                                                <form action="{{ route('admin.locations.update', $loc->id) }}" method="POST" class="space-y-4">
                                                    @csrf
                                                    @method('PUT')

                                                    <div class="grid grid-cols-2 gap-2">
                                                        <div>
                                                            <label class="block text-[11px] text-slate-500 mb-1">Latitude</label>
                                                            <input type="text" name="latitude" value="{{ $loc->latitude }}" class="w-full rounded-lg border border-slate-300 text-xs py-1.5 px-2.5 font-mono">
                                                        </div>
                                                        <div>
                                                            <label class="block text-[11px] text-slate-500 mb-1">Longitude</label>
                                                            <input type="text" name="longitude" value="{{ $loc->longitude }}" class="w-full rounded-lg border border-slate-300 text-xs py-1.5 px-2.5 font-mono">
                                                        </div>
                                                    </div>

                                                    <div class="space-y-3">
                                                        <span class="text-xs font-bold text-slate-700 block">Add / Edit Translations:</span>
                                                        @foreach($locales as $code => $name)
                                                            <div class="bg-slate-50 p-2.5 rounded-lg border border-slate-200 space-y-1.5">
                                                                <span class="text-[10px] font-bold text-indigo-700 uppercase block">{{ $name }} ({{ $code }})</span>
                                                                <input type="text" 
                                                                    name="translations[{{ $code }}][title]" 
                                                                    value="{{ $existingTrans[$code]->title ?? '' }}" 
                                                                    placeholder="Location title in this language..." 
                                                                    class="w-full rounded-lg border border-slate-300 text-xs py-1 px-2">
                                                                <textarea name="translations[{{ $code }}][description]" 
                                                                        rows="2" 
                                                                        placeholder="Location description in this language..." 
                                                                        class="w-full rounded-lg border border-slate-300 text-xs py-1 px-2">{{ $existingTrans[$code]->description ?? '' }}</textarea>
                                                            </div>
                                                        @endforeach
                                                    </div>

                                                    <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                                                        <button type="button" @click="editingLocation = null" class="bg-slate-100 text-slate-600 px-3 py-1.5 rounded-lg text-xs font-semibold">Cancel</button>
                                                        <button type="submit" class="bg-indigo-600 text-white px-4 py-1.5 rounded-lg text-xs font-semibold hover:bg-indigo-700">Save Changes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>

                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                </div>

                <!-- Add New Location & Translations Form -->
                <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                    <h3 class="text-sm font-bold text-slate-800 mb-4 pb-2 border-b border-slate-100">Add New Location & Translations</h3>
                    
                    <form action="{{ route('admin.locations.store', $selectedArticle->id) }}" method="POST" class="space-y-4">
                        @csrf
                        
                        <!-- Coordinates (Auto-filled on map click or entered manually) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 bg-indigo-50/50 p-3 rounded-xl border border-indigo-100">
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Latitude <span class="text-[10px] text-slate-400">(Click map or type manually)</span></label>
                                <input type="text" name="latitude" id="lat_input" class="w-full bg-white rounded-lg border border-slate-300 text-xs py-2 px-3 outline-none focus:border-indigo-500 font-mono" placeholder="33.3152..." required>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Longitude <span class="text-[10px] text-slate-400">(Click map or type manually)</span></label>
                                <input type="text" name="longitude" id="lng_input" class="w-full bg-white rounded-lg border border-slate-300 text-xs py-2 px-3 outline-none focus:border-indigo-500 font-mono" placeholder="44.3661..." required>
                            </div>
                        </div>

                        <!-- Per-Language Sections -->
                        <div class="space-y-3 pt-2">
                            <span class="text-xs font-bold text-slate-700 block">Title & Description per Language:</span>
                            
                            @php
                                // Fetch supported locales dynamically from mcamara package
                                $locales = [];
                                if (class_exists('Mcamara\LaravelLocalization\Facades\LaravelLocalization')) {
                                    foreach (\Mcamara\LaravelLocalization\Facades\LaravelLocalization::getSupportedLocales() as $localeCode => $properties) {
                                        $locales[$localeCode] = $properties['native'] ?? $properties['name'];
                                    }
                                } else {
                                    // Fallback values if package is not found
                                    $locales = ['ar' => 'العربية', 'en' => 'English'];
                                }
                            @endphp

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($locales as $code => $name)
                                    <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200 space-y-2">
                                        <div class="flex items-center justify-between border-b border-slate-200 pb-1">
                                            <span class="text-xs font-bold text-indigo-700 uppercase">{{ $name }} ({{ $code }})</span>
                                            <span class="text-[10px] bg-slate-200 text-slate-600 font-semibold px-2 py-0.5 rounded">Optional</span>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-[11px] text-slate-500 mb-1">Location Title</label>
                                            <input type="text" 
                                                name="translations[{{ $code }}][title]" 
                                                class="w-full rounded-lg border border-slate-300 text-xs py-1.5 px-2.5 outline-none focus:border-indigo-500" 
                                                placeholder="e.g., Primary Crime Scene">
                                        </div>

                                        <div>
                                            <label class="block text-[11px] text-slate-500 mb-1">Location Description (Optional)</label>
                                            <textarea name="translations[{{ $code }}][description]" 
                                                    rows="2" 
                                                    class="w-full rounded-lg border border-slate-300 text-xs py-1.5 px-2.5 outline-none focus:border-indigo-500" 
                                                    placeholder="Additional details about this location..."></textarea>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex justify-end pt-3 border-t border-slate-100">
                            <button type="submit" class="bg-indigo-600 text-white font-semibold px-5 py-2 rounded-xl hover:bg-indigo-700 text-xs transition shadow-sm">
                                Save Location & Translations
                            </button>
                        </div>
                    </form>
                </div>

            @else
                <div class="bg-white border border-slate-200 rounded-2xl p-12 text-center text-slate-400">
                    <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    </svg>
                    <p class="text-sm font-semibold text-slate-600">Please select an article from the sidebar to assign its geographic location and translations.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    @if($selectedArticle)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var defaultLat = 33.3152;
            var defaultLng = 44.3661;
            
            var map = L.map('map').setView([defaultLat, defaultLng], 5);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap'
            }).addTo(map);

            var activeMarker = null;
            var latInput = document.getElementById('lat_input');
            var lngInput = document.getElementById('lng_input');

            // Update or create marker on the map
            function updateMarker(lat, lng) {
                if (!isNaN(lat) && !isNaN(lng) && lat !== '' && lng !== '') {
                    if (activeMarker) {
                        map.removeLayer(activeMarker);
                    }
                    activeMarker = L.marker([lat, lng]).addTo(map);
                }
            }

            // Display pre-existing locations for this article on the map
            var existingLocations = @json($selectedArticle->locations->map(function($l) {
                return [
                    'lat' => $l->latitude,
                    'lng' => $l->longitude,
                    'title' => $l->translation->title ?? 'Location'
                ];
            }));

            existingLocations.forEach(function(loc) {
                L.marker([loc.lat, loc.lng])
                    .addTo(map)
                    .bindPopup('<b>' + loc.title + '</b>');
            });

            // 1. Capture coordinates automatically on map click
            map.on('click', function(e) {
                var lat = e.latlng.lat.toFixed(8);
                var lng = e.latlng.lng.toFixed(8);

                latInput.value = lat;
                lngInput.value = lng;

                updateMarker(lat, lng);
                if (activeMarker) {
                    activeMarker.bindPopup('Currently selected point').openPopup();
                }
            });

            // 2. Move marker automatically on manual coordinate input
            function handleManualInput() {
                var lat = parseFloat(latInput.value);
                var lng = parseFloat(lngInput.value);
                if (!isNaN(lat) && !isNaN(lng)) {
                    updateMarker(lat, lng);
                }
            }

            if (latInput && lngInput) {
                latInput.addEventListener('input', handleManualInput);
                lngInput.addEventListener('input', handleManualInput);
            }
        });
    </script>
    @endif
@endpush