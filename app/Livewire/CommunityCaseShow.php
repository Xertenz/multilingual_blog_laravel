<?php

namespace App\Livewire;

use App\Models\CommunityCase;
use App\Models\Comment;
use App\Models\User;
use App\Notifications\NewCommentReactionNotification;
use App\Notifications\NewCommentReplyNotification;
use App\Notifications\UserMentionedNotification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class CommunityCaseShow extends Component
{
    public CommunityCase $communityCase;

    public string $newCommentBody = '';
    public ?int $replyToCommentId = null;
    public string $replyBody = '';
    public ?int $editingCommentId = null;
    public string $editBody = '';

    public function mount($slug)
    {
        $this->communityCase = CommunityCase::where('slug', $slug)
            ->where('moderation_status', 'approved')
            ->with([
                'user',
                'category.translations',
                'comments.user',
                'comments.reactions',
                'comments.replies.user',
                'comments.replies.reactions',
                'comments.replies.replies'
            ])
            ->firstOrFail();

        $this->communityCase->increment('views_count');
    }

    public function addComment()
    {
        $this->validate(['newCommentBody' => 'required|string|max:1000']);
        $this->saveComment($this->newCommentBody, null);
        $this->newCommentBody = '';
    }

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

        if (method_exists($comment, 'mentionedUsers')) {
            $comment->mentionedUsers()->sync($mentionedUserIds);
        }

        foreach ($mentionedUserIds as $userId) {
            $user = User::find($userId);
            if ($user && $user->id !== Auth::id()) {
                $user->notify(new UserMentionedNotification($comment));
            }
        }

        $this->hideEditForm();
    }

    public function deleteComment($commentId)
    {
        $comment = Comment::findOrFail($commentId);

        if (Auth::id() !== $comment->user_id) {
            return;
        }

        $comment->delete();
    }

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

    // دالة حفظ التعليق مطابقة تماماً لمنطق ArticleComments لضمان توافق الحاويات والـ Polymorphic
    private function saveComment(string $body, ?int $parentId)
    {
        preg_match_all('/@([a-zA-Z0-9_]+)/', $body, $matches);
        $usernames = $matches[1];
        $mentionedUserIds = User::whereIn('name', $usernames)->pluck('id')->toArray();

        $comment = Comment::create([
            'user_id' => Auth::id(),
            'commentable_type' => CommunityCase::class,
            'commentable_id' => $this->communityCase->id,
            'parent_id' => $parentId,
            'body' => trim(strip_tags($body)),
            'locale' => LaravelLocalization::getCurrentLocale()
        ]);

        if (method_exists($comment, 'mentionedUsers')) {
            $comment->mentionedUsers()->sync($mentionedUserIds);
        }

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
    }

    public function render()
    {
        // جلب التعليقات بنفس طريقة ArticleComments تماماً لضمان انتظام الحاويات والترتيب
        $comments = Comment::with([
            'user',
            'reactions',
            'replies' => fn($q) => $q->oldest(),
            'replies.user',
            'replies.reactions',
            'replies.replies'  => fn($q) => $q->oldest()
        ])
            ->where('commentable_type', CommunityCase::class)
            ->where('commentable_id', $this->communityCase->id)
            ->whereNull('parent_id')
            //->oldest()
            ->latest()
            ->get();

        $categoryTrans = $this->communityCase->category->translations()
            ->where('locale', app()->getLocale())
            ->first() ?? $this->communityCase->category->translations()->first();

        return view('livewire.community-case-show', [
            'categoryTrans' => $categoryTrans,
            'comments' => $comments,
        ])->layout('layouts.app');
    }
}
