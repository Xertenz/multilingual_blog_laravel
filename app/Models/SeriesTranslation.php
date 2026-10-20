<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeriesTranslation extends Model
{
    protected $table = 'series_translations';

    protected $fillable = [
        'series_id',
        'locale',
        'title',
        'description',
        'slug',
    ];

    public function series(): BelongsTo
    {
        return $this->belongsTo(Series::class);
    }
}
