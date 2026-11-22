<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimelineTranslation extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function timeline(): BelongsTo
    {
        return $this->belongsTo(Timeline::class);
    }
}
