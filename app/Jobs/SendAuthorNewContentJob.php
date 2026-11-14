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
        // 1. تحديد الكاتب صاحب المحتوى
        $author = $this->content->author;
        if (!$author) return;

        $authorId = $author->id;


        // 2. تحديد الفئة المرتبطة بالمحتوى بناءً على نوعه
        $category = null;
        if ($this->contentType === 'article') {
            // المقال مرتبط بـ article->category عبر الترجمة أو المقال الأساسي
            $article = $this->content->article ?? null;
            $category = $article ? $article->category : null;
        } elseif ($this->contentType === 'book') {
            // الكتاب مرتبط بـ bookCategory
            $book = $this->content->book ?? null;
            $category = $book ? $book->bookCategory : null;
        } elseif ($this->contentType === 'game') {
            // الألعاب مرتبطة بـ gameCategory (عدل اسم العلاقة حسب نموذجك إن لم تكن gameCategory)
            $game = $this->content->game ?? null;
            $category = $game ? $game->gameCategory : null;
        } elseif ($this->contentType === 'media') {
            // الميديا مرتبطة بـ category أو mediaCategory حسب هيكلك
            $media = $this->content->mediaSummary ?? null;
            $category = $media ? $media->type : null;
        }

        // 3. لغة المحتوى المنشور
        $contentLocale = $this->content->locale ?? config('app.fallback_locale', 'en');
        $fallbackLocale = config('app.fallback_locale', 'en');

        // 4. جمع متابعي الكاتب (تصفية حسب اللغة واستثناء الكاتب نفسه)
        $authorFollowers = collect();
        if ($author && method_exists($author, 'authorFollowers')) {
            $author->load('authorFollowers');
            $authorFollowers = $author->authorFollowers->filter(function ($user) use ($authorId, $contentLocale, $fallbackLocale) {
                if ($user->id === $authorId) return false;
                $userLocale = $user->locale ?? $fallbackLocale;
                return $userLocale === $contentLocale;
            });
        }

        // 5. جمع متابعي الفئة (تصفية حسب اللغة واستثناء الكاتب نفسه)
        $categoryFollowers = collect();
        if ($category && method_exists($category, 'followers')) {
            $category->load('followers');
            $categoryFollowers = $category->followers->filter(function ($user) use ($authorId, $contentLocale, $fallbackLocale) {
                if ($user->id === $authorId) return false;
                $userLocale = $user->locale ?? $fallbackLocale;
                return $userLocale === $contentLocale;
            });
        }

        // 6. 🛡️ الدمج الذكي ومنع التكرار (Unique): 
        // إذا كان المستخدم يتابع الكاتب والفئة معاً، سيتم الاحتفاظ به مرة واحدة فقط
        // سنعطي الأولوية لإشعار الكاتب، أو يمكننا إرسال إشعار موحد. بما أنك ترغب بإشعار الفئة السابق، 
        // سنفصلهم أو نرسل إشعارين منفصلين لمن يستحق، مع استثناء من يستقبل إشعار الكاتب من استقبال إشعار الفئة في نفس اللحظة.

        // أ. إرسال إشعار الكاتب للمتابعين المهتمين بالكاتب
        if ($authorFollowers->isNotEmpty()) {
            foreach ($authorFollowers as $user) {
                $user->notify(new \App\Notifications\AuthorNewContentNotification($this->content, $this->contentType));
            }
        }

        // ب. إرسال إشعار الفئة للمتابعين المهتمين بالفئة، بشرط ألا يكونوا قد استلموا للتو إشعار الكاتب (منع التكرار)
        if ($categoryFollowers->isNotEmpty()) {
            // استثناء المستخدمين الذين وجدوا في قائمة متابعي الكاتب لكي لا يصلهم إشعاران لنفس العنصر
            $authorFollowerIds = $authorFollowers->pluck('id')->toArray();

            $pureCategoryFollowers = $categoryFollowers->reject(fn($user) => in_array($user->id, $authorFollowerIds));

            if ($pureCategoryFollowers->isNotEmpty()) {
                foreach ($pureCategoryFollowers as $user) {
                    // يمكنك استخدام إشعار الفئة المخصص الذي تريده، أو إشعار الفئة القديم
                    $user->notify(new \App\Notifications\CategoryNewContentNotification($this->content, $this->contentType, $category));
                }
            }
        }
    }
}
