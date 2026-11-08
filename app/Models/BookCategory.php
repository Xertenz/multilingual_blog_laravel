<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class BookCategory extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = ['label', 'user_id'];

    public function translations(): HasMany
    {
        return $this->hasMany(BookCategoryTranslation::class);
    }

    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        // 1. حجم مصغر (للقوائم والجداول)
        $this->addMediaConversion('thumb')
            ->performOnCollections('featured_imgs')
            ->width(150)
            ->optimize()
            ->sharpen(5)
            ->quality(80)
            ->format('webp')
            ->queued(); // تعمل في الخلفية لتسريع الرفع

        // 2. حجم متوسط (للمعاينات)
        $this->addMediaConversion('small')
            ->performOnCollections('featured_imgs')
            ->width(300)
            ->optimize()
            ->sharpen(5)
            ->quality(85)
            ->format('webp')
            ->queued();

        // 3. حجم كبير (لعرض التفاصيل)
        $this->addMediaConversion('large')
            ->performOnCollections('featured_imgs')
            ->width(1200)
            ->optimize()
            ->sharpen(5)
            ->quality(85)
            ->format('webp')
            ->queued();
    }
}
