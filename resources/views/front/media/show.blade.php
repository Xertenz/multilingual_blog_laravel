{{-- 
@extends('layouts.main')

@section('title', $currentTranslation->title ?? 'تفاصيل العمل')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="bg-slate-800 border border-slate-700 rounded-2xl overflow-hidden shadow-2xl p-6 md:p-8 space-y-6">
        
        <!-- رأس الصفحة / البوستر والمعلومات -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-start">
            <div class="md:col-span-1">
                @if($media->hasMedia('poster_imgs'))
                    <img src="{{ $media->getFirstMediaUrl('poster_imgs') }}" class="w-full rounded-xl shadow-lg border border-slate-700 object-cover" alt="Poster">
                @else
                    <div class="w-full h-80 bg-slate-900 rounded-xl flex items-center justify-center text-slate-600">لا توجد صورة</div>
                @endif
            </div>

            <div class="md:col-span-2 space-y-4">
                <div class="flex flex-wrap items-center gap-3">
                    <span class="bg-indigo-600/20 text-indigo-400 border border-indigo-500/30 text-xs font-bold px-3 py-1 rounded-full uppercase">
                        {{ $media->type }}
                    </span>
                    @if($media->release_year)
                        <span class="text-slate-400 text-sm font-medium">سنة الإصدار: {{ $media->release_year }}</span>
                    @endif
                    @if($media->imdb_rating)
                        <span class="text-amber-400 text-sm font-bold flex items-center gap-1">
                            ★ IMDb: {{ $media->imdb_rating }}
                        </span>
                    @endif
                </div>

                <h1 class="text-3xl font-extrabold text-white">{{ $currentTranslation->title ?? '' }}</h1>

                @if($media->location && $media->location->translation)
                    <div class="bg-slate-900/60 border border-slate-700 p-3 rounded-lg flex items-center justify-between text-sm">
                        <span class="text-slate-300 flex items-center gap-2">
                            📍 موقع الجريمة/الحدث الحقيقي: <strong class="text-white">{{ $media->location->translation->name }}</strong>
                        </span>
                        <a href="{{ route('frontend.map', ['location' => $media->location->id]) }}" class="text-indigo-400 hover:underline">عرض على الخريطة ←</a>
                    </div>
                @endif
            </div>
        </div>

        <!-- الملخص -->
        <div class="border-t border-slate-700 pt-6">
            <h2 class="text-xl font-bold text-white mb-3">ملخص القصة الحقيقية</h2>
            <div class="text-slate-300 leading-relaxed whitespace-pre-line bg-slate-900/40 p-5 rounded-xl border border-slate-700/50">
                {{ $currentTranslation->synopsis ?? 'لا يوجد ملخص متاح.' }}
            </div>
        </div>

        <!-- التحليل الجنائي -->
        @if($currentTranslation->review_analysis)
            <div class="border-t border-slate-700 pt-6">
                <h2 class="text-xl font-bold text-white mb-3">تحليل المنصة والجانب الجنائي</h2>
                <div class="text-slate-300 leading-relaxed whitespace-pre-line bg-indigo-950/20 p-5 rounded-xl border border-indigo-500/20">
                    {{ $currentTranslation->review_analysis }}
                </div>
            </div>
        @endif

    </div>
</div>
@endsection
--}}