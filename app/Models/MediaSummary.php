<?php

namespace App\Models;

use App\Enums\ContentStatus;
use App\Enums\MediaType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class MediaSummary extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'type',
        'release_year',
        'imdb_rating',
        'article_location_id',
        'status',
        'views'
    ];

    protected $casts = [
        'type' => MediaType::class,
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

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
