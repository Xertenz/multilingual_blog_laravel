<div class="container mx-auto px-4 py-8">
    <!-- رأس لوحة المحققين — موحّد مع بقية الصفحات -->
    <div class="bg-[#0f1526] border border-slate-800 rounded-xl px-5 py-4 mb-6 shadow-md flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="space-y-1">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 bg-red-600 rounded-sm shrink-0"></span>
                <h1 class="text-lg md:text-xl font-bold text-white tracking-wide">
                    {{ __('Detective Board') }}
                </h1>
            </div>
            <p class="text-gray-400 text-xs max-w-2xl leading-relaxed">
                {{ __('A documented community space to review and discuss mysterious cases and crimes.') }}
            </p>
        </div>

        <div class="shrink-0 self-start sm:self-center flex items-center gap-2">
            @auth
                @if(auth()->user()->isBanned())
                    @php $penalty = auth()->user()->activePenalty; @endphp
                    <div class="bg-rose-950/80 border border-rose-800 text-rose-300 px-3 py-1.5 rounded-lg text-[11px] flex items-center gap-2">
                        <span>⚠️ {{ __('Account restricted: :reason', ['reason' => $penalty->reason]) }}</span>
                    </div>
                @else
                    <button @click="$dispatch('open-case-modal')"
                            class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg text-xs font-semibold transition shadow-lg shadow-red-600/20 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path>
                        </svg>
                        <span>{{ __('Add New Case') }}</span>
                    </button>
                @endif
            @else
                <a href="{{ route('login') }}"
                   class="text-xs text-gray-300 hover:text-white bg-slate-800 px-3 py-2 rounded-lg border border-slate-700 transition">
                    {{ __('Log in to participate') }}
                </a>
            @endauth
        </div>
    </div>

    <!-- رسائل التنبيه -->
    @if (session()->has('message'))
        <div class="mb-6 p-4 bg-emerald-950/80 border border-emerald-800 text-emerald-400 rounded-xl text-sm">
            {{ session('message') }}
        </div>
    @endif

    <!-- فلترة الفئات -->
    <div class="flex items-center gap-2 overflow-x-auto pb-4 mb-6">
        <button wire:click="$set('selectedCategory', '')"
                class="px-4 py-1.5 rounded-lg text-xs font-semibold transition whitespace-nowrap {{ $selectedCategory === '' ? 'bg-red-600 text-white' : 'bg-slate-800 text-gray-400 hover:text-white' }}">
            {{ __('All') }}
        </button>
        @foreach($categories as $cat)
            @php
                $trans = $cat->translations->where('locale', App::getLocale())->first();
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
            <div class="bg-[#0f1526] border border-slate-800 hover:border-red-600/60 rounded-xl p-5 flex flex-col justify-between transition-all duration-300 shadow-lg">
                <div>
                    <!-- الفئة والتاريخ -->
                    <div class="flex justify-between items-center mb-3">
                        <span class="text-[10px] font-semibold px-2.5 py-1 rounded-md bg-slate-900 text-red-400 border border-slate-800 font-mono">
                            {{ $caseCatTrans->name ?? __('General') }}
                        </span>
                        <span class="text-[10px] text-gray-500 font-mono">{{ $case->created_at->diffForHumans() }}</span>
                    </div>

                    <!-- العنوان -->
                    <h3 class="text-base font-bold text-gray-200 mb-2 line-clamp-2 group-hover:text-red-500 transition-colors">
                        <a href="{{ route('community.cases.show', $case->slug) }}" class="hover:text-red-500 transition-colors">{{ $case->title }}</a>
                    </h3>

                    <!-- نبذة -->
                    <p class="text-gray-400 text-xs line-clamp-3 mb-4 leading-relaxed">
                        {{ Str::limit(strip_tags($case->body), 120) }}
                    </p>
                </div>

                <!-- الشريط السفلي: الكاتب + العدادات -->
                <div class="pt-3 border-t border-slate-800 flex items-center justify-between text-[11px] text-slate-400">
                    <span class="font-mono truncate max-w-[120px]">
                        ✍️ {{ $case->author->name ?? __('Anonymous Detective') }}
                    </span>
                    <div class="flex items-center gap-3 shrink-0">
                        @auth
                            {{-- ✅ زر تعديل القضية (لصاحبها أو المشرف/المدير) --}}
                            @if($case->user_id === auth()->id() || auth()->user()->hasAnyRole(['super-admin', 'editor']))
                                <button wire:click="openEditModal({{ $case->id }})"
                                        class="text-gray-400 hover:text-blue-400 transition"
                                        title="{{ __('Edit case') }}">
                                    ✏️
                                </button>
                            @endif

                            @if($case->user_id !== auth()->id())
                                <button onclick="openReportModal('{{ App\Models\CommunityCase::class }}', {{ $case->id }})"
                                        class="text-gray-400 hover:text-rose-400 transition" title="{{ __('Report case') }}">
                                    🚨
                                </button>
                            @endif
                        @endauth
                        <span class="flex items-center gap-1">💬 {{ $case->comments_count }}</span>
                        <span class="flex items-center gap-1">👁️ {{ $case->views_count }}</span>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center bg-[#0f1526] border border-slate-800 rounded-xl">
                <p class="text-sm text-gray-400">{{ __('No cases published yet in this category. Be the first to submit one!') }}</p>
            </div>
        @endforelse
    </div>

    <!-- التصفح -->
    <div class="mt-8">
        {{ $cases->links() }}
    </div>

    <!-- Modal إنشاء قضية جديدة -->
    <div id="new-case-modal"
         x-data="{ open: false }"
         @open-case-modal.window="open = true"
         @close-case-modal.window="open = false"
         x-show="open"
         style="display: none;"
         class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">

        <div class="bg-[#0f1526] border border-slate-800 w-full max-w-2xl rounded-2xl p-6 shadow-2xl relative" @click.away="open = false">

            <div class="flex justify-between items-center mb-6 pb-4 border-b border-slate-800">
                <h3 class="text-base font-bold text-white">{{ __('Submit a New Case to the Detective Board') }}</h3>
                <button @click="open = false" class="text-gray-400 hover:text-white">&times;</button>
            </div>

            @auth
                @if(auth()->user()->isBanned())
                    @php $penalty = auth()->user()->activePenalty; @endphp
                    <div class="p-4 bg-rose-950/80 border border-rose-800 text-rose-300 rounded-xl text-sm space-y-2">
                        <p class="font-bold">{{ __('Sorry, you cannot add new cases.') }}</p>
                        <p>{{ __('Your account has an active penalty. Reason: :reason', ['reason' => $penalty->reason]) }}</p>
                    </div>
                @else
                    <form wire:submit.prevent="storeCase" class="space-y-4">

                        {{-- 1. الفئة --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-400 mb-1">{{ __('Case Category') }}</label>
                            <select wire:model.defer="community_case_category_id" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2 text-white text-sm focus:outline-none focus:border-red-600">
                                <option value="">{{ __('Choose a category...') }}</option>
                                @foreach($categories as $cat)
                                    @php $t = $cat->translations->first(); @endphp
                                    @if($t)
                                        <option value="{{ $cat->id }}">{{ $t->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                            @error('community_case_category_id') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        {{-- 2. حالة القضية --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-400 mb-1">{{ __('Case Status') }}</label>
                            <select wire:model.defer="case_status" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2 text-white text-sm focus:outline-none focus:border-red-600">
                                @foreach(\App\Enums\CommunityCaseStatus::cases() as $status)
                                    <option value="{{ $status->value }}">{{ $status->label() }}</option>
                                @endforeach
                            </select>
                            @error('case_status') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        {{-- 3. العنوان --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-400 mb-1">{{ __('Case Title') }}</label>
                            <input type="text" wire:model.defer="title" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2 text-white text-sm focus:outline-none focus:border-red-600">
                            @error('title') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        {{-- 4. الوصف --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-400 mb-1">{{ __('Case Details & Evidence') }}</label>
                            <textarea wire:model.defer="body" rows="5" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2 text-white text-sm focus:outline-none focus:border-red-600" placeholder="{{ __('Write the case details and circumstances here...') }}"></textarea>
                            @error('body') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex justify-end gap-3 pt-4 border-t border-slate-800">
                            <button type="button" @click="open = false" class="px-4 py-2 bg-slate-800 text-gray-300 rounded-xl text-sm font-semibold hover:bg-slate-700 transition">{{ __('Cancel') }}</button>
                            <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-xl text-sm font-semibold hover:bg-red-700 transition shadow-lg shadow-red-600/20">{{ __('Submit for Review') }}</button>
                        </div>
                    </form>
                @endif
            @endauth

        </div>
    </div>

    <!-- Modal تعديل القضية -->
    <div id="edit-case-modal"
        x-data="{ open: false }"
        @open-edit-case-modal.window="open = true"
        @close-edit-case-modal.window="open = false"
        x-show="open"
        style="display: none;"
        class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">

        <div class="bg-[#0f1526] border border-slate-800 w-full max-w-2xl rounded-2xl p-6 shadow-2xl relative" @click.away="open = false">

            <div class="flex justify-between items-center mb-6 pb-4 border-b border-slate-800">
                <h3 class="text-base font-bold text-white">{{ __('Edit Case') }}</h3>
                <button @click="open = false" class="text-gray-400 hover:text-white">&times;</button>
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
                    <textarea wire:model.defer="edit_body" rows="5"
                            class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2 text-white text-sm focus:outline-none focus:border-red-600"></textarea>
                    @error('edit_body') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                @if(!auth()->user()->hasAnyRole(['super-admin', 'editor']))
                    <div class="bg-amber-950/40 border border-amber-800/50 rounded-xl p-3 text-xs text-amber-300">
                        ⚠️ {{ __('Your edit will be submitted for review. The case will return to "pending" status.') }}
                    </div>
                @endif

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-800">
                    <button type="button" @click="open = false"
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
</div>