{{-- resources/views/partials/comment_reply.blade.php --}}
<div class="scroll-mt-[80px] mt-2.5" id="reply-item-{{$reply->id}}">
    <div id="reply-{{$reply->id}}" class="bg-slate-900/30 p-2.5 rounded-lg border border-slate-800/40 hover:border-slate-800 transition">
        
        <div class="flex justify-between items-center mb-1">
            <p class="capitalize whitespace-normal text-slate-300 font-bold text-xs flex items-center gap-1">
                <span class="w-1 h-1 rounded-full bg-slate-500"></span>
                {{$reply->user->name}}
            </p>
            <p class="text-slate-500 font-mono text-[10px]">{{$reply->created_at->diffForHumans(['parts' => 1, 'short' => true])}}</p>
        </div>

        <div class="ms-2">
            {{-- <p id="reply-text-{{$reply->id}}" class="text-slate-300 text-xs leading-relaxed" style="white-space: pre-wrap">{{$reply->body}}</p> --}}
            <p id="reply-text-{{$reply->id}}" class="text-slate-300 text-xs leading-relaxed" style="white-space: pre-wrap">{!! $reply->formatted_body !!}</p>
            
            {{-- شريط التفاعلات الجنائية للرد (بنفس منطق التعليق تماماً) --}}
            <div class="flex flex-wrap items-center gap-1.5 my-2">
                @php
                    $reactionTypes = [
                        'like' => ['icon' => '👍', 'label' => 'Like'],
                        'dislike' => ['icon' => '👎', 'label' => 'Dislike'],
                        'shocked' => ['icon' => '😲', 'label' => 'Shocked'],
                        'confused' => ['icon' => '😕', 'label' => 'Confused'],
                        'sad' => ['icon' => '😢', 'label' => 'Sad'],
                        'angry' => ['icon' => '😡', 'label' => 'Angry'],
                        'mind_blown' => ['icon' => '🤯', 'label' => 'Mind Blown'],
                    ];
                    
                    // جلب تفاعل المستخدم الحالي على هذا الرد إن وجد
                    $userReplyReaction = Auth::check() ? $reply->reactions->where('user_id', Auth::id())->first() : null;
                @endphp

                @foreach($reactionTypes as $key => $data)
                    @php
                        $count = $reply->reactions->where('type', $key)->count();
                        $isActive = $userReplyReaction && $userReplyReaction->type === $key;
                    @endphp
                    <button type="button"
                            id="comment-reaction-{{$reply->id}}-{{$key}}"
                            onclick="toggleCommentReaction(event, {{$reply->id}}, '{{$key}}')"
                            data-active="{{ $isActive ? 'true' : 'false' }}"
                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-medium transition border
                            {{ $isActive 
                                ? 'bg-red-950/80 text-red-400 border-red-800/60 shadow-sm' 
                                : 'bg-slate-800/60 text-gray-400 border-slate-700/50 hover:bg-slate-800 hover:text-gray-200' 
                            }}">
                        <span>{{ $data['icon'] }}</span>
                        <span id="comment-reaction-count-{{$reply->id}}-{{$key}}" class="font-mono text-[9px] text-gray-300">{{ $count > 0 ? $count : '' }}</span>
                    </button>
                @endforeach
            </div>

            @if (Auth::check() && Auth::id() == $reply->user_id)
            <div id="edit-reply-form-{{$reply->id}}" style="display: none;" class="mt-1.5 space-y-1.5">
                <textarea
                    class="w-full text-xs text-slate-200 bg-slate-800 placeholder-slate-500 py-2 px-3 mb-1.5 rounded-lg border border-slate-700 transition duration-200 focus:border-red-600 focus:outline-none focus:ring-0"
                    rows="2"
                    style="field-sizing: content;"
                    id="new-reply-input-{{$reply->id}}"
                >{{$reply->body}}</textarea>
                <div class="flex gap-2 justify-start items-center">
                    <button onclick="updateReply({{$reply->id}})" class="bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] font-semibold px-2.5 py-1 rounded">Update</button>
                    <button onclick="hideEditReplyForm({{$reply->id}})" class="text-[10px] text-gray-400 hover:underline px-2 py-1">Close</button>
                </div>
            </div>
            @endif
        </div>

        {{-- نموذج الرد الفرعي (يظهر عند الضغط على زر Reply الخاص بهذا الرد بالذات) --}}
        @if(Auth::check())
        <div id="reply-form-{{$reply->id}}" class="my-2 ms-2" style="display: none">
            <textarea
                placeholder="Write a reply..."
                id="reply-input-{{$reply->id}}"
                data-article-id="{{$reply->article_id}}"
                rows="2"
                style="field-sizing: content;"
                class="w-full text-xs text-slate-200 bg-slate-800 placeholder-slate-500 py-2 px-3 mb-1.5 rounded-lg border border-slate-700 transition duration-200 focus:border-red-600 focus:outline-none focus:ring-0"
            ></textarea>
            <div class="flex justify-start items-center gap-2">
                <button onclick="replyToComment(this, {{$reply->id}})" class="bg-sky-600 hover:bg-sky-700 text-white text-[10px] font-semibold px-2.5 py-1 rounded">Reply</button>
                <button onclick="hideReplyForm({{$reply->id}})" class="text-[10px] text-gray-400 hover:underline px-2 py-1">Close</button>
            </div>
        </div>
        @endif

        <div id="reply-actions-{{$reply->id}}" class="reply-actions mt-1 ms-2 flex justify-start gap-3 text-[10px] font-medium text-slate-500">
            @if(Auth::check())
            <button onclick="showReplyForm({{$reply->id}})" class="hover:text-sky-400 transition">reply</button>
            <span class="text-slate-800/50">|</span>
            @endif
            @if (Auth::check() && Auth::id() == $reply->user_id)
            <button onclick="showEditReplyForm({{$reply->id}})" class="hover:text-amber-400 transition">edit</button>
            <span class="text-slate-800/50">|</span>
            <button onclick="deleteReply({{$reply->id}})" class="hover:text-rose-500 transition">delete</button>
            @endif
        </div>

        {{-- استدعاء الردود الفرعية للرد الحالي بشكل شجري متداخل (Recursive) --}}
        <div id="replies-container-{{$reply->id}}" class="ms-3 mt-2 replies-container border-l border-slate-800/85 pl-4 rtl:border-l-0 rtl:border-r rtl:pl-0 rtl:pr-4 space-y-2.5">
            @if($reply->replies->count() > 0)
                @foreach($reply->replies as $subReply)
                    @include('partials.comment_reply', ['reply' => $subReply])
                @endforeach
            @endif
        </div>

    </div>
</div>