<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notification;

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

        $recipientLocale = $notifiable->locale ?? ($this->content->locale ?? app()->getLocale());
        $categoryTrans = null;

        if ($this->category && method_exists($this->category, 'translations')) {
            $this->category->loadMissing('translations');
            $categoryTrans = $this->category->translations->firstWhere('locale', $recipientLocale)
                ?? $this->category->translations->firstWhere('locale', $this->content->locale ?? '')
                ?? $this->category->translations->first();
        }

        $categoryName = $categoryTrans?->name
            ?? $this->category?->label
            ?? 'Category';

        $message = match ($this->contentType) {
            'article' => "A new article has been published in category [{$categoryName}]: {$title}",
            'book'    => "A new book summary has been published in category [{$categoryName}]: {$title}",
            'game'    => "A new game has been added to category [{$categoryName}]: {$title}",
            'media'   => "A new media summary has been published in category [{$categoryName}]: {$title}",
            default   => "New content published in category [{$categoryName}]: {$title}",
        };

        return [
            'content_type'  => $this->contentType,
            'content_id'    => $this->content->id,
            'category_id'   => $this->category?->id,
            'category_name' => $categoryName,
            'title'         => $title,
            'message'       => $message,
        ];
    }
}
