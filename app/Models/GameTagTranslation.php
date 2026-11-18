<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GameTagTranslation extends Model
{
    protected $guarded = [];

    public function tag(): BelongsTo
    {
        return $this->belongsTo(GameTag::class, 'game_tag_id');
    }
}
