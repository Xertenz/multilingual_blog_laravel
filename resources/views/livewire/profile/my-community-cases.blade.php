<div class="container mx-auto px-4 py-8 max-w-6xl text-white">

    {{-- شريط تبويبات الحساب الشخصي --}}
    @include('profile.partials.profile-menu')

    <!-- رأس الصفحة -->
    <div class="bg-[#0f1526] border border-slate-800 rounded-xl px-5 py-4 mb-6 shadow-md flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="space-y-1">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 bg-red-600 rounded-sm shrink-0"></span>
                <h1 class="text-lg md:text-xl font-bold text-white tracking-wide">
                    {{ __('My Community Cases') }}
                </h1>
            </div>
            <p class="text-gray-400 text-xs max-w-2xl leading-relaxed">
                {{ __('Manage the cases you have submitted to the Detective Board, track their review status, edit details, or remove them.') }}
            </p>
        </div>

        <a href="{{ route('community.cases.index') }}"
           class="shrink-0 self-start sm:self-center bg-slate-800 hover:bg-slate-700 text-gray-200 px-3.5 py-2 rounded-lg text-xs font-semibold border border-slate-700 transition">
            {{ __('Go to Detective Board') }}
        </a>
    </div>

    <!-- رسائل التنبيه -->
    @if (session()->has('message'))
        <div class="mb-6 p-4 bg-emerald-950/80 border border-emerald-800 text-emerald-400 rounded-xl text-sm">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-6 p-4 bg-rose-950/80 border border-rose-800 text-rose-300 rounded-xl text-sm">
            {{ session('error') }}
        </div>
    @endif

    <!-- فلاتر حالة المراجعة -->
    <div class="flex items-center gap-2 overflow-x-auto pb-4 mb-6">
        <button wire:click="setStatusFilter('all')"
                class="px-4 py-1.5 rounded-lg text-xs font-semibold transition whitespace-nowrap {{ $statusFilter === 'all' ? 'bg-red-600 text-white' : 'bg-slate-800 text-gray-400 hover:text-white' }}">
            {{ __('All') }} ({{ $counts['all'] }})
        </button>

        <button wire:click="setStatusFilter('approved')"
                class="px-4 py-1.5 rounded-lg text-xs font-semibold transition whitespace-nowrap {{ $statusFilter === 'approved' ? 'bg-emerald-700 text-white' : 'bg-slate-800 text-gray-400 hover:text-white' }}">
            {{ __('Published') }} ({{ $counts['approved'] }})
        </button>

        <button wire:click="setStatusFilter('pending')"
                class="px-4 py-1.5 rounded-lg text-xs font-semibold transition whitespace-nowrap {{ $statusFilter === 'pending' ? 'bg-amber-700 text-white' : 'bg-slate-800 text-gray-400 hover:text-white' }}">
            {{ __('Pending Review') }} ({{ $counts['pending'] }})
        </button>

        <button wire:click="setStatusFilter('rejected')"
                class="px-4 py-1.5 rounded-lg text-xs font-semibold transition whitespace-nowrap {{ $statusFilter === 'rejected' ? 'bg-rose-800 text-white' : 'bg-slate-800 text-gray-400 hover:text-white' }}">
            {{ __('Rejected') }} ({{ $counts['rejected'] }})
        </button>
    </div>

    <!-- شبكة عرض قضايا العضو -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($cases as $case)
            @php
                $caseCatTrans = $case->category?->translations->where('locale', app()->getLocale())->first()
                             ?? $case->category?->translations->first();
            @endphp

            <div wire:key="my-case-{{ $case->id }}" class="bg-[#0f1526] border border-slate-800 hover:border-slate-700 rounded-xl p-5 flex flex-col justify-between transition-all duration-300 shadow-lg">
                <div>
                    <!-- الفئة وحالة المراجعة -->
                    <div class="flex flex-wrap justify-between items-center gap-2 mb-3">
                        <span class="text-[10px] font-semibold px-2.5 py-1 rounded-md bg-slate-900 text-red-400 border border-slate-800 font-mono">
                            {{ $caseCatTrans->name ?? __('General') }}
                        </span>

                        @if($case->moderation_status?->value === 'approved')
                            <span class="text-[10px] font-semibold px-2.5 py-1 rounded-md bg-emerald-950/70 text-emerald-400 border border-emerald-800/60">
                                {{ __('Published') }}
                            </span>
                        @elseif($case->moderation_status?->value === 'pending')
                            <span class="text-[10px] font-semibold px-2.5 py-1 rounded-md bg-amber-950/70 text-amber-300 border border-amber-800/60">
                                {{ __('Pending Review') }}
                            </span>
                        @else
                            <span class="text-[10px] font-semibold px-2.5 py-1 rounded-md bg-rose-950/70 text-rose-300 border border-rose-800/60">
                                {{ __('Rejected') }}
                            </span>
                        @endif
                    </div>

                    <!-- العنوان (رابط إذا كانت منشورة) -->
                    <h3 class="text-base font-bold text-gray-100 mb-2 line-clamp-2">
                        @if($case->moderation_status === 'approved')
                            <a href="{{ route('community.cases.show', $case->slug) }}" class="hover:text-red-500 transition-colors">
                                {{ $case->title }}
                            </a>
                        @else
                            <span>{{ $case->title }}</span>
                        @endif
                    </h3>

                    <!-- مقتطف المحتوى -->
                    <p class="text-gray-400 text-xs line-clamp-3 mb-4 leading-relaxed">
                        {{ Str::limit(strip_tags($case->body), 120) }}
                    </p>
                </div>

                <!-- الشريط السفلي: الإحصائيات + أزرار التحكم -->
                <div class="pt-3 border-t border-slate-800 flex items-center justify-between text-[11px] text-slate-400">
                    <div class="flex items-center gap-2.5 font-mono">
                        <span>📍 {{ $case->locations->count() }}</span>
                        <span>💬 {{ $case->comments_count }}</span>
                        <span>👁️ {{ $case->views_count }}</span>
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        @if($case->moderation_status !== 'rejected')
                            <button wire:click="openEditModal({{ $case->id }})"
                                    class="px-2.5 py-1 rounded-lg bg-blue-600/20 text-blue-400 hover:bg-blue-600/30 border border-blue-600/30 font-semibold transition">
                                ✏️ {{ __('Edit') }}
                            </button>
                        @endif

                        <button wire:click="deleteCase({{ $case->id }})"
                                wire:confirm="{{ __('Are you sure you want to delete this case?') }}"
                                class="px-2.5 py-1 rounded-lg bg-rose-600/20 text-rose-400 hover:bg-rose-600/30 border border-rose-600/30 font-semibold transition">
                            🗑️ {{ __('Delete') }}
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center bg-[#0f1526] border border-slate-800 rounded-xl space-y-3">
                <p class="text-sm text-gray-400">{{ __('You have not submitted any cases yet.') }}</p>
                <a href="{{ route('community.cases.index') }}"
                   class="inline-block px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-lg transition">
                    {{ __('Submit Your First Case') }}
                </a>
            </div>
        @endforelse
    </div>

    <!-- التصفح -->
    <div class="mt-8">
        {{ $cases->links() }}
    </div>

    <!-- Modal تعديل القضية -->
    @if($showEditModal)
        <div class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4"
             wire:click.self="closeEditModal">

            <div class="bg-[#0f1526] border border-slate-800 w-full max-w-2xl max-h-[90vh] overflow-y-auto rounded-2xl p-6 shadow-2xl relative">

                <div class="flex justify-between items-center mb-6 pb-4 border-b border-slate-800">
                    <h3 class="text-base font-bold text-white">{{ __('Edit Case') }}</h3>
                    <button wire:click="closeEditModal" class="text-gray-400 hover:text-white text-xl leading-none">&times;</button>
                </div>

                <form wire:submit.prevent="updateCase" class="space-y-4">

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

                    <div>
                        <label class="block text-xs font-semibold text-gray-400 mb-1">{{ __('Case Status') }}</label>
                        <select wire:model.defer="edit_case_status"
                                class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2 text-white text-sm focus:outline-none focus:border-red-600">
                            @foreach(\App\Enums\CommunityCaseStatus::cases() as $status)
                                <option value="{{ $status->value }}">{{ $status->label() }}</option>
                            @endforeach
                        </select>
                        @error('edit_case_status') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-400 mb-1">{{ __('Case Title') }}</label>
                        <input type="text" wire:model.defer="edit_title"
                               class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2 text-white text-sm focus:outline-none focus:border-red-600">
                        @error('edit_title') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-400 mb-1">{{ __('Case Details & Evidence') }}</label>
                        <textarea wire:model.defer="edit_body" rows="5"
                                  class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2 text-white text-sm focus:outline-none focus:border-red-600"></textarea>
                        @error('edit_body') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- المواقع الجغرافية (اختياري - متعدد) -->
                    <div class="bg-slate-900/60 border border-slate-800 rounded-xl p-4 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-blue-400">📍 {{ __('Case Locations (Optional)') }}</span>
                            <button type="button" wire:click="addEditLocation"
                                    class="text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-gray-200 px-3 py-1 rounded-lg border border-slate-700 transition">
                                + {{ __('Add Location') }}
                            </button>
                        </div>

                        @foreach($edit_locations as $index => $loc)
                            <div wire:key="profile-edit-loc-{{ $index }}" class="bg-slate-950/70 border border-slate-800/90 rounded-xl p-3.5 space-y-2.5">
                                <div class="flex items-center justify-between text-xs text-gray-400">
                                    <span class="font-semibold">{{ __('Location #:num', ['num' => $index + 1]) }}</span>
                                    <button type="button" wire:click="removeEditLocation({{ $index }})"
                                            class="text-rose-400 hover:text-rose-300 text-xs">
                                        {{ __('Remove') }}
                                    </button>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-gray-400 mb-1">{{ __('Location Title') }}</label>
                                    <input type="text" wire:model.defer="edit_locations.{{ $index }}.title"
                                           class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3.5 py-2 text-white text-sm focus:outline-none focus:border-red-600">
                                    @error("edit_locations.$index.title") <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-400 mb-1">{{ __('Latitude') }}</label>
                                        <input type="text" wire:model.defer="edit_locations.{{ $index }}.latitude"
                                               class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3.5 py-2 text-white text-sm font-mono focus:outline-none focus:border-red-600">
                                        @error("edit_locations.$index.latitude") <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-xs font-semibold text-gray-400 mb-1">{{ __('Longitude') }}</label>
                                        <input type="text" wire:model.defer="edit_locations.{{ $index }}.longitude"
                                               class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3.5 py-2 text-white text-sm font-mono focus:outline-none focus:border-red-600">
                                        @error("edit_locations.$index.longitude") <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-gray-400 mb-1">{{ __('Location Notes') }}</label>
                                    <textarea wire:model.defer="edit_locations.{{ $index }}.description" rows="2"
                                              class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3.5 py-2 text-white text-sm focus:outline-none focus:border-red-600"></textarea>
                                    @error("edit_locations.$index.description") <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        @endforeach
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