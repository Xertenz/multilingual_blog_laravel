<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommunityCaseCategoryTranslation extends Model
{
    protected $fillable = [
        'community_case_category_id',
        'locale',
        'name',
        'slug',
        'description',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(CommunityCaseCategory::class, 'community_case_category_id');
    }
}
