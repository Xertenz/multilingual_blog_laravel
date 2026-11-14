<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Models\ArticleTranslation;

class NewArticleFromFollowedAuthor extends Notification implements ShouldQueue
{
    use Queueable;

    public object $article_trans;
    public object $author;

    public function __construct(ArticleTranslation $article_trans)
    {
        $this->article_trans = $article_trans;
        $this->author = $article_trans->article->author; // الكاتب صاحب المقال
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'article_id' => $this->article_trans->article_id,
            'article_trans_id' => $this->article_trans->id,
            'slug' => $this->article_trans->slug,
            'title' => $this->article_trans->title,
            'author_name' => $this->author->name,
            'author_id' => $this->author->id,
            'message' => "الكاتب {$this->author->name} الذي تتابعه نشر مقالاً جديداً: {$this->article_trans->title}"
        ];
    }
}
