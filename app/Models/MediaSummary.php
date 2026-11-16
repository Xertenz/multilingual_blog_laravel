<?php

namespace App\Models;

use App\Enums\ContentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Image\Enums\Fit;

class MediaSummary extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $guarded = [];

    protected $casts = [
        //'type' => MediaType::class,
        'status' => ContentStatus::class,
    ];

    // علاقة الترجمات
    public function translations()
    {
        return $this->hasMany(MediaSummaryTranslation::class);
    }

    // جلب ترجمة اللغة الحالية تلقائياً
    public function translation()
    {
        return $this->hasOne(MediaSummaryTranslation::class)->where('locale', app()->getLocale());
    }

    // الربط مع الموقع الجغرافي
    public function location()
    {
        return $this->belongsTo(ArticleLocation::class, 'article_location_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(MediaSummaryCategory::class, 'media_summary_category_id');
    }
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(
            MediaSummaryTag::class,
            'media_summary_tag',
            'media_summary_id',
            'media_summary_tag_id'
        )->withTimestamps();
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

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
}
