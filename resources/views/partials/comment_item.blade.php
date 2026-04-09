<li>
    <div id="comment-{{$comment_item->id}}" class="bg-slate-600 mb-3 p-3 rounded-md">
        <div class="flex justify-between mb-2">
            <p class="capitalize whitespace-normal text-slate-200 font-bold">{{$comment_item->user->name}}:</p>
            <p class="text-slate-300">{{$comment_item->created_at->diffForHumans(['parts' => 1, 'short' => true])}}</p>
        </div>
        <p
            id="comment-text-{{$comment_item->id}}"
            class="ms-8 mb-2"
            style="white-space: pre-wrap"
            >{{ $comment_item->body }}</p>

            <div id="comment-actions-{{$comment_item->id}}" class="actions my-5 ms-8 flex justify-start gap-2">
            @if(Auth::check())
                <div id="comment-reply-{{$comment_item->id}}" class="actions flex justify-end gap-2">
                    <button
                        onclick="showReplyForm({{$comment_item->id}})"
                        class="underline underline-offset-4 italic"
                    >reply</button>
                </div>
            @endif
            @if (Auth::check() && Auth::id() == $comment_item->user_id)
                <button
                    onclick="showEditForm({{$comment_item->id}})"
                    class="underline underline-offset-4 italic"
                >edit</button>
                <button
                    onclick="deleteComment({{$comment_item->id}})"
                    class="underline underline-offset-4 italic"
                >delete</button>
            @endif
            </div>

        @if (Auth::check() && Auth::id() == $comment_item->user_id)
            <div id="edit-form-{{$comment_item->id}}" class="mb-5" style="display: none">
                <textarea
                    id="edit-input-{{$comment_item->id}}"
                    rows="10"
                    style="field-sizing: content;"
                    class="w-full text-slate-200 bg-slate-600 placeholder-slate-400 py-2 px-4 mb-2 rounded-md border-2 border-slate-500 transition-colors duration-300 focus:border-red-600 focus:shadow-transparent focus:outline-none focus:ring-0"
                >{{$comment_item->body}}</textarea>
                <div class="flex justify-start items-center gap-2">
                    <button
                        onclick="updateComment({{$comment_item->id}})"
                        class="px-4 py-1 border-2 border-slate-500 rounded-md hover:border-red-600 transition-colors duration-300"
                        >Update</button>
                    <button
                        onclick="hideEditForm({{$comment_item->id}})"
                        class="px-4 py-1 border-2 border-slate-500 rounded-md hover:border-red-600 transition-colors duration-300"
                        >Close</button>
                </div>
            </div>
        @endif



        <div id="reply-form-{{$comment_item->id}}" class="my-5 ms-8" style="display: none">
            <textarea
                placeholder="Enter a reply here..."
                id="reply-input-{{$comment_item->id}}"
                data-article-id="{{$comment_item->article->id}}"
                rows="10"
                style="field-sizing: content;"
                class="w-full text-slate-200 bg-slate-600 placeholder-slate-400 py-2 px-4 mb-2 rounded-md border-2 border-slate-500 transition-colors duration-300 focus:border-red-600 focus:shadow-transparent focus:outline-none focus:ring-0"
            ></textarea>
            <div class="flex justify-start items-center gap-2">
                <button
                    onclick="replyToComment(this, {{$comment_item->id}})"
                    class="px-4 py-1 border-2 border-slate-500 rounded-md hover:border-red-600 transition-colors duration-300"
                    >Reply</button>
                <button
                    onclick="hideReplyForm({{$comment_item->id}})"
                    class="px-4 py-1 border-2 border-slate-500 rounded-md hover:border-red-600 transition-colors duration-300"
                    >Close</button>
            </div>
        </div>

        <div id="replies-container-{{$comment_item->id}}" class="ms-8 replies-container" style="border-left: 2px solid #eee;">
            @if($comment_item->replies->count() > 0)
            <ul>
            @foreach($comment_item->replies as $reply)
                {{-- نمرر الرد كأنه comment_item للملف نفسه --}}
                {{-- @include('partials.comment_item', ['comment_item' => $reply]) --}}
                @include('partials.comment_reply', ['reply' => $reply])
            @endforeach
            </ul>
            @endif
        </div>

    </div>
</li>