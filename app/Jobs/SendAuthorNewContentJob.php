<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Database\Eloquent\Model;

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
        $author = $this->content->user ?? $this->content->author;
        if (!$author) return;

        $authorId = $author->id;

        // جلب متابعي الكاتب
        if (method_exists($author, 'authorFollowers')) {
            $author->load('authorFollowers');
            $authorFollowers = $author->authorFollowers ?? collect();
        } else {
            $authorFollowers = collect();
        }

        // تحديد لغة المحتوى المنشور (مثلاً المقال المنشور باللغة الألمانية سيكون لها locale معين)
        // إذا لم يكن النموذج يمتلك حقل locale مباشر، نلجأ للغة الافتراضية
        $contentLocale = $this->content->locale ?? app()->getLocale();

        // تصفية المستخدمين: استثناء الكاتب + مطابقة لغة المستخدم مع لغة المحتوى
        $filteredUsers = $authorFollowers->filter(function ($user) use ($authorId, $contentLocale) {
            // 🛑 1. استثناء الكاتب نفسه
            if ($user->id === $authorId) {
                return false;
            }

            // 🛑 2. مطابقة اللغة (لغة حساب المستخدم يجب أن تتطابق مع لغة المحتوى المنشور)
            $userLocale = $user->locale ?? app()->getLocale();
            return $userLocale === $contentLocale;
        });

        if ($filteredUsers->isNotEmpty()) {
            foreach ($filteredUsers as $user) {
                $user->notify(new \App\Notifications\AuthorNewContentNotification($this->content, $this->contentType));
            }
        }
    }
}
