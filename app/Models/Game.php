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

    // ربط التعليقات بنظام الـ Polymorphic Comments الموحد في المنصة
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
