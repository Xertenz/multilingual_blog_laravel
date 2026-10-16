<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewCommentReactionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public object $reaction;
    public object $comment;
    public object $user;

    /**
     * Create a new notification instance.
     */
    public function __construct(object $reaction)
    {
        $this->reaction = $reaction;
        $this->comment = $reaction->reactionable; // التعليق المرتبط بالتفاعل
        $this->user = $reaction->user; // المستخدم الذي قام بالتفاعل
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database']; // تخزين الإشعار في قاعدة البيانات
    }

    /**
     * Get the array representation of the notification for database storage.
     *
     * @return array<string, mixed>
     */

    /*
    public function toArray(object $notifiable): array
    {
        $comment = $this->comment;
        $article = $comment->article;

        // جلب أول ترجمة متاحة للمقال
        $translation = $article ? $article->translations()->first() : null;

        return [
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'comment_id' => $comment->id,
            'article_id' => $article ? $article->id : null,
            'article_trans_id' => $translation ? $translation->id : null,
            'article_trans_slug' => $translation ? $translation->slug : null,
            'reaction_type' => $this->reaction->type,
            'message' => "فاعل بـ ({$this->reaction->type}) على تعليقك.",
        ];
    }
        */
    public function toArray(object $notifiable): array
    {
        $comment = $this->comment;
        $article = $comment->article;
        $translation = $article ? $article->translations()->first() : null;

        // التحقق المباشر: إذا كان للتعليق parent_id فهذا يعني أنه رد فرعي
        $isReplyFlag = !empty($comment->parent_id);

        return [
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'comment_id' => $isReplyFlag ? null : $comment->id,
            'reply_id' => $isReplyFlag ? $comment->id : null,
            'article_id' => $article ? $article->id : null,
            'article_trans_id' => $translation ? $translation->id : null,
            'article_trans_slug' => $translation ? $translation->slug : null,
            'reaction_type' => $this->reaction->type,
            'message' => "فاعل بـ ({$this->reaction->type}) على " . ($isReplyFlag ? 'ردك.' : 'تعليقك.'),
        ];
    }
}
