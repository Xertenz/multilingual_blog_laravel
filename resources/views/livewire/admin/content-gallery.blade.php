<div>
    {{-- زر فتح المعرض --}}
    <button wire:click="openModal" type="button"
            class="show-gallery inline-flex items-center gap-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 font-medium px-2.5 py-1 rounded-lg text-xs transition-colors cursor-pointer">
        <span>📷</span>
        <span class="font-semibold">{{ count($images) }}</span>
    </button>

    {{-- المودال --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4"
         wire:click.self="closeModal">
        <div class="bg-white rounded-2xl shadow-2xl border border-slate-100 w-full max-w-3xl overflow-hidden flex flex-col max-h-[85vh]">

            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 bg-slate-50 border-b border-slate-200">
                <div class="flex items-center gap-2">
                    <span class="text-base font-bold text-slate-800">Content Gallery</span>
                    <span class="bg-indigo-100 text-indigo-700 text-xs font-semibold px-2.5 py-0.5 rounded-full">
                        {{ count($images) }} Images
                    </span>
                </div>

                <div class="flex items-center gap-3">
                    <label class="cursor-pointer inline-flex items-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-xs px-3 py-2 rounded-lg shadow-sm transition-all">
                        <span wire:loading.remove wire:target="uploadImage, newImage">+ Upload Image</span>
                        <span wire:loading wire:target="uploadImage, newImage">Uploading...</span>
                        <input type="file" wire:model="newImage" class="hidden" accept="image/*">

                    </label>

                    <button wire:click="closeModal" type="button"
                            class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-200 hover:bg-red-100 text-slate-600 hover:text-red-600 transition-colors cursor-pointer text-sm font-bold">
                        &times;
                    </button>
                </div>
            </div>

            {{-- رسائل --}}
            @error('newImage')
                <div class="px-6 py-2 bg-red-50 text-red-600 text-xs border-b border-red-200">{{ $message }}</div>
            @enderror

            {{-- شبكة الصور --}}
            <div class="p-6 overflow-y-auto flex-1 bg-slate-50/50">
                @if(count($images) > 0)
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                        @foreach($images as $img)
                            <div wire:key="img-{{ $img['id'] }}"
                                 class="group relative bg-white border border-slate-200 rounded-xl overflow-hidden shadow-2xs aspect-square flex items-center justify-center">
                                <img src="{{ $img['url'] }}" alt="Gallery Image"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                     loading="lazy" />

                                <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                    <button wire:click="deleteImage({{ $img['id'] }})"
                                            wire:confirm="Are you sure you want to delete this image?"
                                            type="button"
                                            class="bg-red-600 hover:bg-red-700 text-white p-2 rounded-lg shadow-md transition-transform hover:scale-110 cursor-pointer text-xs font-medium flex items-center gap-1">
                                        🗑️ Delete
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center py-12 text-slate-400">
                        <span class="text-4xl mb-2">📁</span>
                        <p class="text-sm font-medium">No images uploaded yet.</p>
                        <p class="text-xs text-slate-400 mt-1">Upload images to use them inside your content.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>