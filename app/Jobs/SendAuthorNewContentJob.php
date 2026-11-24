<?php

namespace App\Jobs;

use App\Notifications\AuthorNewContentNotification;
use App\Notifications\CategoryNewContentNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Queue\Queueable;

class SendAuthorNewContentJob implements ShouldQueue
{
    use Queueable;

    public Model $content;
    public string $contentType;

    public function __construct(Model $content, string $contentType)
    {
        $this->content = $content;
        $this->contentType = $contentType;
    }

    public function handle(): void
    {
        // 1. تحديد الكاتب (سواء كان $this->content نموذج ترجمة أو نموذجاً أساسياً)
        $author = $this->content->author
            ?? ($this->content->article->author ?? null)
            ?? ($this->content->book->author ?? null)
            ?? ($this->content->game->author ?? null)
            ?? ($this->content->mediaSummary->author ?? null);

        if (!$author) {
            return;
        }

        $authorId = $author->id;

        // 2. تحديد الفئة الخاصة بنوع المحتوى عبر علاقة category()
        $category = match ($this->contentType) {
            'article' => $this->content->article?->category ?? $this->content->category ?? null,
            'book'    => $this->content->book?->category ?? $this->content->category ?? null,
            'game'    => $this->content->game?->category ?? $this->content->category ?? null,
            'media'   => $this->content->mediaSummary?->category ?? $this->content->category ?? null,
            default   => $this->content->category ?? null,
        };

        // 3. لغة المحتوى المنشور
        $contentLocale = $this->content->locale ?? config('app.fallback_locale', 'en');
        $fallbackLocale = config('app.fallback_locale', 'en');

        // 4. متابعو الكاتب (بنفس لغة المحتوى وبدون الكاتب نفسه)
        $authorFollowers = collect();
        if (method_exists($author, 'authorFollowers')) {
            $author->loadMissing('authorFollowers');
            $authorFollowers = $author->authorFollowers->filter(function ($user) use ($authorId, $contentLocale, $fallbackLocale) {
                if ($user->id === $authorId) {
                    return false;
                }
                $userLocale = $user->locale ?? $fallbackLocale;
                return $userLocale === $contentLocale;
            });
        }

        // 5. متابعو الفئة من جدول follows عبر علاقة followers() (بنفس لغة المحتوى وبدون الكاتب نفسه)
        $categoryFollowers = collect();
        if ($category && method_exists($category, 'followers')) {
            $category->loadMissing('followers');
            $categoryFollowers = $category->followers->filter(function ($user) use ($authorId, $contentLocale, $fallbackLocale) {
                if ($user->id === $authorId) {
                    return false;
                }
                $userLocale = $user->locale ?? $fallbackLocale;
                return $userLocale === $contentLocale;
            });
        }

        // 6. إرسال إشعار الكاتب
        foreach ($authorFollowers as $user) {
            $user->notify(new AuthorNewContentNotification($this->content, $this->contentType, $author));
        }

        // 7. إرسال إشعار الفئة لمن لم يصله إشعار الكاتب في نفس الحدث (منع التكرار)
        if ($categoryFollowers->isNotEmpty()) {
            $authorFollowerIds = $authorFollowers->pluck('id')->all();
            $pureCategoryFollowers = $categoryFollowers->reject(fn($user) => in_array($user->id, $authorFollowerIds, true));

            foreach ($pureCategoryFollowers as $user) {
                $user->notify(new CategoryNewContentNotification($this->content, $this->contentType, $category));
            }
        }
    }
}
