<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CommunityCaseCategory extends Model
{
    protected $guarded = [];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(CommunityCaseCategoryTranslation::class, 'community_case_category_id');
    }

    public function communityCases(): HasMany
    {
        return $this->hasMany(CommunityCase::class, 'community_case_category_id');
    }
}
