<?php

namespace App\Notifications;

use App\Models\ArticleTranslation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class UserMentionedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public object $comment;

    public function __construct(object $comment)
    {
        $this->comment = $comment;
    }

    // طريقة التوصيل (نخزنها في قاعدة البيانات Database لتظهر في لوحة الإشعارات للمستخدم)
    public function via($notifiable): array
    {
        return ['database'];
    }

    // البيانات التي ستخزن في حقل الـ data في جدول notifications
    /*
    public function toArray($notifiable): array
    {
        // التحقق: هل التعليق يحتوي على parent_id (أي أنه رد فرعي) أم تعليق رئيسي؟
        $isReply = $this->comment->parent_id !== null;

        // تحديد الـ Anchor الصحيح بناءً على نوع العنصر
        $anchor = $isReply ? '#reply-item-' . $this->comment->id : '#comment-' . $this->comment->id;

        return [
            'comment_id' => $this->comment->id,
            'article_id' => $this->comment->article_id,
            'user_name' => $this->comment->user->name,
            'message' => 'أشار إليك ' . $this->comment->user->name . ' في ' . ($isReply ? 'رد' : 'تعليق') . '.',
            'url' => route('articles.show', [
                'slug' => $this->comment->article->slug ?? 'article',
                'id' => $this->comment->article_id
            ]) . $anchor,
        ];
    }
    */

    // نخزن فقط البيانات الأساسية النظيفة
    public function toArray($notifiable): array
    {
        $isReply = $this->comment->parent_id !== null;

        return [
            'comment_id' => $this->comment->id,
            'article_id' => $this->comment->article_id,
            'user_name'  => $this->comment->user->name,
            'sender_locale' => $this->comment->locale, // 💡 تخزين لغة المُرسِل (لغة التعليق) هنا
            'message'    => 'أشار إليك ' . $this->comment->user->name . ' في ' . ($isReply ? 'رد' : 'تعليق') . '.',
        ];
    }
}
