<?php

namespace App\Notifications;

use App\Models\Article;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\App;

class NewArticleInFavoriteCategory extends Notification
{
    use Queueable;

    protected Article $article;

    /**
     * Create a new notification instance.
     */
    public function __construct(Article $article)
    {
        $this->article = $article;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        /* 2. هنا السحر! $notifiable هو المستخدم (User) الذي سيستقبل الإشعار.
           إذا كان لكل مستخدم لغة مفضلة مخزنة في الحساب (مثلاً: $notifiable->locale)، 
           يمكنك جلب الترجمة المتوافقة مع لغته. 
           أو نأخذ اللغة الحالية للموقع باستخدام app()->getLocale() كخيار افتراضي.
        */
        $locale = $notifiable->locale ?? App::getLocale();

        // جلب سطر الترجمة الخاص بهذه اللغة
        $article_trans = $this->article->translations->where('locale', $locale)->first()
            ?? $this->article->translations->first(); // fallback لأي لغة لو لم تتوفر لغته


        return [
            'article_id' => $this->article->id,
            'title' => $article_trans ? $article_trans->title : "New Article",
            'category_sign' => $this->article->category->sign,
            'message' => 'A new article has been published in your favorite category!'
        ];
    }
}
