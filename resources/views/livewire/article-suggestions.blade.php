<div class="max-w-5xl mx-auto px-4 py-8">
    
    <!-- الهيدر والتوجيه -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8 border-b border-slate-800 pb-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-black text-gray-100 flex items-center gap-2">
                <span class="text-red-600">#</span> {{ __('Case File Suggestions') ?? 'اقتراحات القضايا' }}
            </h1>
            <p class="text-xs md:text-sm text-gray-400 mt-1">
                {{ __('Suggest cold cases or mysteries you want us to investigate and write about.') ?? 'اقترح قضايا أو جرائم غامضة ترغب في أن يحقق فيها فريقنا ويكتب عنها.' }}
            </p>
        </div>

        @auth
            <button @click="$wire.set('showModal', true)" class="bg-red-600 hover:bg-red-700 text-white font-bold text-xs uppercase px-4 py-2.5 rounded-lg shadow-lg shadow-red-900/30 transition flex items-center justify-center gap-2 self-start md:self-auto">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>{{ __('Suggest a Case') ?? 'اقترح قضية جديدة' }}</span>
            </button>
        @else
            <a href="{{ route('login') }}" class="bg-slate-800 hover:bg-slate-700 text-gray-300 font-bold text-xs px-4 py-2.5 rounded-lg border border-slate-700 transition text-center">
                {{ __('Log in to suggest cases') ?? 'سجل الدخول لإضافة اقتراح' }}
            </a>
        @endauth
    </div>

    <!-- رسالة النجاح -->
    @if (session()->has('message'))
        <div class="mb-6 p-4 bg-emerald-950/80 border border-emerald-800 text-emerald-300 text-xs rounded-xl flex items-center justify-between">
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <!-- شريط التصفية والفرز -->
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center gap-2">
            <button wire:click="$set('filter', 'popular')" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ $filter === 'popular' ? 'bg-red-950/80 text-red-400 border border-red-800/60' : 'bg-slate-900 text-gray-400 hover:text-white border border-slate-800' }}">
                {{ __('Most Wanted') ?? 'الأكثر طلباً' }}
            </button>
            <button wire:click="$set('filter', 'latest')" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ $filter === 'latest' ? 'bg-red-950/80 text-red-400 border border-red-800/60' : 'bg-slate-900 text-gray-400 hover:text-white border border-slate-800' }}">
                {{ __('Newest') ?? 'الأحدث' }}
            </button>
        </div>

        <!-- فلتر مقترحاتي فقط (يظهر للمسجلين فقط) -->
        @auth
        <label class="flex items-center gap-2 cursor-pointer text-xs font-medium text-gray-300 hover:text-white select-none">
            <input type="checkbox" wire:model.live="mySuggestionsOnly" class="w-4 h-4 rounded bg-slate-900 border-slate-800 text-red-600 focus:ring-red-600/50 focus:ring-offset-0 cursor-pointer">
            <span>{{ __('My Suggestions Only') ?? 'مقترحاتي فقط' }}</span>
        </label>
        @endauth

        <!-- قائمة اختيار اللغة -->
        <div class="flex items-center gap-2">
            <label class="text-xs text-gray-400 font-medium">{{ __('Language') ?? 'اللغة' }}:</label>
            <select wire:model.live="selectedLocale" class="bg-slate-900 border border-slate-800 text-gray-300 text-xs rounded-lg px-3 py-1.5 focus:outline-none focus:border-red-600/60 capitalize cursor-pointer">
                <option value="all">{{ __('All Languages') ?? 'جميع اللغات' }}</option>
                @foreach($locales as $code => $properties)
                    <option value="{{ $code }}">{{ $properties['native'] }} ({{ strtoupper($code) }})</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- قائمة الاقتراحات -->
    <div class="space-y-4">
        @forelse($suggestions as $suggestion)
            @php $isVoted = $suggestion->isVotedBy(auth()->user()); @endphp
            <div class="bg-[#0f1526] border border-slate-800 hover:border-slate-700 rounded-xl p-4 transition flex items-start gap-4 shadow-lg">
                
                <!-- زر التصويت -->
                <button wire:click="toggleVote({{ $suggestion->id }})" 
                        class="flex flex-col items-center justify-center p-2.5 rounded-xl border transition min-w-[54px] {{ $isVoted ? 'bg-red-600/20 border-red-600/60 text-red-500' : 'bg-slate-900 border-slate-800 text-gray-400 hover:border-slate-700 hover:text-white' }}">
                    <svg class="w-5 h-5 transition-transform group-hover:-translate-y-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"></path>
                    </svg>
                    <span class="text-xs font-black mt-1">{{ $suggestion->votes_count }}</span>
                </button>

                <!-- تفاصيل الاقتراح -->
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap mb-1">
                        <h2 class="text-sm md:text-base font-bold text-gray-200 truncate">{{ $suggestion->title }}</h2>
                        
                        <!-- بادج الحالة -->
                        @if($suggestion->status === 'pending')
                            <span class="px-2 py-0.5 text-[10px] font-semibold bg-amber-950/60 text-amber-400 border border-amber-800/40 rounded-md">{{ __('Pending') ?? 'قيد المراجعة' }}</span>
                        @elseif($suggestion->status === 'approved')
                            <span class="px-2 py-0.5 text-[10px] font-semibold bg-blue-950/60 text-blue-400 border border-blue-800/40 rounded-md">{{ __('In Research') ?? 'قيد البحث' }}</span>
                        @elseif($suggestion->status === 'written')
                            <span class="px-2 py-0.5 text-[10px] font-semibold bg-emerald-950/60 text-emerald-400 border border-emerald-800/40 rounded-md">{{ __('Investigated') ?? 'تمت كتابتها' }}</span>
                        @endif
                    </div>

                    @if($suggestion->description)
                        <p class="text-xs text-gray-400 line-clamp-2 mb-2">{{ $suggestion->description }}</p>
                    @endif

                    <div class="flex items-center gap-3 text-[11px] text-gray-500">
                        <span>{{ __('Suggested by') ?? 'مقترح بواسطة' }}: <strong class="text-gray-400"><a href="{{route('profile.show', ['id' => $suggestion->user->id])}}">{{ $suggestion->user->name }}</a></strong></span>
                        <span>•</span>
                        <span>{{ $suggestion->created_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>
        @empty
            <div class="p-8 bg-[#0f1526] border border-slate-800 rounded-xl text-center text-gray-500 text-xs">
                {{ __('No case suggestions found yet. Be the first to suggest one!') ?? 'لا توجد اقتراحات قضايا بعد. كن أول من يقترح!' }}
            </div>
        @endforelse
    </div>

    <!-- الترقيم التلقائي -->
    <div class="mt-6">
        {{ $suggestions->links() }}
    </div>

    <!-- النافذة المنبثقة لإضافة اقتراح جديد Modal -->
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm">
            <div class="bg-[#0f1526] border border-slate-800 rounded-2xl w-full max-w-lg p-6 shadow-2xl relative" @click.outside="$wire.set('showModal', false)">
                <div class="flex justify-between items-center pb-4 border-b border-slate-800 mb-4">
                    <h3 class="text-base font-bold text-gray-200">{{ __('Submit Case Suggestion') ?? 'تقديم اقتراح قضية' }}</h3>
                    <button wire:click="$set('showModal', false)" class="text-gray-500 hover:text-white">&times;</button>
                </div>

                <form wire:submit.prevent="createSuggestion" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 mb-1">{{ __('Case Title / Topic') ?? 'عنوان القضية / الموضوع' }} *</label>
                        <input type="text" wire:model="title" class="w-full bg-slate-900 border border-slate-800 text-gray-200 text-xs rounded-xl p-3 focus:outline-none focus:border-red-600" placeholder="{{ __('e.g., Miyazawa Family Murder') }}" />
                        @error('title') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-400 mb-1">{{ __('Additional Details / References') ?? 'تفاصيل إضافية / مراجع' }}</label>
                        <textarea wire:model="description" rows="4" class="w-full bg-slate-900 border border-slate-800 text-gray-200 text-xs rounded-xl p-3 focus:outline-none focus:border-red-600" placeholder="{{ __('Add any links, year, or summary about this case...') }}"></textarea>
                        @error('description') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" wire:click="$set('showModal', false)" class="px-4 py-2 text-xs font-semibold text-gray-400 hover:text-white bg-slate-900 rounded-xl border border-slate-800">
                            {{ __('Cancel') ?? 'إلغاء' }}
                        </button>
                        <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-red-600 hover:bg-red-700 rounded-xl shadow-lg shadow-red-900/30">
                            {{ __('Submit Suggestion') ?? 'إرسال الاقتراح' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>