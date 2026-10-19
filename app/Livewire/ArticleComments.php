<?php

namespace App\Livewire;

use App\Models\Article;
use App\Models\ArticleTranslation;
use App\Models\Comment;
use App\Models\User;
use App\Notifications\NewCommentReactionNotification;
use App\Notifications\NewCommentReplyNotification;
use App\Notifications\UserMentionedNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class ArticleComments extends Component
{
    public int $articleId;
    public string $newCommentBody = '';

    // لإدارة النماذج المفتوحة (الردود والتعديل)
    public ?int $replyToCommentId = null;
    public string $replyBody = '';

    public ?int $editingCommentId = null;
    public string $editBody = '';

    protected $rules = [
        'newCommentBody' => 'required|string|max:1000',
    ];

    public function mount($articleId)
    {
        $this->articleId = $articleId;
    }

    // --- 1. إضافة تعليق رئيسي جديد ---
    public function addComment()
    {
        $this->validate(['newCommentBody' => 'required|string|max:1000']);

        $this->saveComment($this->newCommentBody, null);

        $this->newCommentBody = '';
    }

    // --- 2. إضافة رد على تعليق/رد فرعي ---
    public function showReplyForm($commentId)
    {
        $this->editingCommentId = null;
        $this->replyToCommentId = $commentId;
        $this->replyBody = '';
    }

    public function hideReplyForm()
    {
        $this->replyToCommentId = null;
        $this->replyBody = '';
    }

    public function addReply($parentId)
    {
        $this->validate(['replyBody' => 'required|string|max:1000']);

        $this->saveComment($this->replyBody, $parentId);

        $this->hideReplyForm();
    }

    // --- 3. تعديل تعليق/رد ---
    public function showEditForm($commentId, $currentBody)
    {
        $this->hideReplyForm();
        $this->editingCommentId = $commentId;
        $this->editBody = $currentBody;
    }

    public function hideEditForm()
    {
        $this->editingCommentId = null;
        $this->editBody = '';
    }

    public function updateComment($commentId)
    {
        $this->validate(['editBody' => 'required|string|max:1000']);

        $comment = Comment::findOrFail($commentId);

        if (Auth::id() !== $comment->user_id) {
            return;
        }

        preg_match_all('/@([a-zA-Z0-9_]+)/', $this->editBody, $matches);
        $usernames = $matches[1];
        $mentionedUserIds = User::whereIn('name', $usernames)->pluck('id')->toArray();

        $comment->update([
            'body' => trim(strip_tags($this->editBody)),
        ]);

        $comment->mentionedUsers()->sync($mentionedUserIds);

        foreach ($mentionedUserIds as $userId) {
            $user = User::find($userId);
            if ($user && $user->id !== Auth::id()) {
                $user->notify(new UserMentionedNotification($comment));
            }
        }

        $this->clearArticleCache();
        $this->hideEditForm();
    }

    // --- 4. حذف تعليق/رد ---
    public function deleteComment($commentId)
    {
        $comment = Comment::findOrFail($commentId);

        if (Auth::id() !== $comment->user_id) {
            return;
        }

        $comment->delete();
        $this->clearArticleCache();
    }

    // --- 5. التفاعلات (Reactions) ---
    public function toggleReaction($commentId, $type)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $comment = Comment::findOrFail($commentId);
        $userId = Auth::id();

        $existing = $comment->reactions()->where('user_id', $userId)->first();
        $reaction = null;
        $status = '';

        if ($existing) {
            if ($existing->type === $type) {
                $existing->delete();
                $status = 'removed';
            } else {
                $existing->update(['type' => $type]);
                $reaction = $existing;
                $status = 'updated';
            }
        } else {
            $reaction = $comment->reactions()->create([
                'user_id' => $userId,
                'type' => $type
            ]);
            $status = 'added';
        }

        if ($status !== 'removed' && $reaction && $comment->user_id !== $userId) {
            $comment->user->notify(new NewCommentReactionNotification($reaction));
        }
    }

    // دالة مساعدة لحفظ التعليق ومعالجة المنشنات والإشعارات
    private function saveComment(string $body, ?int $parentId)
    {
        preg_match_all('/@([a-zA-Z0-9_]+)/', $body, $matches);
        $usernames = $matches[1];
        $mentionedUserIds = User::whereIn('name', $usernames)->pluck('id')->toArray();

        $comment = Comment::create([
            'user_id' => Auth::id(),
            'article_id' => $this->articleId,
            'parent_id' => $parentId,
            'body' => trim(strip_tags($body)),
            'locale' => LaravelLocalization::getCurrentLocale()
        ]);

        $comment->mentionedUsers()->sync($mentionedUserIds);

        if ($comment->parent_id !== null) {
            $originalComment = Comment::find($comment->parent_id);
            $receiver = $originalComment->user;

            if ($receiver->id !== Auth::id() && !in_array($receiver->id, $mentionedUserIds)) {
                $receiver->notify(new NewCommentReplyNotification($comment, $originalComment->locale));
            }
        }

        foreach ($mentionedUserIds as $userId) {
            $user = User::find($userId);
            if ($user && $user->id !== Auth::id()) {
                $user->notify(new UserMentionedNotification($comment));
            }
        }

        $this->clearArticleCache();
    }

    private function clearArticleCache()
    {
        $article = Article::with('translations')->find($this->articleId);
        if ($article) {
            foreach ($article->translations as $translation) {
                Cache::forget("article_show_{$translation->id}_lang_{$translation->language_code}");
            }
        }
    }

    public function render()
    {
        $comments = Comment::with(['user', 'reactions', 'replies.user', 'replies.reactions'])
            ->where('article_id', $this->articleId)
            ->whereNull('parent_id')
            ->latest()
            ->get();

        return view('livewire.article-comments', compact('comments'));
    }
}
