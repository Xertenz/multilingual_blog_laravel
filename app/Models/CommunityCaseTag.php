<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CommunityCaseTag extends Model
{
    protected $guarded = [];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(CommunityCaseTagTranslation::class, 'community_case_tag_id');
    }

    public function communityCases(): BelongsToMany
    {
        return $this->belongsToMany(
            CommunityCase::class,
            'community_case_tag',
            'community_case_tag_id',
            'community_case_id'
        )->withTimestamps();
    }
}
