<div class="container mx-auto px-4 py-10 max-w-4xl text-white">
    
    <!-- زر العودة -->
    <div class="mb-6">
        <a href="{{ route('community.cases.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-gray-400 hover:text-white transition">
            <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>Back to Detective Board</span>
        </a>
    </div>

    <!-- بطاقة القضية الرئيسية -->
    <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-6 md:p-8 shadow-xl space-y-6 mb-8">
        
        <!-- معلومات رأس القضية -->
        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-800 pb-4">
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 text-xs font-bold bg-red-600/20 text-red-500 border border-red-600/30 rounded-lg uppercase tracking-wider">
                    {{ $categoryTrans->name ?? 'General' }}
                </span>

                @if($communityCase->case_status === 'open')
                    <span class="px-2.5 py-1 text-[11px] font-semibold bg-emerald-950/60 text-emerald-400 border border-emerald-800/40 rounded-lg">Active Investigation</span>
                @elseif($communityCase->case_status === 'solved')
                    <span class="px-2.5 py-1 text-[11px] font-semibold bg-blue-950/60 text-blue-400 border border-blue-800/40 rounded-lg">Solved Case</span>
                @else
                    <span class="px-2.5 py-1 text-[11px] font-semibold bg-slate-800 text-gray-400 border border-slate-700 rounded-lg">Closed</span>
                @endif
            </div>

            <div class="flex items-center gap-3 text-xs text-gray-400">
                <span>By <strong class="text-gray-200">{{ $communityCase->user->name }}</strong></span>
                <span>•</span>
                <span>{{ $communityCase->created_at->format('F j, Y') }}</span>
                <span>•</span>
                <span class="flex items-center gap-1">
                    👁️ {{ $communityCase->views_count }} Views
                </span>
            </div>
        </div>

        <h1 class="text-2xl md:text-3xl font-black text-white leading-tight">
            {{ $communityCase->title }}
        </h1>

        <div class="prose prose-invert max-w-none text-gray-300 leading-relaxed text-sm md:text-base space-y-4 pt-2">
            {!! $communityCase->body !!}
        </div>
    </div>

    <!-- قسم التعليقات والنقاشات المجتمعية -->
    <div class="bg-slate-900/40 border border-slate-800 rounded-2xl p-6 md:p-8 shadow-xl space-y-6">
        <h3 class="text-lg font-bold text-white flex items-center gap-2">
            <span>💬 Investigation Discussion</span>
            <span class="text-xs px-2 py-0.5 bg-slate-800 text-gray-400 rounded-full font-mono">{{ $comments->count() }}</span>
        </h3>

        <!-- نموذج إضافة تعليق جديد -->
        @auth
            <form wire:submit.prevent="addComment" class="space-y-3">
                <div>
                    <textarea wire:model="newCommentBody" 
                        x-on:keydown.enter="if (!$event.shiftKey) { $event.preventDefault(); $wire.addComment(); }"
                        rows="3" placeholder="Share your evidence, theory, or thoughts on this case..." 
                        class="w-full bg-slate-950/60 border border-slate-800 rounded-xl p-4 text-sm text-white placeholder-gray-500 focus:border-red-600 focus:ring-1 focus:ring-red-600 outline-none transition"></textarea>
                    @error('newCommentBody') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="px-5 py-2.5 bg-red-600 hover:bg-red-500 text-white text-xs font-bold rounded-xl transition shadow-lg shadow-red-600/20">
                        Post Evidence / Comment
                    </button>
                </div>
            </form>
        @else
            <div class="p-4 bg-slate-800/40 border border-slate-800 rounded-xl text-center text-xs text-gray-400">
                Please <a href="{{ route('login') }}" class="text-red-500 font-bold hover:underline">log in</a> to participate in this investigation.
            </div>
        @endauth

        <!-- قائمة التعليقات الرئيسية (مغلفة بـ ul لضمان انتظام الحاويات مثل المقالات) -->
        <div class="pt-4 border-t border-slate-800">
            <ul class="space-y-4">
                @forelse($comments as $comment_item)
                    @include('partials.comment_item', ['comment_item' => $comment_item])
                @empty
                    <div class="text-center py-8 text-gray-500 text-xs italic">
                        {{ __('No theories or comments posted yet. Be the first investigator to share insights!') }}
                    </div>
                @endforelse
            </ul>
        </div>
    </div>

</div>