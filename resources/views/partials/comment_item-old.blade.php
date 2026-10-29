{{-- resources/views/partials/comment_item.blade.php --}}
<li wire:key="comment-item-{{ $comment_item->id }}">
    <div id="comment-{{$comment_item->id}}" class="bg-slate-900/50 mb-3 p-3 rounded-xl border border-slate-800 text-sm">
        
        <div class="flex justify-between items-center mb-1.5">
            <p class="capitalize whitespace-normal text-slate-200 font-bold flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                <a href="{{ route('profile.show', $comment_item->user->id) }}">
                    {{ $comment_item->user->name }}
                </a>
            </p>
            <p class="text-slate-500 font-mono text-[11px]">{{ $comment_item->created_at->diffForHumans(['parts' => 1, 'short' => true]) }}</p>
        </div>

        <p id="comment-text-{{$comment_item->id}}" class="ms-3 mb-1 text-slate-300 text-xs leading-relaxed" style="white-space: pre-wrap">{!! $comment_item->formatted_body !!}</p>

        <!-- شريط التفاعلات -->
        <div class="flex flex-wrap items-center gap-1.5 ms-3 my-2">
            @php
                $reactionTypes = [
                    'like' => ['icon' => '👍'],
                    'dislike' => ['icon' => '👎'],
                    'shocked' => ['icon' => '😲'],
                    'confused' => ['icon' => '😕'],
                    'sad' => ['icon' => '😢'],
                    'angry' => ['icon' => '😡'],
                    'mind_blown' => ['icon' => '🤯'],
                ];
                $userCommentReaction = Auth::check() ? $comment_item->reactions->where('user_id', Auth::id())->first() : null;
            @endphp

            @foreach($reactionTypes as $key => $data)
                @php
                    $count = $comment_item->reactions->where('type', $key)->count();
                    $isActive = $userCommentReaction && $userCommentReaction->type === $key;
                @endphp
                <button type="button"
                        wire:click="toggleReaction({{ $comment_item->id }}, '{{ $key }}')"
                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium transition border
                        {{ $isActive 
                            ? 'bg-red-950/80 text-red-400 border-red-800/60 shadow-sm' 
                            : 'bg-slate-800/60 text-gray-400 border-slate-700/50 hover:bg-slate-800 hover:text-gray-200' 
                        }}">
                    <span>{{ $data['icon'] }}</span>
                    <span class="font-mono text-[10px]">{{ $count > 0 ? $count : '' }}</span>
                </button>
            @endforeach
        </div>

        <!-- أزرار الإجراءات -->
        <div class="actions my-1.5 ms-3 flex justify-start gap-3 text-[11px] text-slate-500 font-medium">
            @if(Auth::check())
                <button wire:click="showReplyForm({{ $comment_item->id }})" class="hover:text-sky-400 transition">reply</button>
            @endif
            @if (Auth::check() && Auth::id() == $comment_item->user_id)
                <span class="text-slate-800">|</span>
                <button wire:click="showEditForm({{ $comment_item->id }}, '{{ addslashes($comment_item->body) }}')" class="hover:text-amber-400 transition">edit</button>
                <span class="text-slate-800">|</span>
                <button wire:confirm="Are you sure you want to delete this comment?" wire:click="deleteComment({{ $comment_item->id }})" class="hover:text-rose-500 transition">delete</button>
            @endif
        </div>

        <!-- نموذج التعديل -->
        @if ($editingCommentId === $comment_item->id)
            <div wire:transition class="my-2 ms-3" wire:key="edit-box-{{ $comment_item->id }}">
                <textarea
                    wire:model="editBody"
                    x-data
                    x-init="$el.focus()"
                    x-on:keydown.enter="if (!$event.shiftKey) { $event.preventDefault(); $wire.updateComment({{ $comment_item->id }}); }"
                    rows="2"
                    style="field-sizing: content;"
                    class="w-full text-xs text-slate-200 bg-slate-800 placeholder-slate-500 py-2 px-3 mb-1.5 rounded-lg border border-slate-700 transition duration-200 focus:border-red-600 focus:outline-none focus:ring-0"
                ></textarea>
                <div class="flex justify-start items-center gap-2">
                    <button wire:click="updateComment({{ $comment_item->id }})" class="bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] font-semibold px-2.5 py-1 rounded">Update</button>
                    <button wire:click="hideEditForm" class="text-[10px] text-gray-400 hover:underline px-2 py-1">Close</button>
                </div>
            </div>
        @endif

        <!-- نموذج الرد -->
        @if ($replyToCommentId === $comment_item->id)
            <div wire:transition class="my-2 ms-3" wire:key="reply-box-{{ $comment_item->id }}">
                <textarea
                    wire:model="replyBody"
                    x-data
                    x-init="$el.focus()"
                    x-on:keydown.enter="if (!$event.shiftKey) { $event.preventDefault(); $wire.addReply({{ $comment_item->id }}); }"
                    placeholder="Enter a reply here..."
                    rows="2"
                    style="field-sizing: content;"
                    class="w-full text-xs text-slate-200 bg-slate-800 placeholder-slate-500 py-2 px-3 mb-1.5 rounded-lg border border-slate-700 transition duration-200 focus:border-red-600 focus:outline-none focus:ring-0"
                ></textarea>
                <div class="flex justify-start items-center gap-2">
                    <button wire:click="addReply({{ $comment_item->id }})" class="bg-sky-600 hover:bg-sky-700 text-white text-[10px] font-semibold px-2.5 py-1 rounded">Reply</button>
                    <button wire:click="hideReplyForm" class="text-[10px] text-gray-400 hover:underline px-2 py-1">Close</button>
                </div>
            </div>
        @endif

{{-- حاوية الردود المتداخلة تابعة حصرياً لهذا التعليق الأب --}}
        <div id="replies-container-{{$comment_item->id}}" class="ms-3 mt-2 replies-container border-l border-slate-800/85 pl-4 rtl:border-l-0 rtl:border-r rtl:pl-0 rtl:pr-4">
            @if($comment_item->replies->count() > 0)
                <ul class="space-y-2.5">
                    @foreach($comment_item->replies->sortBy('created_at') as $reply)
                        @include('partials.comment_item', ['comment_item' => $reply])
                    @endforeach
                </ul>
            @endif
        </div>

    </div>
</li>
