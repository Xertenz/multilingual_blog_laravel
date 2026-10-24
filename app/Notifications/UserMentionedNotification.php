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


    /*
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
    */

    public function toArray($notifiable): array
    {
        $comment = $this->comment;
        $commentable = $comment->commentable ?? null;
        $isReply = $comment->parent_id !== null;

        $data = [
            'comment_id' => $comment->id,
            'user_name'  => $comment->user->name,
            'sender_locale' => $comment->locale,
            'message'    => 'أشار إليك ' . $comment->user->name . ' في ' . ($isReply ? 'رد' : 'تعليق') . '.',
        ];

        // إذا كانت الإشارة داخل مقال (Article)
        if ($commentable instanceof \App\Models\Article) {
            $translation = $commentable->translations()->first();
            $data['article_id'] = $commentable->id;
            $data['article_trans_id'] = $translation ? $translation->id : null;
            $data['article_trans_slug'] = $translation ? $translation->slug : null;
        }
        // إذا كانت الإشارة داخل قضية في لوحة المحققين (CommunityCase)
        elseif ($commentable instanceof \App\Models\CommunityCase) {
            $data['community_case_slug'] = $commentable->slug;
        }

        return $data;
    }
}
