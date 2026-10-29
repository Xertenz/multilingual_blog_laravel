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
        'solved_cases_count',
        'reputation_points',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
