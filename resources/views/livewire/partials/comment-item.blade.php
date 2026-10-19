<li wire:key="comment-item-{{ $comment->id }}">
    {{-- إضافة wire:key لمنع الوميض وضمان استقرار العنصر --}}
    <div id="comment-{{ $comment->id }}" 
         class="bg-slate-900/50 mb-4 p-4 rounded-xl border border-slate-800 text-sm transition-all duration-300 ease-in-out">
        
        <div class="flex justify-between items-center mb-2">
            <p class="capitalize whitespace-normal text-slate-100 font-bold text-sm flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-red-500"></span>
                <a href="{{route('profile.show', $comment->user->id)}}">
                    {{ $comment->user->name }}
                </a>
            </p>
            <p class="text-slate-400 font-mono text-xs">{{ $comment->created_at->diffForHumans(['parts' => 1, 'short' => true]) }}</p>
        </div>

        <p class="ms-4 mb-2 text-slate-200 text-sm leading-relaxed" style="white-space: pre-wrap">{!! $comment->formatted_body !!}</p>

        <!-- شريط التفاعلات (Reactions) -->
        <div class="flex flex-wrap items-center gap-1.5 ms-4 my-2.5">
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
                $userReaction = Auth::check() ? $comment->reactions->where('user_id', Auth::id())->first() : null;
            @endphp

            @foreach($reactionTypes as $key => $data)
                @php
                    $count = $comment->reactions->where('type', $key)->count();
                    $isActive = $userReaction && $userReaction->type === $key;
                @endphp
                <button type="button"
                        wire:click="toggleReaction({{ $comment->id }}, '{{ $key }}')"
                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-medium transition border
                        {{ $isActive 
                            ? 'bg-red-950/80 text-red-400 border-red-800/60 shadow-sm' 
                            : 'bg-slate-800/60 text-gray-300 border-slate-700/50 hover:bg-slate-800 hover:text-white' 
                        }}">
                    <span>{{ $data['icon'] }}</span>
                    <span class="font-mono text-xs">{{ $count > 0 ? $count : '' }}</span>
                </button>
            @endforeach
        </div>

        <!-- أزرار الإجراءات -->
        <div class="actions my-2 ms-4 flex justify-start gap-3 text-xs text-slate-400 font-medium">
            @auth
                <button wire:click="showReplyForm({{ $comment->id }})" class="hover:text-sky-400 transition">reply</button>
            @endauth
            
            @if (Auth::check() && Auth::id() == $comment->user_id)
                <span class="text-slate-700">|</span>
                <button wire:click="showEditForm({{ $comment->id }}, '{{ addslashes($comment->body) }}')" class="hover:text-amber-400 transition">edit</button>
                <span class="text-slate-700">|</span>
                <button wire:confirm="Are you sure you want to delete this comment?" wire:click="deleteComment({{ $comment->id }})" class="hover:text-rose-500 transition">delete</button>
            @endif
        </div>

        <!-- نموذج التعديل (مع x-init لجعل الـ Focus يتم تلقائياً) -->
        @if ($editingCommentId === $comment->id)
            <div wire:transition class="my-3 ms-4" wire:key="edit-box-{{ $comment->id }}">
                <textarea
                    wire:model="editBody"
                    @keydown.enter.prevent="$wire.updateComment({{ $comment->id }})"
                    x-data
                    x-init="$el.focus()"
                    rows="3"
                    class="w-full text-sm text-slate-100 bg-slate-800 placeholder-slate-400 py-2.5 px-3 mb-2 rounded-lg border border-slate-700 focus:border-red-600 focus:ring-0 focus:outline-none"
                    style="field-sizing: content;"
                ></textarea>
                <div class="flex gap-2">
                    <button wire:click="updateComment({{ $comment->id }})" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-3 py-1.5 rounded-lg">Update</button>
                    <button wire:click="hideEditForm" class="text-xs text-gray-400 hover:underline px-2 py-1">Close</button>
                </div>
            </div>
        @endif

        <!-- نموذج الرد (مع x-init لجعل الـ Focus يتم تلقائياً) -->
        @if ($replyToCommentId === $comment->id)
            <div wire:transition class="my-3 ms-4" wire:key="reply-box-{{ $comment->id }}">
                <textarea
                    wire:model="replyBody"
                    @keydown.enter.prevent="$wire.addReply({{ $comment->id }})"
                    x-data
                    x-init="$el.focus()"
                    placeholder="Enter a reply here..."
                    rows="3"
                    class="w-full text-sm text-slate-100 bg-slate-800 placeholder-slate-400 py-2.5 px-3 mb-2 rounded-lg border border-slate-700 focus:border-red-600 focus:ring-0 focus:outline-none"
                    style="field-sizing: content;"
                ></textarea>
                <div class="flex gap-2">
                    <button wire:click="addReply({{ $comment->id }})" class="bg-sky-600 hover:bg-sky-700 text-white text-xs font-semibold px-3 py-1.5 rounded-lg">Reply</button>
                    <button wire:click="hideReplyForm" class="text-xs text-gray-400 hover:underline px-2 py-1">Close</button>
                </div>
            </div>
        @endif

        <!-- الردود المتداخلة (Recursive Replies) -->
        <div class="ms-4 mt-3 border-l border-slate-800/85 pl-4 rtl:border-l-0 rtl:border-r rtl:pl-0 rtl:pr-4 space-y-3">
            @if($comment->replies->count() > 0)
                <ul class="space-y-3">
                    @foreach($comment->replies as $reply)
                        @include('livewire.partials.comment-item', ['comment' => $reply])
                    @endforeach
                </ul>
            @endif
        </div>

    </div>
</li>