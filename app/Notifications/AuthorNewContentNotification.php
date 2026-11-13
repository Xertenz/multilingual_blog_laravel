<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

class AuthorNewContentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Model $content;
    public string $contentType;
    public object $author;

    public function __construct(Model $content, string $contentType)
    {
        $this->content = $content;
        $this->contentType = $contentType;
        $this->author = $content->user ?? $content->author;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        // استخراج العنوان بناءً على نموذج المحتوى
        $title = $this->content->title
            //?? optional($this->content->translations?->first())->title
            ?? 'New Publication';

        // تخصيص رسالة الإشعار بناءً على النوع (Type)
        $message = match ($this->contentType) {
            'article' => "The author {$this->author->name} published a new article: {$title}",
            'book'    => "The author {$this->author->name} published a new book summary: {$title}",
            'game'    => "The author {$this->author->name} published a new game summary: {$title}",
            'media'   => "The author {$this->author->name} published a new media summary: {$title}",
            default   => "The author {$this->author->name} published a new content: {$title}",
        };

        return [
            'content_type' => $this->contentType, // نوع المحتوى مخزن بوضوح في الـ JSON
            'content_id'   => $this->content->id,
            'author_id'    => $this->author->id,
            'author_name'  => $this->author->name,
            'title'        => $title,
            'message'      => $message,
        ];
    }
}
