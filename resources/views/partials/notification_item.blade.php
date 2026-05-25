@php
    $data = $notification->data;
@endphp

<li class="notifications-list-item {{$notification->unread() ? 'bg-gray-600' : 'bg-gray-800'}}">
    <a href="{{ route('notifications.read', ['id' => $notification->id]) }}" class="block p-3">
        <p class="whitespace-nowrap max-w-[280px] overflow-hidden text-ellipsis">
            <span class="font-bold">{{$data['reply_author_name']}}</span>
            <span>replied to your comment:</span>
        </p>
        <p class=" whitespace-nowrap max-w-[280px] overflow-hidden text-ellipsis">
            {{$data['comment_body']}}
        </p>
        <p class="text-start italic">{{$notification->created_at->diffForHumans()}}</p>
    </a>
</li>
