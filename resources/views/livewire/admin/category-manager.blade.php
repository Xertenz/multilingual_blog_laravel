<div>
    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-xl font-bold text-slate-800">{{ $pageTitle }}</h2>
            <p class="text-sm text-slate-500">Manage, edit, and organize {{ strtolower($pageTitle) }}</p>
        </div>
        <button wire:click="openCreateModal"
                type="button"
                class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm px-4 py-2 rounded-lg shadow-sm flex items-center gap-2">
            <span>+ Add New</span>
        </button>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase">
                        <th class="py-3 px-4 w-16">ID</th>
                        <th class="py-3 px-4">Label</th>
                        <th class="py-3 px-4">Translations</th>
                        @if($countRelation)
                            <th class="py-3 px-4 w-24">Count</th>
                        @endif
                        <th class="py-3 px-4 w-32">Status</th>
                        <th class="py-3 px-4 text-right w-44">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($items as $item)
                        <tr class="hover:bg-slate-50/70 transition-colors" wire:key="cat-row-{{ $item->id }}">
                            <td class="py-3 px-4 font-mono text-xs text-slate-500">#{{ $item->id }}</td>
                            <td class="py-3 px-4 font-medium text-slate-800">{{ $item->label }}</td>

                            {{-- Translations --}}
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-1.5 flex-nowrap">
                                    @foreach($supportedLocales as $code => $props)
                                        @php $trans = $item->translations->firstWhere('locale', $code); @endphp
                                        @if($trans)
                                            <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-lg"
                                                  title="{{ $trans->name }}">
                                                {{ strtoupper($code) }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-mono bg-red-50 text-red-400 border border-dashed border-red-200 rounded-lg"
                                                  title="{{ __('Missing translation') }}">
                                                {{ strtoupper($code) }}
                                            </span>
                                        @endif
                                    @endforeach
                                </div>
                            </td>

                            {{-- Count --}}
                            @if($countRelation)
                                <td class="py-3 px-4">
                                    <span class="bg-slate-100 text-slate-700 px-2.5 py-1 rounded-full text-xs font-semibold">
                                        {{ $item->{$countRelation . '_count'} ?? 0 }}
                                    </span>
                                </td>
                            @endif

                            {{-- Status --}}
                            <td class="py-3 px-4">
                                <button wire:click="toggleActive({{ $item->id }})"
                                        type="button"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium border transition-colors
                                        {{ $item->is_active
                                            ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100'
                                            : 'bg-slate-100 text-slate-600 border-slate-200 hover:bg-slate-200' }}"
                                        title="{{ $item->is_active ? 'Click to deactivate' : 'Click to activate' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $item->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                    {{ $item->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </td>

                            {{-- Actions --}}
                            <td class="py-3 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button wire:click="openEditModal({{ $item->id }})"
                                            type="button"
                                            class="bg-blue-50 text-blue-600 hover:bg-blue-100 border border-blue-200 px-2.5 py-1 rounded-lg text-xs font-medium transition-colors">
                                        Edit
                                    </button>
                                    <button wire:click="delete({{ $item->id }})"
                                            wire:confirm="{{ __('Are you sure you want to delete this item?') }}"
                                            type="button"
                                            class="bg-red-50 text-red-600 hover:bg-red-100 border border-red-200 px-2.5 py-1 rounded-lg text-xs font-medium transition-colors">
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $countRelation ? 6 : 5 }}" class="text-center py-12 text-slate-400 text-sm">
                                <div class="flex flex-col items-center justify-center">
                                    <span class="text-3xl mb-2">📂</span>
                                    <p class="font-medium text-slate-600">No {{ strtolower($pageTitle) }} found.</p>
                                    <button wire:click="openCreateModal" type="button"
                                            class="text-indigo-600 hover:underline text-xs font-medium mt-2">
                                        Create your first one &rarr;
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($items->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $items->links() }}
            </div>
        @endif
    </div>

    {{-- Modal (Create/Edit) — نفس تصميم TagManager --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4"
             wire:click.self="$set('showModal', false)">
            <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-3xl overflow-hidden max-h-[90vh] flex flex-col">

                <div class="flex justify-between items-center px-6 py-4 bg-slate-50 border-b border-slate-200">
                    <h3 class="text-base font-bold text-slate-800">
                        {{ $editingId ? __('Edit') : __('Create') }} {{ $pageTitle }}
                    </h3>
                    <button wire:click="$set('showModal', false)"
                            type="button"
                            class="text-slate-400 hover:text-red-600 text-xl leading-none">
                        &times;
                    </button>
                </div>

                <div class="p-6 overflow-y-auto flex-1">

                    {{-- Basic Info --}}
                    <div class="bg-white rounded-xl p-4 border border-slate-200 mb-4">
                        <div class="mb-3">
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                                System Label (Slug) <span class="text-red-500">*</span>
                            </label>
                            <input type="text" wire:model="label"
                                   class="w-full rounded-lg border border-slate-300 text-sm py-2 px-3 outline-none transition focus:border-indigo-500 focus:ring-indigo-500 @error('label') border-red-500 bg-red-50/50 @enderror"
                                   placeholder="e.g., true-crime" autocomplete="off">
                            @error('label') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                            <p class="text-[11px] text-slate-400 mt-1">Unique identifier in English, only for admins.</p>
                        </div>

                        <div>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" wire:model="is_active"
                                       class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 w-4 h-4">
                                <span class="text-sm font-medium text-slate-700">Active (visible to visitors)</span>
                            </label>
                            <p class="text-[11px] text-slate-400 mt-1">Inactive categories won't appear in public menus or lists.</p>
                        </div>
                    </div>

                    {{-- Translations --}}
                    @foreach($supportedLocales as $code => $props)
                        <div class="bg-white rounded-xl p-4 border border-slate-200 mb-3">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                                    </svg>
                                    <h4 class="text-xs font-semibold text-slate-700">
                                        {{ $props['native'] }} ({{ strtoupper($code) }})
                                    </h4>
                                </div>
                                @if($code === config('app.locale') || $code === 'en')
                                    <span class="bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full text-[10px] font-medium">Required</span>
                                @else
                                    <span class="bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full text-[10px] font-medium">Optional</span>
                                @endif
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Name</label>
                                    <input type="text" wire:model="translations.{{ $code }}.name"
                                           dir="{{ $code === 'ar' ? 'rtl' : 'ltr' }}"
                                           class="w-full rounded-lg border border-slate-300 text-sm py-2 px-3 outline-none transition focus:border-indigo-500 focus:ring-indigo-500 @error("translations.{$code}.name") border-red-500 bg-red-50/50 @enderror">
                                    @error("translations.{$code}.name") <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Slug (Optional)</label>
                                    <input type="text" wire:model="translations.{{ $code }}.slug"
                                           dir="{{ $code === 'ar' ? 'rtl' : 'ltr' }}"
                                           class="w-full rounded-lg border border-slate-300 text-sm py-2 px-3 outline-none transition focus:border-indigo-500 focus:ring-indigo-500"
                                           placeholder="Auto-generated">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Description</label>
                                <textarea wire:model="translations.{{ $code }}.description" rows="2"
                                          dir="{{ $code === 'ar' ? 'rtl' : 'ltr' }}"
                                          class="w-full rounded-lg border border-slate-300 text-sm py-2 px-3 outline-none transition focus:border-indigo-500 focus:ring-indigo-500 resize-y min-h-[70px]"></textarea>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Meta Title</label>
                                    <input type="text" wire:model="translations.{{ $code }}.meta_title"
                                           dir="{{ $code === 'ar' ? 'rtl' : 'ltr' }}"
                                           class="w-full rounded-lg border border-slate-300 text-sm py-2 px-3 outline-none transition focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Meta Description</label>
                                    <textarea wire:model="translations.{{ $code }}.meta_description" rows="2"
                                              dir="{{ $code === 'ar' ? 'rtl' : 'ltr' }}"
                                              class="w-full rounded-lg border border-slate-300 text-sm py-2 px-3 outline-none transition focus:border-indigo-500 focus:ring-indigo-500 resize-y"></textarea>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="flex justify-end gap-3 px-6 py-4 border-t border-slate-200 bg-slate-50">
                    <button wire:click="$set('showModal', false)"
                            type="button"
                            class="px-4 py-2 bg-white text-slate-700 border border-slate-300 rounded-lg text-sm font-medium hover:bg-slate-50">
                        {{ __('Cancel') }}
                    </button>
                    <button wire:click="save"
                            type="button"
                            class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium shadow-sm">
                        {{ $editingId ? __('Update') : __('Create') }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>