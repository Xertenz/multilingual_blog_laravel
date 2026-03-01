<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Category extends Model implements HasMedia
{
    //
    use InteractsWithMedia;
    protected $fillable = ['sign'];

    //public $timestamps = false;

    public function translations(): HasMany
    {
        return $this->hasMany(CategoryTranslation::class);
    }
    
    public function articles():HasMany {
        return $this->hasMany(Article::class);
    }

    public function registerMediaConversions(Media $media = null): void
    {
        // 1. thumbnail صغير (للقوائم)
        $this->addMediaConversion('thumb')->performOnCollections('featured_imgs')->width(150)->optimize() ->sharpen(5)->quality(80)->format('webp')->nonQueued();
        // 2. صغير (للمعاينات)
        $this->addMediaConversion('small')->performOnCollections('featured_imgs')->width(300)->optimize() ->sharpen(5)->quality(85)->format('webp')->nonQueued();
        // 3. متوسط (للصفحة الرئيسية)
        $this->addMediaConversion('medium')->performOnCollections('featured_imgs')->width(600)->optimize() ->sharpen(5)->quality(85)->format('webp')->nonQueued();
        // 4. كبير (لصفحة المقال الرئيسية)
        $this->addMediaConversion('large')->performOnCollections('featured_imgs')->width(1200)->optimize() ->sharpen(5)->quality(85)->format('webp')->nonQueued();
    }

}
