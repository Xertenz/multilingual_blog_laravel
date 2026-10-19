<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    //

    protected $fillable = ['user_id', 'article_id', 'parent_id', 'body', 'locale', 'mentioned_user_ids'];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id')->oldest();
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function reactions()
    {
        return $this->morphMany(Reaction::class, 'reactionable');
    }

    // هذه الدالة هي Accessor اي انها نستعدى تلقائيا عند طلب العنصر formatted_body من اي اوبجكت من Comment
    // يعني تستدعى عندما نطلب $comment->formatted_body لذلك تسمى attribute
    // الدالة يجب ان تبدأ بـ get وتنتهي بـ Attribute
    public function getFormattedBodyAttribute()
    {
        $body = e($this->body); // حماية ضد الثغرات

        return preg_replace_callback('/@([a-zA-Z0-9_]+)/', function ($matches) {
            $username = $matches[1];
            $user = User::where('name', $username)->first();

            if ($user) {
                return '<a href="' . route('profile.show', $user->id) . '" class="text-blue-500 font-semibold">@' . $username . '</a>';
            }

            return '@' . $username;
        }, $body);
    }


    public function mentionedUsers()
    {
        return $this->belongsToMany(User::class, 'comment_user_mentions', 'comment_id', 'user_id');
    }
}
