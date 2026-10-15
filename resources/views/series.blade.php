@extends('layouts.main') {{-- استبدلها باسم الـ Layout العام للموقع لديك --}}

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    {{-- رأس السلسلة --}}
    <div class="bg-gradient-to-r from-indigo-900 to-slate-900 text-white rounded-2xl p-8 mb-8 shadow-lg">
        <span class="bg-indigo-600 text-indigo-100 text-xs px-3 py-1 rounded-full uppercase tracking-wider font-semibold">سلسلة مقالات</span>
        <h1 class="text-3xl font-bold mt-4 mb-3">{{ $translation->title ?? 'بدون عنوان' }}</h1>
        <p class="text-slate-300 text-sm leading-relaxed mb-6">{{ $translation->description }}</p>
        
        <div class="flex items-center text-xs text-slate-400 space-x-4">
            <span>بواسطة: <strong class="text-white">{{ $series->user->name ?? 'مشرف الموقع' }}</strong></span>
            <span>عدد المقالات: <strong class="text-white">{{ $series->articles->count() }}</strong> مقالات</span>
        </div>
    </div>

    {{-- قائمة مقالات السلسلة --}}
    <h2 class="text-xl font-bold text-slate-800 mb-4">محتويات السلسلة (ترتيب القراءة)</h2>
    
    <div class="space-y-3">
        @foreach($series->articles as $index => $article)
            @php
                $articleTrans = $article->translations->first();
            @endphp
            <div class="bg-white border border-slate-200 rounded-xl p-4 flex items-center justify-between shadow-sm hover:shadow-md transition">
                <div class="flex items-center space-x-4">
                    {{-- رقم التسلسل --}}
                    <div class="w-8 h-8 rounded-full bg-indigo-50 text-indigo-600 font-bold flex items-center justify-center text-sm ml-4">
                        {{ $index + 1 }}
                    </div>
                    <div>
                        <h3 class="font-semibold text-slate-800">
                            <a href="{{ route('articles.show', ['id' => $article->id, 'slug' => $articleTrans->slug ?? '']) }}" class="hover:text-indigo-600 transition">
                                {{ $articleTrans->title ?? 'مقال غير مترجم' }}
                            </a>
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">تاريخ النشر: {{ $article->created_at->format('Y-m-d') }}</p>
                    </div>
                </div>

                <div>
                    <a href="{{ route('articles.show', ['id' => $article->id, 'slug' => $articleTrans->slug ?? '']) }}" class="bg-slate-100 hover:bg-indigo-600 hover:text-white text-slate-700 px-4 py-2 rounded-lg text-xs font-medium transition">
                        قراءة المقال &larr;
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection