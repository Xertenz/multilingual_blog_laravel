<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class MediaSummaryTag extends Model
{
    protected $guarded = [];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(MediaSummaryTagTranslation::class, 'media_summary_tag_id');
    }

    public function translate(string $locale)
    {
        return $this->translations->firstWhere('locale', $locale);
    }

    public function mediaSummaries(): BelongsToMany
    {
        return $this->belongsToMany(
            MediaSummary::class,
            'media_summary_tag',
            'media_summary_tag_id',
            'media_summary_id'
        )->withTimestamps();
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
