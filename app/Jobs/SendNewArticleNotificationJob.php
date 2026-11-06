<?php

/*
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

*/

namespace App\Jobs;

use App\Models\ArticleTranslation;
use App\Notifications\NewArticleInFavoriteCategory;
use App\Notifications\NewArticleFromFollowedAuthor;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

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

        // جلب معرف الكاتب صاحب المقال لتجنب إرسال إشعار له
        //$authorId = $article->user_id ?? $this->article_trans->user_id;
        $authorId = $this->article_trans->user_id ?? $this->article_trans->user_id;

        // تحميل المتابعين للفئة ومتابعي الكاتب
        $article->load(['category.followers', 'user.authorFollowers']);

        $categoryFollowers = $article->category ? $article->category->followers : collect();
        $authorFollowers = $article->user && method_exists($article->user, 'authorFollowers')
            ? $article->user->authorFollowers
            : collect();

        // دمج المجموعتين واستثناء التكرار
        $allUsersToNotify = $categoryFollowers->merge($authorFollowers)->unique('id');

        $targetLocale = $this->article_trans->locale;

        // تصفية المستخدمين: مطابقة اللغة + استثناء الكاتب نفسه 
        $filteredUsers = $allUsersToNotify->filter(function ($user) use ($targetLocale, $authorId) {
            // 🛑 شرط منع إرسال الإشعار للكاتب نفسه
            if ($user->id === $authorId) {
                return false;
            }

            return ($user->locale ?? app()->getLocale()) === $targetLocale;
        });

        // إرسال الإشعارات للمستحقين فقط
        if ($filteredUsers->isNotEmpty()) {
            foreach ($filteredUsers as $user) {
                if ($authorFollowers->contains('id', $user->id)) {
                    $user->notify(new \App\Notifications\NewArticleFromFollowedAuthor($this->article_trans));
                } elseif ($categoryFollowers->contains('id', $user->id)) {
                    $user->notify(new \App\Notifications\NewArticleInFavoriteCategory($article));
                }
            }
        }
    }
}
