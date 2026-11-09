<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class GameCategory extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $guarded = [];

    public function translations(): HasMany
    {
        return $this->hasMany(GameCategoryTranslation::class);
    }

    public function games(): HasMany
    {
        return $this->hasMany(Game::class);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(150)
            ->height(150)
            ->optimize()
            ->format('webp');

        $this->addMediaConversion('medium')
            ->width(400)
            ->height(400)
            ->optimize()
            ->format('webp');
    }
}
