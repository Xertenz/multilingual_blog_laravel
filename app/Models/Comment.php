<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model 
{
    //

    protected $fillable = ['user_id', 'article_id', 'parent_id', 'body', 'locale'];
    public function user():BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function article():BelongsTo {
        return $this->belongsTo(Article::class);
    }

    public function replies() {
        return $this->hasMany(Comment::class, 'parent_id')->oldest();
    }

    public function parent() {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function reactions() {
        return $this->morphMany(Reaction::class, 'reactionable');
    }
}
