<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GameTag extends Model
{
    protected $guarded = [];

    public function translations(): HasMany
    {
        return $this->hasMany(GameTagTranslation::class);
    }

    public function games(): BelongsToMany
    {
        return $this->belongsToMany(Game::class, 'game_tag', 'game_tag_id', 'game_id');
    }
}
