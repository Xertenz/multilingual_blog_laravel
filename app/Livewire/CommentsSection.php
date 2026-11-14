<?php

namespace App\Livewire;

use App\Models\Comment;
use App\Models\User;
use App\Notifications\NewCommentReactionNotification;
use App\Notifications\NewCommentReplyNotification;
use App\Notifications\UserMentionedNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
//use Livewire\Attributes\Locked;

class CommentsSection extends Component
{
    public Model $model;

    public string $newCommentBody = '';
    public ?int $replyToCommentId = null;
    public string $replyBody = '';
    public ?int $editingCommentId = null;
    public string $editBody = '';

    public function mount(Model $model)
    {
        $this->model = $model;
    }

    public function addComment()
    {
        if (Auth::check() && method_exists(Auth::user(), 'isBanned') && Auth::user()->isBanned()) {
            $penalty = Auth::user()->activePenalty;
            session()->flash('error', "Your account is temporarily banned: {$penalty->reason}");
            return;
        }

        $this->validate(['newCommentBody' => 'required|string|max:1000']);
        $this->saveComment($this->newCommentBody, null);
        $this->newCommentBody = '';
    }

    public function showReplyForm(int $commentId)
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

    public function addReply(int $parentId)
    {
        $this->validate(['replyBody' => 'required|string|max:1000']);
        $this->saveComment($this->replyBody, $parentId);
        $this->hideReplyForm();
    }

    public function showEditForm(int $commentId, string $currentBody)
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

    public function updateComment(int $commentId)
    {
        $this->validate(['editBody' => 'required|string|max:1000']);
        $comment = Comment::findOrFail($commentId);

        if (Auth::id() !== $comment->user_id) return;

        preg_match_all('/@([a-zA-Z0-9_]+)/', $this->editBody, $matches);
        $usernames = $matches[1];
        $mentionedUserIds = User::whereIn('name', $usernames)->pluck('id')->toArray();

        $comment->update(['body' => trim(strip_tags($this->editBody))]);

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

    public function deleteComment(int $commentId)
    {
        $comment = Comment::findOrFail($commentId);
        if (Auth::id() === $comment->user_id) {
            $comment->delete();
        }
    }

    public function toggleReaction(int $commentId, string $type)
    {
        if (!Auth::check()) return redirect()->route('login');

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
            $reaction = $comment->reactions()->create(['user_id' => $userId, 'type' => $type]);
            $status = 'added';
        }

        if ($status !== 'removed' && $reaction && $comment->user_id !== $userId) {
            $comment->author->notify(new NewCommentReactionNotification($reaction));
        }
    }

    private function saveComment(string $body, ?int $parentId)
    {
        preg_match_all('/@([a-zA-Z0-9_]+)/', $body, $matches);
        $usernames = $matches[1];
        $mentionedUserIds = User::whereIn('name', $usernames)->pluck('id')->toArray();

        // استخدام العلاقة البوليمورفية بشكل آلي ونظيف
        $comment = $this->model->comments()->create([
            'user_id' => Auth::id(),
            'parent_id' => $parentId,
            'body' => trim(strip_tags($body)),
            'locale' => LaravelLocalization::getCurrentLocale()
        ]);

        if (method_exists($comment, 'mentionedUsers')) {
            $comment->mentionedUsers()->sync($mentionedUserIds);
        }

        if ($comment->parent_id !== null) {
            $originalComment = Comment::find($comment->parent_id);
            $receiver = $originalComment->author ?? null;

            if ($receiver && $receiver->id !== Auth::id() && !in_array($receiver->id, $mentionedUserIds)) {
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
        $comments = $this->model->comments()
            ->with([
                'author',
                'reactions',
                'replies' => fn($q) => $q->oldest(),
                'replies.author',
                'replies.reactions',
                'replies.replies' => fn($q) => $q->oldest()
            ])
            ->whereNull('parent_id')
            ->latest()
            ->get();

        return view('livewire.comments-section', compact('comments'));
    }
}
