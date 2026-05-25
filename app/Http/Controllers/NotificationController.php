<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\DatabaseNotification;


class NotificationController extends Controller
{
    public function fetch()
    {
        $user_id = Auth::id();
        $userNotifications = DatabaseNotification::where('notifiable_type', User::class)
            ->where('notifiable_id', $user_id)
            ->latest()
            ->paginate(10);
        $html = '';
        foreach ($userNotifications as $userNotification) {
            $html .= view('partials.notification_item', ['notification' => $userNotification])->render();
        }
        //if ($request->ajax()) {
        return response()->json([
            'html' => $html,
            'hasMorePages' => $userNotifications->hasMorePages(),
        ]);
        //}
    }

    public function read(string $notification_id)
    {
        $user_id = Auth::id();
        $notification = DatabaseNotification::where('id', $notification_id)
            ->where('notifiable_type', User::class)
            ->where('notifiable_id', $user_id)
            ->first();

        if (!$notification) {
            //Do Something here
        }

        if ($notification->unread()) {
            $notification->markAsRead();
        }

        $data = $notification->data;
        $targetUrl = route('articles.show', [
            'id'   => $data['article_trans_id'],
            'slug' => $data['article_trans_slug']
        ]) . '#reply-item-' . $data['reply_id'];

        return redirect($targetUrl);
    }
}
