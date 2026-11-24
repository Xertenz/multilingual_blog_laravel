<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notification;

class AuthorNewContentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Model $content;
    public string $contentType;
    public ?object $author;

    public function __construct(Model $content, string $contentType, ?object $author = null)
    {
        $this->content = $content;
        $this->contentType = $contentType;
        $this->author = $author
            ?? $content->author
            ?? ($content->article->author ?? null)
            ?? ($content->book->author ?? null)
            ?? ($content->game->author ?? null)
            ?? ($content->mediaSummary->author ?? null);
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $title = $this->content->title ?? 'New Publication';
        $authorName = $this->author?->name ?? 'Author';
        $authorId = $this->author?->id;

        $message = match ($this->contentType) {
            'article' => "The author {$authorName} published a new article: {$title}",
            'book'    => "The author {$authorName} published a new book summary: {$title}",
            'game'    => "The author {$authorName} published a new game summary: {$title}",
            'media'   => "The author {$authorName} published a new media summary: {$title}",
            default   => "The author {$authorName} published a new content: {$title}",
        };

        return [
            'content_type' => $this->contentType,
            'content_id'   => $this->content->id,
            'author_id'    => $authorId,
            'author_name'  => $authorName,
            'title'        => $title,
            'message'      => $message,
        ];
    }
}
