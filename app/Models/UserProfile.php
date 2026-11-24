<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $fillable = [
        'user_id',
        'bio',
        'location',
        'website',
        'social_links',
        'solved_cases_count',
    ];

    protected $casts = [
        'social_links' => 'array',
        'solved_cases_count' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
