<li>
    <div id="comment-{{$comment_item->id}}" class="bg-slate-900/50 mb-3 p-3 rounded-xl border border-slate-800 text-sm">
        
        <div class="flex justify-between items-center mb-1.5">
            <p class="capitalize whitespace-normal text-slate-200 font-bold flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                {{$comment_item->user->name}}
            </p>
            <p class="text-slate-500 font-mono text-[11px]">{{$comment_item->created_at->diffForHumans(['parts' => 1, 'short' => true])}}</p>
        </div>

        <p id="comment-text-{{$comment_item->id}}" class="ms-3 mb-1 text-slate-300 text-xs leading-relaxed" style="white-space: pre-wrap">{{ $comment_item->body }}</p>

        <div id="comment-actions-{{$comment_item->id}}" class="actions my-1.5 ms-3 flex justify-start gap-3 text-[11px] text-slate-500 font-medium">
            @if(Auth::check())
                <div id="comment-reply-{{$comment_item->id}}" class="actions flex justify-end gap-2">
                    <button onclick="showReplyForm({{$comment_item->id}})" class="hover:text-sky-400 transition">reply</button>
                </div>
            @endif
            @if (Auth::check() && Auth::id() == $comment_item->user_id)
                <span class="text-slate-800">|</span>
                <button onclick="showEditForm({{$comment_item->id}})" class="hover:text-amber-400 transition">edit</button>
                <span class="text-slate-800">|</span>
                <button onclick="deleteComment({{$comment_item->id}})" class="hover:text-rose-500 transition">delete</button>
            @endif
        </div>

        @if (Auth::check() && Auth::id() == $comment_item->user_id)
            <div id="edit-form-{{$comment_item->id}}" class="my-2 ms-3" style="display: none">
                <textarea
                    id="edit-input-{{$comment_item->id}}"
                    rows="2" {{-- تم تقليله من 10 إلى 2 لضغط المساحة الرأسية --}}
                    style="field-sizing: content;"
                    class="w-full text-xs text-slate-200 bg-slate-800 placeholder-slate-500 py-2 px-3 mb-1.5 rounded-lg border border-slate-700 transition duration-200 focus:border-red-600 focus:outline-none focus:ring-0"
                >{{$comment_item->body}}</textarea>
                <div class="flex justify-start items-center gap-2">
                    <button onclick="updateComment({{$comment_item->id}})" class="bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] font-semibold px-2.5 py-1 rounded">Update</button>
                    <button onclick="hideEditForm({{$comment_item->id}})" class="text-[10px] text-gray-400 hover:underline px-2 py-1">Close</button>
                </div>
            </div>
        @endif

        <div id="reply-form-{{$comment_item->id}}" class="my-2 ms-3" style="display: none">
            <textarea
                placeholder="Enter a reply here..."
                id="reply-input-{{$comment_item->id}}"
                data-article-id="{{$comment_item->article->id}}"
                rows="2" {{-- تم تقليله من 10 إلى 2 لمنع الانتفاخ الرأسي --}}
                style="field-sizing: content;"
                class="w-full text-xs text-slate-200 bg-slate-800 placeholder-slate-500 py-2 px-3 mb-1.5 rounded-lg border border-slate-700 transition duration-200 focus:border-red-600 focus:outline-none focus:ring-0"
            ></textarea>
            <div class="flex justify-start items-center gap-2">
                <button onclick="replyToComment(this, {{$comment_item->id}})" class="bg-sky-600 hover:bg-sky-700 text-white text-[10px] font-semibold px-2.5 py-1 rounded">Reply</button>
                <button onclick="hideReplyForm({{$comment_item->id}})" class="text-[10px] text-gray-400 hover:underline px-2 py-1">Close</button>
            </div>
        </div>

        <div id="replies-container-{{$comment_item->id}}" class="ms-3 mt-2 replies-container border-l border-slate-800/85 pl-4 rtl:border-l-0 rtl:border-r rtl:pl-0 rtl:pr-4">
            @if($comment_item->replies->count() > 0)
            <ul class="space-y-2 mt-2">
            @foreach($comment_item->replies as $reply)
                @include('partials.comment_reply', ['reply' => $reply])
            @endforeach
            </ul>
            @endif
        </div>

    </div>
</li>