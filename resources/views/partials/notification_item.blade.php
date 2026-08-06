@php
    $data = $notification->data;
@endphp

<li class="notifications-list-item {{$notification->unread() ? 'bg-gray-600' : 'bg-gray-800'}} border-b border-gray-700/50 hover:bg-gray-700/60 transition-colors">
    <a href="{{ route('notifications.read', ['id' => $notification->id]) }}" class="block p-3">
        
        {{-- 1️⃣ الحالة الأولى: إشعار الرد على تعليق --}}
        @if($notification->type === 'App\Notifications\NewCommentReply')
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

        {{-- 2️⃣ الحالة الثانية: إشعار مقالة جديدة من الفئة المفضلة --}}
        @elseif($notification->type === 'App\Notifications\NewArticleInFavoriteCategory')
            @php
                // جلب المقالة مع ترجماتها لضمان الأداء الأفضل
                $article = \App\Models\Article::with('translations')->find($data['article_id'] ?? null);
                
                if ($article) {
                    // جلب الترجمة للغة الحالية للمستخدم أو أول ترجمة متاحة
                    $currentLocale = app()->getLocale();
                    $translation = $article->translations->where('language_code', $currentLocale)->first() 
                                ?? $article->translations->first();
                }
            @endphp
            @if($article && isset($translation))
                <div class="flex items-start gap-2">
                    <div class="flex-1">
                        <p class="text-xs font-semibold text-purple-400 uppercase tracking-wider mb-0.5">
                            📁 {{ $data['category_sign'] ?? __('New Article') }}
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

        {{-- 3️⃣ حالة fallback عامة لأي إشعار مستقبلي قد تضيفه --}}
        @else
            <p class="text-sm text-gray-200">
                {{ $data['message'] ?? __('You have a new notification') }}
            </p>
        @endif

        {{-- الوقت يظهر في الأسفل دائماً لجميع أنواع الإشعارات --}}
        <p class="text-start italic text-[11px] text-gray-400 mt-2">
            {{ $notification->created_at->diffForHumans() }}
        </p>
    </a>
</li>