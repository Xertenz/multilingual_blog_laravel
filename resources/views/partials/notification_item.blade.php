@php
    $data = $notification->data;
@endphp

<li class="notifications-list-item {{$notification->unread() ? 'bg-gray-600' : 'bg-gray-800'}} border-b border-gray-700/50 hover:bg-gray-700/60 transition-colors">

    {{-- 🛑 إذا كان إشعار حالة الاقتراح: نعرضه كـ button خارجي بدون رابط a محيط به --}}
    @if($notification->type === 'App\Notifications\SuggestionStatusUpdated')
        <div wire:click="openSuggestionModal('{{ $data['message'] ?? '' }}', '{{ $notification->id }}')" 
             class="block p-3 cursor-pointer w-full text-left">
            <div class="flex items-start gap-2">
                <div class="flex-1">
                    <p class="text-xs font-semibold text-red-400 uppercase tracking-wider mb-0.5">
                        📊 {{ __('Case Suggestion Update') }}
                    </p>
                    <p class="whitespace-nowrap max-w-[280px] overflow-hidden text-ellipsis text-sm font-medium text-gray-100">
                        {{ $data['message'] ?? '' }}
                    </p>
                </div>
            </div>
            <p class="text-start italic text-[11px] text-gray-400 mt-2">
                {{ $notification->created_at->diffForHumans() }}
            </p>
        </div>

    {{-- 🔗 لباقي الإشعارات العادية: تبقي على رابط الـ a التوجيهي --}}
    @else
        <a href="{{ route('notifications.read', ['id' => $notification->id]) }}" class="block p-3">
            
            {{-- 1️⃣ إشعار الرد على تعليق --}}
            @if($notification->type === 'App\Notifications\NewCommentReplyNotification')
                <div class="flex items-start gap-2">
                    <div class="flex-1">
                        <p class="whitespace-nowrap max-w-[280px] overflow-hidden text-ellipsis text-sm text-gray-200">
                            <span class="font-bold text-indigo-400">{{ $data['reply_author_name'] ?? 'Someone' }}</span>
                            <span>{{ __('replied to your comment:') }}</span>
                        </p>
                        <p class="whitespace-nowrap max-w-[280px] overflow-hidden text-ellipsis text-xs text-gray-400 mt-1">
                            "{{ $data['comment_body'] ?? '' }}"
                        </p>
                    </div>
                </div>

            {{-- 2️⃣ إشعار التفاعل على التعليق --}}
            @elseif($notification->type === 'App\Notifications\NewCommentReactionNotification')
                @php
                    $reactionEmojis = [
                        'like' => '👍', 'dislike' => '👎', 'shocked' => '😲',
                        'confused' => '😕', 'sad' => '😢', 'angry' => '😡', 'mind_blown' => '🤯',
                    ];
                    $reactionType = $data['reaction_type'] ?? 'like';
                    $emoji = $reactionEmojis[$reactionType] ?? '💬';
                @endphp
                <div class="flex items-start gap-2">
                    <div class="flex-1">
                        <p class="whitespace-nowrap max-w-[280px] overflow-hidden text-ellipsis text-sm text-gray-200">
                            <span class="font-bold text-red-400">{{ $data['user_name'] ?? 'Someone' }}</span>
                            <span>{{ __('reacted with') }}</span>
                            <span class="text-base mx-1">{{ $emoji }}</span>
                            <span>{{ __('to your comment') }}</span>
                        </p>
                    </div>
                </div>

            {{-- 3️⃣ إشعار مقالة جديدة من الفئة المفضلة --}}
            @elseif($notification->type === 'App\Notifications\NewArticleInFavoriteCategory')
                @php
                    $article = \App\Models\Article::with('translations')->find($data['article_id'] ?? null);
                    if ($article) {
                        $currentLocale = app()->getLocale();
                        $translation = $article->translations->where('locale', $currentLocale)->first() 
                                    ?? $article->translations->first();
                    }
                @endphp
                @if($article && isset($translation))
                    <div class="flex items-start gap-2">
                        <div class="flex-1">
                            <p class="text-xs font-semibold text-purple-400 uppercase tracking-wider mb-0.5">
                                📁 {{ $data['category_label'] ?? __('New Article') }}
                            </p>
                            <p class="whitespace-nowrap max-w-[280px] overflow-hidden text-ellipsis text-sm font-medium text-gray-100">
                                {{ $translation->title }}
                            </p>
                            <p class="text-xs text-gray-400 mt-1">
                                {{ __('A new article has been published in your favorite category!') }}
                            </p>
                        </div>
                    </div>
                @endif

            {{-- 4️⃣ إشعار مقالة جديدة من كاتب تتابعه (الجديد) --}}
            @elseif($notification->type === 'App\Notifications\NewArticleFromFollowedAuthor')
                <div class="flex items-start gap-2">
                    <div class="flex-1">
                        <p class="text-xs font-semibold text-rose-400 uppercase tracking-wider mb-0.5">
                            ✍️ {{ __('New Author Article') }}
                        </p>
                        <p class="whitespace-nowrap max-w-[280px] overflow-hidden text-ellipsis text-sm font-medium text-gray-100">
                            {{ $data['title'] ?? __('New Article') }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">
                            {{ __('By') }} <span class="text-red-400 font-semibold">{{ $data['author_name'] ?? 'Author' }}</span>
                        </p>
                    </div>
                </div>




    {{-- 6️⃣ إشعار محتوى جديد من كاتب تتابعه (مقال، كتاب، لعبة، ميديا) --}}
    @elseif($notification->type === 'App\Notifications\AuthorNewContentNotification')
        @php
            $contentType = $data['content_type'] ?? 'article';
            
            // تخصيص الأيقونة واللون حسب نوع المحتوى
            $badgeConfig = match($contentType) {
                'article' => ['icon' => '📰', 'label' => __('New Article'), 'color' => 'text-rose-400'],
                'book'    => ['icon' => '📖', 'label' => __('New Book Summary'), 'color' => 'text-amber-400'],
                'game'    => ['icon' => '🎮', 'label' => __('New Game'), 'color' => 'text-emerald-400'],
                'media'   => ['icon' => '🎥', 'label' => __('New Media Summary'), 'color' => 'text-indigo-400'],
                default   => ['icon' => '🔔', 'label' => __('New Publication'), 'color' => 'text-gray-400'],
            };
        @endphp
        <div class="flex items-start gap-2">
            <span class="text-base shrink-0">{{ $badgeConfig['icon'] }}</span>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold {{ $badgeConfig['color'] }} uppercase tracking-wider mb-0.5">
                    {{ $badgeConfig['label'] }}
                </p>
                <p class="whitespace-nowrap max-w-[280px] overflow-hidden text-ellipsis text-sm font-medium text-gray-100">
                    {{ $data['title'] ?? '' }}
                </p>
                <p class="text-xs text-gray-400 mt-1 truncate">
                    {{ __('By') }} <span class="text-red-400 font-semibold">{{ $data['author_name'] ?? 'Author' }}</span>
                </p>
            </div>
        </div>




            {{-- 5️⃣ حالة Fallback --}}
            @else
                <p class="text-sm text-gray-200">
                    {{ $data['message'] ?? __('You have a new notification') }}
                </p>
            @endif

            <p class="text-start italic text-[11px] text-gray-400 mt-2">
                {{ $notification->created_at->diffForHumans() }}
            </p>
        </a>
    @endif

</li>