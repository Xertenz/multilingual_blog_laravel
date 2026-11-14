<section class="mt-16 pt-8 border-t border-gray-800">
    
    @php
    /*
        $totalComments = \App\Models\Comment::where([
            'commentable_type' => \App\Models\Article::class,
            'commentable_id'=> $articleId
        ])->count();
        */
        $totalComments = $model->comments()->count();
    @endphp

    @if ($totalComments > 0)
        <h2 class="text-xl font-bold text-white mb-6">Comments ({{ $totalComments }})</h2>
    @else
        <h2 class="text-xl font-bold text-gray-400 mb-6 italic">No comments yet</h2>
    @endif

    <!-- نموذج إضافة تعليق جديد -->
    <div class="comments-wrapper mb-8 bg-slate-900/40 p-4 rounded-xl border border-slate-800">
        @auth
            <form wire:submit.prevent="addComment" class="space-y-3">
                
                <!-- أزرار اختيار نوع التعليق -->
                <div class="flex flex-wrap items-center gap-2 mb-2">
                    <span class="text-xs text-slate-400 font-medium">Type:</span>
                    
                    <label class="cursor-pointer text-xs px-2.5 py-1 rounded-md border transition"
                           :class="$wire.newCommentType === 'comment' ? 'bg-slate-700 text-white border-slate-600' : 'bg-slate-800/50 text-slate-400 border-slate-700'">
                        <input type="radio" wire:model="newCommentType" value="comment" class="hidden"> 💬 Comment
                    </label>

                    <label class="cursor-pointer text-xs px-2.5 py-1 rounded-md border transition"
                           :class="$wire.newCommentType === 'evidence' ? 'bg-red-950/80 text-red-400 border-red-800' : 'bg-slate-800/50 text-slate-400 border-slate-700'">
                        <input type="radio" wire:model="newCommentType" value="evidence" class="hidden"> 🔍 Evidence
                    </label>

                    <label class="cursor-pointer text-xs px-2.5 py-1 rounded-md border transition"
                           :class="$wire.newCommentType === 'hint' ? 'bg-amber-950/80 text-amber-400 border-amber-800' : 'bg-slate-800/50 text-slate-400 border-slate-700'">
                        <input type="radio" wire:model="newCommentType" value="hint" class="hidden"> 💡 Hint
                    </label>

                    <label class="cursor-pointer text-xs px-2.5 py-1 rounded-md border transition"
                           :class="$wire.newCommentType === 'question' ? 'bg-sky-950/80 text-sky-400 border-sky-800' : 'bg-slate-800/50 text-slate-400 border-slate-700'">
                        <input type="radio" wire:model="newCommentType" value="question" class="hidden"> ❓ Question
                    </label>
                </div>

                <textarea 
                    wire:model="newCommentBody"
                    @keydown.enter.prevent="$wire.addComment()"
                    rows="4"
                    placeholder="Write a public comment and press Enter..."
                    class="w-full text-slate-200 bg-slate-800 placeholder-slate-500 py-3 px-4 rounded-xl border border-slate-700 transition duration-200 focus:border-red-600 focus:outline-none focus:ring-0 text-sm"
                    style="field-sizing: content;"
                ></textarea>
                <div class="flex justify-end">
                    <button type="submit" class="bg-sky-600 hover:bg-sky-700 text-white font-semibold text-xs py-2 px-4 rounded-md transition duration-150">
                        Add Comment
                    </button>
                </div>
            </form>
        @else
            <!-- رسالة تسجيل الدخول -->
        @endauth
    </div>

    <!-- شجرة التعليقات -->
    <div class="mt-6">
        <ul class="space-y-4">
            @foreach ($comments as $comment)
                @include('livewire.partials.comment-item', ['comment' => $comment])
            @endforeach
        </ul>
    </div>
</section>