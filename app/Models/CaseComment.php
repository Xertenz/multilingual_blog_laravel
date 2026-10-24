<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CaseComment extends Model
{
    protected $fillable = [
        'user_id',
        'community_case_id',
        'parent_id',
        'body',
        'locale'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function communityCase(): BelongsTo
    {
        return $this->belongsTo(CommunityCase::class);
    }

    public function replies()
    {
        return $this->hasMany(CaseComment::class, 'parent_id')->oldest();
    }

    public function parent()
    {
        return $this->belongsTo(CaseComment::class, 'parent_id');
    }

    // نفس ميزة تنسيق الإشارات (@Username) التي بنيتها سابقاً
    public function getFormattedBodyAttribute()
    {
        $body = e($this->body);

        return preg_replace_callback('/@([a-zA-Z0-9_]+)/', function ($matches) {
            $username = $matches[1];
            $user = User::where('name', $username)->first();

            if ($user) {
                return '<a href="' . route('profile.show', $user->id) . '" class="text-red-500 font-semibold">@' . $username . '</a>';
            }

            return '@' . $username;
        }, $body);
    }
}
