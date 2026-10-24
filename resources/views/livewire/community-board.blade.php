<div class="max-w-7xl mx-auto px-4 py-8">
    
    <!-- رأس لوحة المحققين -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 pb-6 border-b border-slate-800 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-white tracking-wider flex items-center gap-2">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"></path>
                </svg>
                لوحة المحققين (Detective Board)
            </h1>
            <p class="text-gray-400 text-sm mt-1">ساحة مجتمعية موثقة لاستعراض ومناقشة القضايا الغامضة والجرائم.</p>
        </div>

        @auth
            <!-- زر فتح نافذة/مودال إضافة قضية -->
            <button onclick="document.getElementById('new-case-modal').classList.remove('hidden')" 
                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-xl text-sm font-semibold transition shadow-lg shadow-red-600/20 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path>
                </svg>
                إضافة قضية جديدة
            </button>
        @else
            <a href="{{ route('login') }}" class="text-sm text-gray-400 hover:text-white bg-slate-800 px-4 py-2 rounded-xl border border-slate-700">
                تسجيل الدخول للمشاركة
            </a>
        @endauth
    </div>

    <!-- رسائل التنبيه -->
    @if (session()->has('message'))
        <div class="mb-6 p-4 bg-emerald-950/80 border border-emerald-800 text-emerald-400 rounded-xl text-sm">
            {{ session('message') }}
        </div>
    @endif

    <!-- فلترة الفئات (ديناميكية حسب الترجمات) -->
    <div class="flex items-center gap-2 overflow-x-auto pb-4 mb-8">
        <button wire:click="$set('selectedCategory', '')" 
                class="px-4 py-1.5 rounded-lg text-xs font-semibold transition {{ $selectedCategory === '' ? 'bg-red-600 text-white' : 'bg-slate-800 text-gray-400 hover:text-white' }}">
            الكل
        </button>
        @foreach($categories as $cat)
            @php
                $trans = $cat->translations->first();
            @endphp
            @if($trans)
                <button wire:click="$set('selectedCategory', '{{ $cat->id }}')" 
                        class="px-4 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition {{ $selectedCategory == $cat->id ? 'bg-red-600 text-white' : 'bg-slate-800 text-gray-400 hover:text-white' }}">
                    {{ $trans->name }}
                </button>
            @endif
        @endforeach
    </div>

    <!-- شبكة عرض القضايا -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($cases as $case)
            @php
                $caseCatTrans = $case->category?->translations->where('locale', app()->getLocale())->first() 
                             ?? $case->category?->translations->first();
            @endphp
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 flex flex-col justify-between hover:border-slate-700 transition">
                <div>
                    <!-- الفئة والحالة -->
                    <div class="flex justify-between items-center mb-3">
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-md bg-slate-800 text-red-400 border border-slate-700/50">
                            {{ $caseCatTrans->name ?? 'عام' }}
                        </span>
                        <span class="text-xs text-gray-500 font-mono">{{ $case->created_at->diffForHumans() }}</span>
                    </div>

                    <!-- العنوان مع رابط التوجيه لصفحة العرض المفردة التي أنشأناها -->
                    <h3 class="text-lg font-bold text-white mb-2 line-clamp-2 hover:text-red-400 transition">
                        <a href="{{ route('community.cases.show', $case->slug) }}">{{ $case->title }}</a>
                    </h3>

                    <!-- نبذة من المحتوى -->
                    <p class="text-gray-400 text-sm line-clamp-3 mb-4">
                        {{ Str::limit(strip_tags($case->body), 120) }}
                    </p>
                </div>

                <!-- معلومات الكاتب والعدادات -->
                <div class="pt-4 border-t border-slate-800 flex items-center justify-between text-xs text-gray-500">
                    <span class="text-gray-300 font-medium">بواسطة: {{ $case->user->name ?? 'محقق مجهول' }}</span>
                    <div class="flex items-center gap-3">
                        <span class="flex items-center gap-1">
                            💬 {{ $case->comments_count }}
                        </span>
                        <span class="flex items-center gap-1">
                            👁️ {{ $case->views_count }}
                        </span>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center text-gray-500">
                لا توجد قضايا منشورة حتى الآن في هذه الفئة. كن أول من يطرح قضية!
            </div>
        @endforelse
    </div>

    <!-- التصفح (Pagination) -->
    <div class="mt-8">
        {{ $cases->links() }}
    </div>

    <!-- Modal إنشاء قضية جديدة -->
    <div id="new-case-modal" class="hidden fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-slate-900 border border-slate-800 w-full max-w-2xl rounded-2xl p-6 shadow-2xl relative">
            
            <div class="flex justify-between items-center mb-6 pb-4 border-b border-slate-800">
                <h3 class="text-xl font-bold text-white">طرح قضية جديدة في لوحة المحققين</h3>
                <button onclick="document.getElementById('new-case-modal').classList.add('hidden')" class="text-gray-400 hover:text-white">✕</button>
            </div>

            <form wire:submit.prevent="storeCase" class="space-y-4">
                
                <div>
                    <label class="block text-xs font-semibold text-gray-400 mb-1">عنوان القضية</label>
                    <input type="text" wire:model.defer="title" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-2 text-white text-sm focus:outline-none focus:border-red-600">
                    @error('title') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-400 mb-1">فئة القضية</label>
                    <!-- تم التعديل ليطابق المتغير الجديد community_case_category_id وجلب الفئات المترجمة -->
                    <select wire:model.defer="community_case_category_id" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-2 text-white text-sm focus:outline-none focus:border-red-600">
                        <option value="">اختر الفئة...</option>
                        @foreach($categories as $cat)
                            @php $t = $cat->translations->first(); @endphp
                            @if($t)
                                <option value="{{ $cat->id }}">{{ $t->name }}</option>
                            @endif
                        @endforeach
                    </select>
                    @error('community_case_category_id') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-400 mb-1">تفاصيل القضية والأدلة</label>
                    <textarea wire:model.defer="body" rows="5" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-2 text-white text-sm focus:outline-none focus:border-red-600" placeholder="اكتب تفاصيل وملابسات القضية هنا..."></textarea>
                    @error('body') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-800">
                    <button type="button" onclick="document.getElementById('new-case-modal').classList.add('hidden')" class="px-4 py-2 bg-slate-800 text-gray-300 rounded-xl text-sm font-semibold hover:bg-slate-700 transition">إلغاء</button>
                    <button type="submit" onclick="document.getElementById('new-case-modal').classList.add('hidden')" class="px-6 py-2 bg-red-600 text-white rounded-xl text-sm font-semibold hover:bg-red-700 transition shadow-lg shadow-red-600/20">إرسال للمراجعة</button>
                </div>
            </form>

        </div>
    </div>

</div>