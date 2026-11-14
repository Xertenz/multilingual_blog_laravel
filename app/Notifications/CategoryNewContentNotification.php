<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

class CategoryNewContentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Model $content;
    public string $contentType;
    public ?Model $category;

    public function __construct(Model $content, string $contentType, ?Model $category = null)
    {
        $this->content = $content;
        $this->contentType = $contentType;
        $this->category = $category;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $title = $this->content->title ?? 'New Publication';
        $categoryName = $this->category->label ??
            /* optional($this->category?->translations?->first())->name ?? */
            'Category';

        $message = match ($this->contentType) {
            'article' => "A new article has been published in category [{$categoryName}]: {$title}",
            'book'    => "A new book summary has been published in category [{$categoryName}]: {$title}",
            'game'    => "A new game has been added to category [{$categoryName}]: {$title}",
            'media'   => "A new media summary has been published in category [{$categoryName}]: {$title}",
            default   => "New content published in category [{$categoryName}]: {$title}",
        };

        return [
            'content_type' => $this->contentType,
            'content_id'   => $this->content->id,
            'category_id'  => $this->category?->id,
            'category_name' => $categoryName,
            'title'        => $title,
            'message'      => $message,
        ];
    }
}
