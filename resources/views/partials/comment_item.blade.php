<li>
    <div id="comment-{{$comment_item->id}}" class="bg-slate-600 mb-3 p-3 rounded-md">
        <div class="flex justify-between mb-2">
            <p class="capitalize whitespace-normal text-slate-200 font-bold">{{$comment_item->user->name}}:</p>
            <p class="text-slate-300">{{$comment_item->created_at->diffForHumans(['parts' => 1, 'short' => true])}}</p>
        </div>
        <p
            id="comment-text-{{$comment_item->id}}"
            class="mx-8 mb-2"
            style="white-space: pre-wrap"
            >{{ $comment_item->body }}</p>
        @if (Auth::check() && Auth::id() == $comment_item->user_id)
            <div id="comment-actions-{{$comment_item->id}}" class="actions flex justify-end gap-2">
                <button
                    onclick="showEditForm({{$comment_item->id}})"
                    class="px-4 py-1 border-2 border-slate-500 rounded-md hover:border-red-600 transition-colors duration-300"
                >edit</button>
                <button
                    onclick="deleteComment({{$comment_item->id}})"
                    class="px-4 py-1 border-2 rounded-md border-red-600 transition-colors duration-300"
                >delete</button>
            </div>
            <div id="edit-form-{{$comment_item->id}}" class="mt-5" style="display: none">
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
                        >Save</button>
                    <button
                        onclick="hideEditForm({{$comment_item->id}})"
                        class="px-4 py-1 border-2 border-slate-500 rounded-md hover:border-red-600 transition-colors duration-300"
                        >Close</button>
                </div>
            </div>
        @endif
    </div>
</li>