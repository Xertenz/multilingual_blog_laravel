<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class NewCommentReplyNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public object $reply_item;
    public string $original_comment_locale;
    public function __construct(object $reply_item, string $original_comment_locale)
    {
        $this->reply_item = $reply_item;
        $this->original_comment_locale = $original_comment_locale;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    /*
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }
    */

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    /*
    public function toArray(object $notifiable): array
    {
        //$article_trans = $this->reply_item->article->translations()->where('locale', LaravelLocalization::getCurrentLocale())->first();
        $article_trans = $this->reply_item->article->translations()->where('locale', $this->original_comment_locale)->first();
        if (!$article_trans) {
            $article_trans = $this->reply_item->article->translations()->first();
        }
        return [
            'reply_author_name' => Auth::user()->name,
            'comment_body' => $this->reply_item->body,
            'reply_id' => $this->reply_item->id,
            //'target_url' => route('articles.show', $article_trans->slug) . '#reply-item-' . $this->reply_item->id,
            'article_trans_id' => $article_trans->id,
            'article_trans_slug' => $article_trans->slug,
            'message' => 'replied on your comment in this article'
        ];
    }
        */
    public function toArray(object $notifiable): array
    {
        return [
            'reply_author_name' => Auth::user()->name,
            'comment_body'      => $this->reply_item->body,
            'reply_id'          => $this->reply_item->id,
            'sender_locale'     => $this->original_comment_locale, // لغة المرسل لتحديد الترجمة بذكاء
            'message'           => 'replied on your comment in this article'
        ];
    }
}
