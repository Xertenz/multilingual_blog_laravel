<?php

namespace App\Jobs;

use App\Models\Article;
use App\Notifications\NewArticleInFavoriteCategory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Notification;

class SendNewArticleNotificationJob implements ShouldQueue
{
    use Queueable;

    public object $article;

    public function __construct(Article $article)
    {
        $this->article = $article;
    }

    public function handle(): void
    {
        $this->article->load(['category.followers', 'translations']);
        $usersToNotify = $this->article->category->followers;

        if ($usersToNotify->isNotEmpty()) {
            Notification::send($usersToNotify, new NewArticleInFavoriteCategory($this->article));
        }
    }
}
