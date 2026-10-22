<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ArticleSuggestion extends Model
{
    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function votes(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'article_suggestion_votes')->withTimestamps();
    }

    public function isVotedBy(?User $user)
    {
        if (!$user) {
            return false;
        }

        return $this->votes()->where('user_id', $user->id)->exists();
    }
}
