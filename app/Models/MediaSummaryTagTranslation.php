<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MediaSummaryTagTranslation extends Model
{
    protected $guarded = [];

    public function tag(): BelongsTo
    {
        return $this->belongsTo(MediaSummaryTag::class, 'media_summary_tag_id');
    }
}
