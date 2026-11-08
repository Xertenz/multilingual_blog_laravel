<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookTagTranslation extends Model
{
    protected $guarded = [];

    public function bookTag(): BelongsTo
    {
        return $this->belongsTo(BookTag::class);
    }
}
