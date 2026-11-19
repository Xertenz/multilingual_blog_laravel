<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class MediaSummaryCategory extends Model implements HasMedia
{
    use InteractsWithMedia;
    protected $guarded = [];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(MediaSummaryCategoryTranslation::class, 'media_summary_category_id');
    }

    public function mediaSummaries(): HasMany
    {
        return $this->hasMany(MediaSummary::class, 'media_summary_category_id');
    }

    public function followers(): MorphToMany
    {
        return $this->morphToMany(
            User::class,
            'followable',
            'follows',
            'followable_id',
            'user_id',
            'id',
            'id'
        )->withTimestamps();
    }
}
