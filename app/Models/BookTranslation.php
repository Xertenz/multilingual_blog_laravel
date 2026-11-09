<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class BookTranslation extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(100)
            ->height(150)
            ->optimize()
            ->sharpen(5)
            ->quality(80)
            ->format('webp')
            ->queued(); // لتعمل في الخلفية إذا كان نظام Queues مفعلاً (اختياري)




        $this->addMediaConversion('medium')
            ->width(300)
            ->height(450)
            ->optimize()
            ->sharpen(5)
            ->quality(80)
            ->format('webp')
            ->queued(); // لتعمل في الخلفية إذا كان نظام Queues مفعلاً (اختياري)

        $this->addMediaConversion('large')
            ->width(600)
            ->height(900)
            ->optimize()
            ->sharpen(5)
            ->quality(80)
            ->format('webp')
            ->queued(); // لتعمل في الخلفية إذا كان نظام Queues مفعلاً (اختياري)
    }
}
