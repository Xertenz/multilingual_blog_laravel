@extends('layouts.main')
@section('title', 'Investigations & Cases Map')

@push('styles')
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #map, #public-investigation-map {
            height: 420px;
            width: 100%;
            border-radius: 0.75rem;
            z-index: 1 !important;
            background-color: #1e293b !important;
        }
        .leaflet-pane { z-index: 1 !important; }
        .leaflet-top, .leaflet-bottom { z-index: 2 !important; }

        /* Night mode filter */
        .leaflet-tile-pane {
            filter: brightness(0.6) invert(1) contrast(3) hue-rotate(200deg) saturate(0.3) brightness(0.7);
        }

        .leaflet-marker-icon, .leaflet-popup {
            filter: invert(1) hue-rotate(200deg) saturate(3);
        }
    </style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8">

    <!-- Header — موحّد مع بقية الصفحات -->
    <div class="bg-[#0f1526] border border-slate-800 rounded-xl px-5 py-4 mb-6 shadow-md flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="space-y-1">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 bg-red-600 rounded-sm shrink-0"></span>
                <h1 class="text-lg md:text-xl font-bold text-white tracking-wide">
                    🗺️ Cases & Investigations Map
                </h1>
            </div>
            <p class="text-gray-400 text-xs max-w-2xl leading-relaxed">
                Explore the geographic scope of real stories and events through the interactive map.
            </p>
        </div>

        <div class="shrink-0 self-start sm:self-center">
            <span class="inline-block px-2.5 py-1 bg-slate-900 text-gray-300 text-[11px] font-mono rounded-md border border-slate-800">
                {{ count($mapData) }} {{ __('Locations') }}
            </span>
        </div>
    </div>

    <!-- Map Canvas -->
    <div class="p-[2px] rounded-xl bg-red-600 shadow-sm">
        <div id="public-investigation-map"></div>
    </div>

</div>
@endsection

@push('scripts')
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var mapLocations = @json($mapData);

            var defaultLat = 33.3152;
            var defaultLng = 44.3661;
            var defaultZoom = 4;

            var map = L.map('public-investigation-map').setView([defaultLat, defaultLng], defaultZoom);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap'
            }).addTo(map);

            var bounds = [];

            mapLocations.forEach(function(item) {
                var lat = item.latitude;
                var lng = item.longitude;

                bounds.push([lat, lng]);

                var popupHtml = `
                    <div class="p-2 max-w-xs space-y-2 text-right dir-rtl">
                        <span class="text-[10px] font-bold text-indigo-600 uppercase bg-indigo-50 px-2 py-0.5 rounded">Case/Investigation</span>
                        <h3 class="font-bold text-sm text-slate-800 leading-snug mt-1">${item.article_title}</h3>
                        ${item.location_title !== item.article_title ? `<p class="text-xs font-semibold text-slate-600">📍 ${item.location_title}</p>` : ''}
                        ${item.description ? `<p class="text-xs text-slate-500 line-clamp-2">${item.description}</p>` : ''}
                        <div class="pt-2 border-t border-slate-100 flex justify-end">
                            <a href="${item.url}" class="inline-flex items-center gap-1 text-xs font-bold text-indigo-600 hover:text-indigo-800">
                                Read full investigation &larr;
                            </a>
                        </div>
                    </div>
                `;

                L.marker([lat, lng])
                    .addTo(map)
                    .bindPopup(popupHtml);
            });

            if (bounds.length > 0) {
                map.fitBounds(bounds, { padding: [50, 50], maxZoom: 12 });
            }
        });
    </script>
@endpush