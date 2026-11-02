@extends('layouts.main')
@section('title', 'خريطة التحقيقات والقضايا')

@push('styles')
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        /*
        #public-investigation-map {
            height: 78vh;
            width: 100%;
            border-radius: 1rem;
            z-index: 1 !important;
        }
        .leaflet-popup-content-wrapper {
            border-radius: 0.75rem;
            padding: 4px;
        }
        */
    </style>
@endpush

@push('styles')
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #map, #public-investigation-map { 
            height: 420px; 
            width: 100%; 
            border-radius: 0.75rem; 
            z-index: 1 !important; 
            background-color: #1e293b !important; /* لون خلفية داكن أثناء التحميل */
        }
        .leaflet-pane { z-index: 1 !important; }
        .leaflet-top, .leaflet-bottom { z-index: 2 !important; }

        /* فلتر الوضع الليلي المضمون والمجاني 100% بدون API Key */
        .leaflet-tile-pane {
            filter: brightness(0.6) invert(1) contrast(3) hue-rotate(200deg) saturate(0.3) brightness(0.7);
        }
        
        /* الحفاظ على ظهور المؤشرات والنوافذ بألوانها الطبيعية الواضحة */
        .leaflet-marker-icon, .leaflet-popup {
            filter: invert(1) hue-rotate(200deg) saturate(3);
        }
    </style>
@endpush

@section('content')
<div class="container mx-auto py-6 px-4">
    
    <!-- Header -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">🗺️ خريطة القضايا والتحقيقات</h1>
            <p class="text-xs text-slate-500 mt-1">استكشف النطاق الجغرافي للقصص والأحداث الحقيقية عبر الخريطة التفاعلية.</p>
        </div>
        <div class="text-xs bg-indigo-50 text-indigo-700 font-semibold px-3 py-1.5 rounded-full border border-indigo-100 self-start">
            إجمالي المواقع المسجلة: <span class="font-bold">{{ count($mapData) }}</span>
        </div>
    </div>

    <!-- Map Canvas -->
    <div class="p-[2px] rounded-xl  bg-red-600 shadow-sm">
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

            // الإحداثيات الافتراضية
            var defaultLat = 33.3152;
            var defaultLng = 44.3661;
            var defaultZoom = 4;

            var map = L.map('public-investigation-map').setView([defaultLat, defaultLng], defaultZoom);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap'
            }).addTo(map);

            var bounds = [];

            // إضافة دبابيس المواقع على الخريطة
            mapLocations.forEach(function(item) {
                var lat = item.latitude;
                var lng = item.longitude;

                bounds.push([lat, lng]);

                var popupHtml = `
                    <div class="p-2 max-w-xs space-y-2 text-right dir-rtl">
                        <span class="text-[10px] font-bold text-indigo-600 uppercase bg-indigo-50 px-2 py-0.5 rounded">قضية/تحقيق</span>
                        <h3 class="font-bold text-sm text-slate-800 leading-snug mt-1">${item.article_title}</h3>
                        ${item.location_title !== item.article_title ? `<p class="text-xs font-semibold text-slate-600">📍 ${item.location_title}</p>` : ''}
                        ${item.description ? `<p class="text-xs text-slate-500 line-clamp-2">${item.description}</p>` : ''}
                        <div class="pt-2 border-t border-slate-100 flex justify-end">
                            <a href="${item.url}" class="inline-flex items-center gap-1 text-xs font-bold text-indigo-600 hover:text-indigo-800">
                                قراءة التحقيق كاملًا &larr;
                            </a>
                        </div>
                    </div>
                `;

                L.marker([lat, lng])
                    .addTo(map)
                    .bindPopup(popupHtml);
            });

            // ضبط زاوية الخريطة تلقائياً لتشمل كافة الدبابيس المتاحة
            if (bounds.length > 0) {
                map.fitBounds(bounds, { padding: [50, 50], maxZoom: 12 });
            }
        });
    </script>
@endpush