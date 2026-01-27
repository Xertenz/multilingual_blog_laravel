<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Article extends Model  implements HasMedia
{
    use InteractsWithMedia;
    protected $fillable = ['category_id', 'featured_img', 'views', 'status'];
    public $timestamps = false;
    //
    public function translations(): HasMany
    {
        return $this->hasMany(ArticleTranslation::class);
    }

    public function registerMediaConversions(Media $media = null): void
    {
            /*
        // 1. التحويلات العادية
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(300)
            ->sharpen(10);
        
        $this->addMediaConversion('medium')
            ->width(600)
            ->height(null) // الحفاظ على النسبة
            ->sharpen(10);
        
        // 2. ⚠️ **هذا هو الأهم للـ Responsive Images!**
        $this->addMediaConversion('responsive')
            ->withResponsiveImages();  // 👈 هذا السطر
        
            */
        // 3. أو بهذه الطريقة لكل conversion
        $this->addMediaConversion('responsive')
            ->width(1200)
            ->withResponsiveImages()
            ->nonQueued(); // إذا أردت معالجة فورية
    }
    

}
