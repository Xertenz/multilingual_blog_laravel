<?php

namespace App\Models;

use App\Enums\ContentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MediaSummaryTranslation extends Model
{
    protected $fillable = [
        'media_summary_id',
        'user_id',
        'locale',
        'title',
        'slug',
        'synopsis',
        'review_analysis',
        'meta_title',
        'meta_description',
        'status',
        'views'
    ];

    protected $casts = [
        'status' => ContentStatus::class,
    ];

    public function mediaSummary()
    {
        return $this->belongsTo(MediaSummary::class);
    }
}
