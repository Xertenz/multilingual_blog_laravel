<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Book extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $guarded = [];

    // علاقة الترجمات
    public function translations(): HasMany
    {
        return $this->hasMany(BookTranslation::class);
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
        // 1. حجم مصغر جداً ( للقوائم الجانبية أو الأيقونات المصغرة )
        $this->addMediaConversion('thumb')
            ->width(100)
            ->height(150)
            ->sharpen(10)
            ->queued(); // لتعمل في الخلفية إذا كان نظام Queues مفعلاً (اختياري)

        // 2. حجم متوسط ( لعرض البطاقات في صفحة القائمة الرئيسية index )
        $this->addMediaConversion('medium')
            ->width(300)
            ->height(450)
            ->sharpen(10);

        // 3. حجم كبير ( لصفحة التفاصيل show )
        $this->addMediaConversion('large')
            ->width(600)
            ->height(900);
    }
}
