<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CommunityCaseCategory extends Model
{
    protected $fillable = ['is_active', 'label'];

    public function translations(): HasMany
    {
        return $this->hasMany(CommunityCaseCategoryTranslation::class, 'community_case_category_id');
    }

    public function cases(): HasMany
    {
        return $this->hasMany(CommunityCase::class, 'community_case_category_id');
    }
}
