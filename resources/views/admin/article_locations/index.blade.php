@extends('layouts.admin.master')
@section('title', 'إدارة مواقع القضايا')

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
<div class="container mx-auto pb-8 px-4">
    
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-xl font-bold text-slate-800">إدارة مواقع المقالات والقضايا</h1>
        <p class="text-xs text-slate-500">اختر المقال، انقر على الخريطة لتحديد الموقع تلقائياً، وأضف الترجمات لكل لغة.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- العمود الأيسر: قائمة المقالات -->
        <div class="lg:col-span-4 bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <h2 class="text-sm font-bold text-slate-700 mb-3 border-b border-slate-100 pb-2">اختر مقالاً لتحديد موقعه</h2>
            
            <div class="space-y-2 max-h-[600px] overflow-y-auto pr-1">
                @foreach($articles as $art)
                    <a href="{{ route('admin.article-locations.index', ['article_id' => $art->id]) }}" 
                       class="block p-3 rounded-xl border transition-all {{ optional($selectedArticle)->id === $art->id ? 'bg-indigo-50 border-indigo-300 ring-1 ring-indigo-300' : 'bg-slate-50 border-slate-200 hover:bg-slate-100' }}">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-semibold text-slate-800 truncate">{{ $art->internal_title }}</span>
                            <span class="bg-indigo-100 text-indigo-700 text-[10px] font-bold px-2 py-0.5 rounded-full">
                                {{ $art->locations->count() }} موقع
                            </span>
                        </div>
                        <span class="text-[11px] text-slate-500 block">التصنيف: {{ $art->category->label ?? 'غير محدد' }}</span>
                    </a>
                @endforeach
            </div>

            <div class="mt-4">
                {{ $articles->links() }}
            </div>
        </div>

        <!-- العمود الأيمن: الخريطة وإضافة المواقع للمقال المختار -->
        <div class="lg:col-span-8 space-y-6">
            @if($selectedArticle)
                
                <!-- تفاصيل المقال المختار والمواقع الحالية -->
                <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm space-y-4">
                    <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                        <div>
                            <h2 class="text-base font-bold text-slate-800">المقال المحدد: <span class="text-indigo-600">{{ $selectedArticle->internal_title }}</span></h2>
                            <p class="text-xs text-slate-500">انقر على الخريطة أدناه لاقتناص الإحداثيات مباشرة.</p>
                        </div>
                    </div>


                    <!-- الخريطة التفاعلية -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-2">الخريطة (اضغط لتحديد النقطة)</label>
                        <div id="map"></div>
                    </div>



                    {{--
                    <!-- المواقع الحالية المسجلة لهذا المقال -->
                    @if($selectedArticle->locations->count() > 0)
                        <div class="pt-3 border-t border-slate-100">
                            <h3 class="text-xs font-bold text-slate-700 mb-2">المواقع المسجلة حالياً لهذا المقال:</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                @foreach($selectedArticle->locations as $loc)
                                    <div class="bg-slate-50 border border-slate-200 p-2.5 rounded-xl flex items-center justify-between text-xs">
                                        <div>
                                            <span class="font-bold text-slate-700 block">{{ $loc->translation->title ?? 'موقع بدون عنوان' }}</span>
                                            <span class="text-[10px] font-mono text-slate-400">Lat: {{ $loc->latitude }}, Lng: {{ $loc->longitude }}</span>
                                        </div>
                                        <form action="{{ route('admin.article-locations.destroy', $loc->id) }}" method="POST" onsubmit="return confirm('هل أنت تأكد من حذف هذا الموقع؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-bold px-2 py-1">حذف</button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    --}}


                    @if($selectedArticle->locations->count() > 0)
                        <div class="pt-3 border-t border-slate-100" x-data="{ editingLocation: null }">
                            <h3 class="text-xs font-bold text-slate-700 mb-2">المواقع المسجلة حالياً لهذا المقال:</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @foreach($selectedArticle->locations as $loc)
                                    @php
                                        // تجميع الترجمات الحالية للموقع
                                        $existingTrans = $loc->translations->keyBy('locale');
                                    @endphp
                                    <div class="bg-slate-50 border border-slate-200 p-3 rounded-xl space-y-2 text-xs">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <span class="font-bold text-slate-800 block">
                                                    {{ $loc->translation->title ?? '📍 (موقع بدون عنوان)' }}
                                                </span>
                                                <span class="text-[10px] font-mono text-slate-400">Lat: {{ $loc->latitude }}, Lng: {{ $loc->longitude }}</span>
                                            </div>
                                            <div class="flex items-center gap-1">
                                                <button type="button" @click="editingLocation = {{ $loc->id }}" class="bg-indigo-50 text-indigo-600 hover:bg-indigo-100 px-2 py-1 rounded text-xs font-semibold">
                                                    تعديل / إضافة لغات
                                                </button>
                                                <form action="{{ route('admin.article-locations.destroy', $loc->id) }}" method="POST" onsubmit="return confirm('هل أنت تأكد من حذف هذا الموقع؟')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-bold px-1.5 py-1">&times;</button>
                                                </form>
                                            </div>
                                        </div>

                                        <!-- نافذة أو نموذج تعديل الموقع المختار (Modal) -->
                                        <div x-show="editingLocation === {{ $loc->id }}" x-cloak class="fixed inset-0 z-[99999] bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4 !m-0">
                                            <div @click.away="editingLocation = null" class="bg-white  max-w-xl w-full p-5 space-y-4 shadow-xl max-h-[90vh] overflow-y-auto">
                                                <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                                                    <h4 class="font-bold text-slate-800 text-sm">تعديل بيانات الموقع (ID: {{ $loc->id }})</h4>
                                                    <button type="button" @click="editingLocation = null" class="text-slate-400 hover:text-slate-600 font-bold">&times;</button>
                                                </div>

                                                <form action="{{ route('admin.article-locations.update', $loc->id) }}" method="POST" class="space-y-4">
                                                    @csrf
                                                    @method('PUT')

                                                    <div class="grid grid-cols-2 gap-2">
                                                        <div>
                                                            <label class="block text-[11px] text-slate-500 mb-1">Lat</label>
                                                            <input type="text" name="latitude" value="{{ $loc->latitude }}" class="w-full rounded-lg border border-slate-300 text-xs py-1.5 px-2.5">
                                                        </div>
                                                        <div>
                                                            <label class="block text-[11px] text-slate-500 mb-1">Lng</label>
                                                            <input type="text" name="longitude" value="{{ $loc->longitude }}" class="w-full rounded-lg border border-slate-300 text-xs py-1.5 px-2.5">
                                                        </div>
                                                    </div>

                                                    <div class="space-y-3">
                                                        <span class="text-xs font-bold text-slate-700 block">إدخال/تعديل اللغات:</span>
                                                        @foreach($locales as $code => $name)
                                                            <div class="bg-slate-50 p-2.5 rounded-lg border border-slate-200 space-y-1.5">
                                                                <span class="text-[10px] font-bold text-indigo-700 uppercase block">{{ $name }} ({{ $code }})</span>
                                                                <input type="text" 
                                                                    name="translations[{{ $code }}][title]" 
                                                                    value="{{ $existingTrans[$code]->title ?? '' }}" 
                                                                    placeholder="مسمى الموقع بهذه اللغة..." 
                                                                    class="w-full rounded-lg border border-slate-300 text-xs py-1 px-2">
                                                                <textarea name="translations[{{ $code }}][description]" 
                                                                        rows="2" 
                                                                        placeholder="وصف الموقع بهذه اللغة..." 
                                                                        class="w-full rounded-lg border border-slate-300 text-xs py-1 px-2">{{ $existingTrans[$code]->description ?? '' }}</textarea>
                                                            </div>
                                                        @endforeach
                                                    </div>

                                                    <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                                                        <button type="button" @click="editingLocation = null" class="bg-slate-100 text-slate-600 px-3 py-1.5 rounded-lg text-xs font-semibold">إلغاء</button>
                                                        <button type="submit" class="bg-indigo-600 text-white px-4 py-1.5 rounded-lg text-xs font-semibold hover:bg-indigo-700">حفظ التغييرات</button>
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

                <!-- نموذج إضافة موقع جديد وترجماته -->
                <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                    <h3 class="text-sm font-bold text-slate-800 mb-4 pb-2 border-b border-slate-100">إضافة موقع جديد وترجماته</h3>
                    
                    <form action="{{ route('admin.article-locations.store', $selectedArticle->id) }}" method="POST" class="space-y-4">
                        @csrf
                        
                        <!-- الإحداثيات (تعبأ تلقائياً عند النقر على الخريطة أو يدوياً بالكتابة) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 bg-indigo-50/50 p-3 rounded-xl border border-indigo-100">
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">خط العرض (Latitude) <span class="text-[10px] text-slate-400">(انقر الخريطة أو اكتب يدوياً)</span></label>
                                <input type="text" name="latitude" id="lat_input" class="w-full bg-white rounded-lg border border-slate-300 text-xs py-2 px-3 outline-none focus:border-indigo-500 font-mono" placeholder="33.3152..." required>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">خط الطول (Longitude) <span class="text-[10px] text-slate-400">(انقر الخريطة أو اكتب يدوياً)</span></label>
                                <input type="text" name="longitude" id="lng_input" class="w-full bg-white rounded-lg border border-slate-300 text-xs py-2 px-3 outline-none focus:border-indigo-500 font-mono" placeholder="44.3661..." required>
                            </div>
                        </div>

                        <!-- التبويبات أو الأقسام المخصصة لكل لغة -->
                        <div class="space-y-3 pt-2">
                            <span class="text-xs font-bold text-slate-700 block">العنوان والوصف لكل لغة:</span>
                            
                            @php
                                // جلب اللغات المدعومة ديناميكياً من حزمة mcamara
                                $locales = [];
                                if (class_exists('Mcamara\LaravelLocalization\Facades\LaravelLocalization')) {
                                    foreach (\Mcamara\LaravelLocalization\Facades\LaravelLocalization::getSupportedLocales() as $localeCode => $properties) {
                                        $locales[$localeCode] = $properties['native'] ?? $properties['name'];
                                    }
                                } else {
                                    // قيمة احتياطية في حال لم يتم العثور على الحزمة
                                    $locales = ['ar' => 'العربية', 'en' => 'English'];
                                }
                            @endphp

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($locales as $code => $name)
                                    <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200 space-y-2">
                                        <div class="flex items-center justify-between border-b border-slate-200 pb-1">
                                            <span class="text-xs font-bold text-indigo-700 uppercase">{{ $name }} ({{ $code }})</span>
                                            <span class="text-[10px] bg-slate-200 text-slate-600 font-semibold px-2 py-0.5 rounded">اختياري</span>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-[11px] text-slate-500 mb-1">مسمى الموقع</label>
                                            <input type="text" 
                                                name="translations[{{ $code }}][title]" 
                                                class="w-full rounded-lg border border-slate-300 text-xs py-1.5 px-2.5 outline-none focus:border-indigo-500" 
                                                placeholder="مثال: مسرح الجريمة الرئيسي">
                                        </div>

                                        <div>
                                            <label class="block text-[11px] text-slate-500 mb-1">وصف الموقع (اختياري)</label>
                                            <textarea name="translations[{{ $code }}][description]" 
                                                    rows="2" 
                                                    class="w-full rounded-lg border border-slate-300 text-xs py-1.5 px-2.5 outline-none focus:border-indigo-500" 
                                                    placeholder="تفاصيل إضافية حول هذا الموقع..."></textarea>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex justify-end pt-3 border-t border-slate-100">
                            <button type="submit" class="bg-indigo-600 text-white font-semibold px-5 py-2 rounded-xl hover:bg-indigo-700 text-xs transition shadow-sm">
                                حفظ الموقع والترجمات
                            </button>
                        </div>
                    </form>
                </div>

            @else
                <div class="bg-white border border-slate-200 rounded-2xl p-12 text-center text-slate-400">
                    <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    </svg>
                    <p class="text-sm font-semibold text-slate-600">يرجى اختيار مقال من القائمة الجانبية للبدء بتحديد موقعه الجغرافي وترجماته.</p>
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

            // دالة لتحديث أو إنشاء المؤشر على الخريطة
            function updateMarker(lat, lng) {
                if (!isNaN(lat) && !isNaN(lng) && lat !== '' && lng !== '') {
                    if (activeMarker) {
                        map.removeLayer(activeMarker);
                    }
                    activeMarker = L.marker([lat, lng]).addTo(map);
                    //map.setView([lat, lng], 10); // تقريب الخريطة للموقع المدخل
                }
            }

            // عرض المواقع المسجلة مسبقاً لهذا المقال على الخريطة
            var existingLocations = @json($selectedArticle->locations->map(function($l) {
                return [
                    'lat' => $l->latitude,
                    'lng' => $l->longitude,
                    'title' => $l->translation->title ?? 'موقع'
                ];
            }));

            existingLocations.forEach(function(loc) {
                L.marker([loc.lat, loc.lng])
                    .addTo(map)
                    .bindPopup('<b>' + loc.title + '</b>');
            });

            // 1. عند النقر على الخريطة لتحديد النقطة (تعبئة الحقول تلقائياً)
            map.on('click', function(e) {
                var lat = e.latlng.lat.toFixed(8);
                var lng = e.latlng.lng.toFixed(8);

                latInput.value = lat;
                lngInput.value = lng;

                updateMarker(lat, lng);
                if (activeMarker) {
                    activeMarker.bindPopup('النقطة المحددة حالياً').openPopup();
                }
            });

            // 2. عند الكتابة اليدوية للإحداثيات في الحقول (تحريك المؤشر على الخريطة تلقائياً)
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