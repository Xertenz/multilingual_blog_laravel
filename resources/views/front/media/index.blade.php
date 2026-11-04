{{--
@extends('layouts.main')

@section('title', 'أرشيف الجريمة والغموض المرئي')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="text-center max-w-2xl mx-auto mb-12">
        <h1 class="text-3xl md:text-4xl font-extrabold text-white tracking-tight mb-3">أرشيف الجريمة والغموض المرئي</h1>
        <p class="text-slate-400">تحليلات وملخصات لأبرز الأفلام، المسلسلات، والوثائقيات الجنائية المستندة إلى قصص حقيقية.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @forelse($mediaList as $item)
            @php
                $trans = $item->translations->first();
            @endphp
            <div class="bg-slate-800/80 border border-slate-700/60 rounded-xl overflow-hidden shadow-lg hover:border-indigo-500/50 transition-all duration-300 flex flex-col">
                <div class="relative h-72 bg-slate-900 overflow-hidden">
                    @if($item->hasMedia('poster_imgs'))
                        <img src="{{ $item->getFirstMediaUrl('poster_imgs') }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="{{ $trans->title ?? '' }}">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-slate-600">بدون بوستر</div>
                    @endif
                    <div class="absolute top-3 right-3 bg-slate-900/80 backdrop-blur border border-slate-700 text-indigo-400 text-xs font-bold px-2.5 py-1 rounded-full uppercase">
                        {{ $item->type }}
                    </div>
                    @if($item->imdb_rating)
                        <div class="absolute bottom-3 left-3 bg-slate-900/80 backdrop-blur border border-slate-700 text-amber-400 text-xs font-bold px-2 py-1 rounded-lg flex items-center gap-1">
                            ★ {{ $item->imdb_rating }}
                        </div>
                    @endif
                </div>

                <div class="p-5 flex flex-col flex-grow">
                    <div class="text-xs text-slate-400 mb-1">{{ $item->release_year ?? '' }}</div>
                    <h3 class="text-lg font-bold text-white mb-2 line-clamp-1">{{ $trans->title ?? 'عنوان غير متوفر' }}</h3>
                    <p class="text-slate-400 text-sm line-clamp-2 mb-4 flex-grow">{{ $trans->synopsis ?? 'لا يوجد ملخص متاح لهذه اللغة...' }}</p>
                    
                    <a href="{{ route('frontend.media.show', ['id' => $item->id, 'slug' => $trans->slug ?? 'media']) }}" class="mt-auto block text-center bg-indigo-600/20 hover:bg-indigo-600 text-indigo-300 hover:text-white border border-indigo-500/30 font-medium py-2 px-4 rounded-lg transition-colors text-sm">
                        قراءة التحليل والقصة
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center text-slate-500">
                لا توجد أعمال مضافة في الأرشيف حالياً.
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $mediaList->links() }}
    </div>
</div>
@endsection
--}}