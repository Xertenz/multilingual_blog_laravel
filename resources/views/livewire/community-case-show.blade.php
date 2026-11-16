<div class="container mx-auto px-4 py-10 max-w-4xl text-white">

    {{-- رسالة النجاح --}}
    @if (session()->has('message'))
        <div class="mb-6 p-4 bg-emerald-950/80 border border-emerald-800 text-emerald-400 rounded-xl text-sm">
            {{ session('message') }}
        </div>
    @endif

    {{-- رسالة الخطأ --}}
    @if (session()->has('error'))
        <div class="mb-6 p-4 bg-rose-950/80 border border-rose-800 text-rose-300 rounded-xl text-sm">
            {{ session('error') }}
        </div>
    @endif

    <!-- زر العودة -->
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('community.cases.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-gray-400 hover:text-white transition">
            <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>Back to Detective Board</span>
        </a>

        {{-- ✅ زر تعديل القضية --}}
        @auth
            @if($communityCase->user_id === auth()->id() || auth()->user()->hasAnyRole(['super-admin', 'editor']))
                <button wire:click="openEditModal"
                        class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-xs font-semibold transition shadow-lg shadow-blue-600/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    <span>{{ __('Edit Case') }}</span>
                </button>
            @endif
        @endauth
    </div>

    <!-- بطاقة القضية الرئيسية -->
    <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-6 md:p-8 shadow-xl space-y-6 mb-8">

        <!-- معلومات رأس القضية -->
        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-800 pb-4">
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 text-xs font-bold bg-red-600/20 text-red-500 border border-red-600/30 rounded-lg uppercase tracking-wider">
                    {{ $categoryTrans->name ?? 'General' }}
                </span>

                @if($communityCase->case_status === App\Enums\CommunityCaseStatus::OPEN)
                    <span class="px-2.5 py-1 text-[11px] font-semibold bg-emerald-950/60 text-emerald-400 border border-emerald-800/40 rounded-lg">Active Investigation</span>
                @elseif($communityCase->case_status === App\Enums\CommunityCaseStatus::SOLVED)
                    <span class="px-2.5 py-1 text-[11px] font-semibold bg-blue-950/60 text-blue-400 border border-blue-800/40 rounded-lg">Solved Case</span>
                @else
                    <span class="px-2.5 py-1 text-[11px] font-semibold bg-slate-800 text-gray-400 border border-slate-700 rounded-lg">Closed</span>
                @endif
            </div>

            <div class="flex items-center gap-3 text-xs text-gray-400">
                <span>By <strong class="text-gray-200">{{ $communityCase->author->name }}</strong></span>
                <span>•</span>
                <span>{{ $communityCase->created_at->format('F j, Y') }}</span>
                <span>•</span>
                <span class="flex items-center gap-1">
                    👁️ {{ $communityCase->views_count }} Views
                </span>
                @auth
                    @if($communityCase->user_id !== auth()->id())
                        <span>•</span>
                        <button onclick="openReportModal('{{ App\Models\CommunityCase::class }}', {{ $communityCase->id }})" class="text-rose-400 hover:text-rose-300 transition flex items-center gap-1">
                            🚨 إبلاغ
                        </button>
                    @endif
                @endauth
            </div>
        </div>

        <h1 class="text-2xl md:text-3xl font-black text-white leading-tight">
            {{ $communityCase->title }}
        </h1>

        <div class="prose prose-invert max-w-none text-gray-300 leading-relaxed text-sm md:text-base space-y-4 pt-2">
            {!! $communityCase->body !!}
        </div>
    </div>

    <!-- قسم التعليقات والنقاشات المجتمعية الموحد -->
    <livewire:comments-section :model="$communityCase" />

    {{-- ✅ Modal تعديل القضية --}}
    @if($showEditModal)
        <div class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4"
             wire:click.self="closeEditModal">

            <div class="bg-[#0f1526] border border-slate-800 w-full max-w-2xl rounded-2xl p-6 shadow-2xl relative">

                <div class="flex justify-between items-center mb-6 pb-4 border-b border-slate-800">
                    <h3 class="text-base font-bold text-white">{{ __('Edit Case') }}</h3>
                    <button wire:click="closeEditModal" class="text-gray-400 hover:text-white text-xl leading-none">&times;</button>
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
                        <textarea wire:model.defer="edit_body" rows="6"
                                class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2 text-white text-sm focus:outline-none focus:border-red-600"></textarea>
                        @error('edit_body') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    @if(!auth()->user()->hasAnyRole(['super-admin', 'editor']))
                        <div class="bg-amber-950/40 border border-amber-800/50 rounded-xl p-3 text-xs text-amber-300">
                            ⚠️ {{ __('Your edit will be submitted for review. The case will return to "pending" status.') }}
                        </div>
                    @endif

                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-800">
                        <button type="button" wire:click="closeEditModal"
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
    @endif

</div>