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

class Game extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function gameCategory(): BelongsTo
    {
        return $this->belongsTo(GameCategory::class);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(GameTranslation::class);
    }

    public function gameTags(): BelongsToMany
    {
        return $this->belongsToMany(GameTag::class, 'game_tag', 'game_id', 'game_tag_id');
    }

    // دالة مساعدة لجلب الترجمة حسب اللغة الحالية أو الافتراضية
    public function translate($locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        return $this->translations->where('locale', $locale)->first()
            ?? $this->translations->first();
    }

    /*
    public function registerMediaConversions(?Media $media = null): void
    {
        // 1. أحجام لصور الغلاف (Cover Images)
        $this->addMediaConversion('thumb')
            ->width(100)
            ->height(150)
            ->sharpen(10);

        $this->addMediaConversion('medium')
            ->width(300)
            ->height(450)
            ->sharpen(10);

        $this->addMediaConversion('large')
            ->width(600)
            ->height(900)
            ->sharpen(10);
    }
    */

    public function registerMediaConversions(?Media $media = null): void
    {
        // 1. الحجم المصغر (Thumb)
        $this->addMediaConversion('thumb')
            //->fit(Fit::Contain, 100, 150) // استخدام Contain يحافظ على الأبعاد الأصلية بدون تشويش
            ->fit(Fit::Contain, 150, 225)
            ->quality(90)
            ->format('webp')
            ->nonQueued();

        // 2. الحجم المتوسط (Medium) - لصفحة القائمة الرئيسية index
        $this->addMediaConversion('medium')
            //->fit(Fit::Contain, 300, 450) // يحافظ على النسبة والتفاصيل الحادة
            ->fit(Fit::Contain, 400, 600)
            ->quality(92)
            ->format('webp')
            ->nonQueued();

        // 3. الحجم الكبير (Large) - لصفحة التفاصيل show
        $this->addMediaConversion('large')
            //->fit(Fit::Contain, 600, 900) // يعطي مساحة كافية لتفاصيل الغلاف على الشاشات الكبيرة
            ->fit(Fit::Contain, 800, 1200)
            ->quality(95)                  // جودة عالية جداً تمنع أي ضغط مرئي أو تشويش
            ->format('webp')
            ->nonQueued();
    }

    // ربط التعليقات بنظام الـ Polymorphic Comments الموحد في المنصة
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
