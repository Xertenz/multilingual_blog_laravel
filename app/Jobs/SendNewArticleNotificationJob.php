<?php

namespace App\Jobs;

use App\Models\Article;
use App\Models\ArticleTranslation;
use App\Notifications\NewArticleInFavoriteCategory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Notification;

class SendNewArticleNotificationJob implements ShouldQueue
{
    use Queueable;

    public object $article_trans;

    public function __construct(ArticleTranslation $article_trans)
    {
        $this->article_trans = $article_trans;
    }

    public function handle(): void
    {
        $article = $this->article_trans->article;
        $article->load('category.followers');
        $usersToNotify = $article->category->followers;

        // 2. تصفية المستخدمين بحيث يتم إرسال الإشعار فقط لمن تتطابق لغته مع لغة هذه الترجمة
        $targetLocale = $this->article_trans->locale;

        $filteredUsers = $usersToNotify->filter(function ($user) use ($targetLocale) {
            return $user->locale === $targetLocale;
        });

        // 3. إرسال الإشعار للمستخدمين المطابقين للغة فقط
        if ($filteredUsers->isNotEmpty()) {
            foreach ($filteredUsers as $user) {
                $user->notify(new NewArticleInFavoriteCategory($article));
            }
        }
    }
}
