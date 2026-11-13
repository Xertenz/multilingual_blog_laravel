<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Image\Enums\Fit;

class Book extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $guarded = [];

    // علاقة مالك الكتاب
    public function uploader()
    {
        return $this->belongsTo(User::class);
    }

    // علاقة الترجمات
    public function translations(): HasMany
    {
        return $this->hasMany(BookTranslation::class);
    }

    // دالة مساعدة لجلب الترجمة حسب اللغة الحالية أو الافتراضية
    public function translate($locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        return $this->translations->where('locale', $locale)->first();
        //?? $this->translations->first();
    }


    /*
    public function registerMediaConversions(?Media $media = null): void
    {
        // 1. حجم مصغر جداً ( للقوائم الجانبية أو الأيقونات المصغرة )
        $this->addMediaConversion('thumb')
            ->width(100)
            ->height(150)
            ->optimize()
            ->sharpen(5)
            ->quality(80)
            ->format('webp')
            //->queued(); // لتعمل في الخلفية إذا كان نظام Queues مفعلاً (اختياري)
            ->nonQueued();

        // 2. حجم متوسط ( لعرض البطاقات في صفحة القائمة الرئيسية index )
        $this->addMediaConversion('medium')
            ->width(300)
            ->height(450)
            ->optimize()
            ->sharpen(5)
            ->quality(80)
            ->format('webp')
            //->queued(); // لتعمل في الخلفية إذا كان نظام Queues مفعلاً (اختياري)
            ->nonQueued();

        // 3. حجم كبير ( لصفحة التفاصيل show )
        $this->addMediaConversion('large')
            ->width(600)
            ->height(900)
            ->optimize()
            ->sharpen(5)
            ->quality(80)
            ->format('webp')
            //->queued(); // لتعمل في الخلفية إذا كان نظام Queues مفعلاً (اختياري)
            ->nonQueued();
    }
    */

    public function registerMediaConversions(?Media $media = null): void
    {
        // 1. الحجم المصغر (Thumb)
        $this->addMediaConversion('thumb')
            ->fit(Fit::Contain, 150, 225) // استخدام Contain يحافظ على الأبعاد الأصلية بدون تشويش
            ->quality(90)
            ->format('webp')
            ->nonQueued();

        // 2. الحجم المتوسط (Medium) - لصفحة القائمة الرئيسية index
        $this->addMediaConversion('medium')
            ->fit(Fit::Contain, 400, 600) // يحافظ على النسبة والتفاصيل الحادة
            ->quality(92)
            ->format('webp')
            ->nonQueued();

        // 3. الحجم الكبير (Large) - لصفحة التفاصيل show
        $this->addMediaConversion('large')
            ->fit(Fit::Contain, 800, 1200) // يعطي مساحة كافية لتفاصيل الغلاف على الشاشات الكبيرة
            ->quality(95)                  // جودة عالية جداً تمنع أي ضغط مرئي أو تشويش
            ->format('webp')
            ->nonQueued();
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function bookCategory(): BelongsTo
    {
        return $this->belongsTo(BookCategory::class);
    }

    public function bookTags(): BelongsToMany
    {
        return $this->belongsToMany(BookTag::class, 'book_tag', 'book_id', 'book_tag_id'); //->withTimestamps();
    }
}
