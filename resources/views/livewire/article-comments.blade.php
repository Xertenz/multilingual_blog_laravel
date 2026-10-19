<section class="mt-16 pt-8 border-t border-gray-800">
    
    @php
        $totalComments = \App\Models\Comment::where('article_id', $articleId)->count();
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
                <textarea 
                    wire:model="newCommentBody"
                    @keydown.enter.prevent="$wire.addComment()"
                    rows="4"
                    placeholder="Write a public comment and press Enter..."
                    class="w-full text-slate-200 bg-slate-800 placeholder-slate-500 py-3 px-4 rounded-xl border border-slate-700 transition duration-200 focus:border-red-600 focus:outline-none focus:ring-0 text-sm"
                    style="field-sizing: content;"
                ></textarea>
                <div class="flex justify-end">
                    <button type="submit" class="bg-sky-600 hover:bg-sky-700 text-white font-semibold text-xs py-2 px-4 rounded-lg transition duration-150">
                        Add Comment
                    </button>
                </div>
            </form>
        @else
            <div class="p-4 text-center rounded-lg bg-slate-900/80 border border-slate-800">
                <p class="text-sm text-gray-400 italic">
                    Create an account to participate in the discussion: 
                    <a href="{{ route('register') }}" class="text-sky-400 hover:underline font-semibold not-italic ml-1">Register Now</a>
                </p>
            </div>
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