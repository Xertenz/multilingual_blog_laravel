<li class="mb-4">
    <div id="reply-{{$reply->id}}">
        <div class="flex justify-between ms-3">
            <p class="capitalize whitespace-normal text-slate-200 font-bold">{{$reply->user->name}}:</p>
            <p class="text-slate-300">{{$reply->created_at->diffForHumans(['parts' => 1, 'short' => true])}}</p>
        </div>

        <div class="ms-8">
            <p id="reply-text-{{$reply->id}}">{{$reply->body}}</p>
            @if (Auth::check() && Auth::id() == $reply->user_id)
            <div id="edit-reply-form-{{$reply->id}}" style="display: none">
                <textarea
                        class="w-full text-slate-200 bg-slate-600 placeholder-slate-400 py-2 px-4 mb-2 rounded-md border-2 border-slate-500 transition-colors duration-300 focus:border-red-600 focus:shadow-transparent focus:outline-none focus:ring-0"
                    rows="1"
                    id="new-reply-input-{{$reply->id}}"
                >{{$reply->body}}</textarea>
                <button 
                    onclick="updateReply(this, {{$reply->id}})"
                    class="px-4 py-1 border-2 border-slate-500 rounded-md hover:border-red-600 transition-colors duration-300"
                    >update</button>
                <button 
                    onclick="hideEditReplyForm(this, {{$reply->id}})"
                    class="px-4 py-1 border-2 border-slate-500 rounded-md hover:border-red-600 transition-colors duration-300"
                    >close</button>
            </div>
            @endif
        </div>

        <div id="reply-actions-{{$reply->id}}" class="reply-actions my-2 ms-8 flex justify-start gap-2">
            @if (Auth::check() && Auth::id() == $reply->user_id)
            <button
                onclick="showEditReplyForm(this, {{$reply->id}})"
                class="underline underline-offset-4 italic"
                >edit</button>
            <button
                onclick="deleteReply({{$reply->id}})"
                class="underline underline-offset-4 italic"
                >delete</button>
            @endif
        </div>
    </div>
</li>