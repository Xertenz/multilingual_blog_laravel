<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Vote extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'votable_type',
        'votable_id',
        'vote',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /*
    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }
    */

    public function votable(): MorphTo
    {
        return $this->morphTo();
    }
}
