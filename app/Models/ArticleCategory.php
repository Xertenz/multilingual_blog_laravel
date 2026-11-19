<?php

namespace App\Models;

use App\Models\Concerns\Followable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ArticleCategory extends Model implements HasMedia
{
    //
    use InteractsWithMedia, HasFactory;
    //protected $fillable = ['label', 'user_id'];
    protected $guarded = [];

    //public $timestamps = false;

    public function translations(): HasMany
    {
        return $this->hasMany(ArticleCategoryTranslation::class, 'article_category_id');
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class, 'article_category_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /*
    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'article_category_user')->withTimestamps();
    }
    */



    public function followers(): MorphToMany
    {
        return $this->morphToMany(
            User::class,        // الموديل المتابِع
            'followable',       // اسم العلاقة
            'follows',          // جدول الربط
            'followable_id',    // مفتاح الكيان الحالي في الجدول الوسيط
            'user_id',          // مفتاح المستخدم
            'id',               // مفتاح الكيان الأساسي
            'id'                // مفتاح المستخدم الأساسي
        )->withTimestamps();
    }


    public function registerMediaConversions(?Media $media = null): void
    {
        // 1. thumbnail صغير (للقوائم)
        //$this->addMediaConversion('thumb')->performOnCollections('featured_imgs')->width(150)->optimize()->sharpen(5)->quality(80)->format('webp')->nonQueued();
        $this->addMediaConversion('thumb')->performOnCollections('featured_imgs')->width(150)->optimize()->sharpen(5)->quality(80)->format('webp')->queued();
        // 2. صغير (للمعاينات)
        //$this->addMediaConversion('small')->performOnCollections('featured_imgs')->width(300)->optimize()->sharpen(5)->quality(85)->format('webp')->nonQueued();
        $this->addMediaConversion('small')->performOnCollections('featured_imgs')->width(300)->optimize()->sharpen(5)->quality(85)->format('webp')->queued();
        // 3. متوسط (للصفحة الرئيسية)
        //$this->addMediaConversion('medium')->performOnCollections('featured_imgs')->width(600)->optimize()->sharpen(5)->quality(85)->format('webp')->nonQueued();
        $this->addMediaConversion('medium')->performOnCollections('featured_imgs')->width(600)->optimize()->sharpen(5)->quality(85)->format('webp')->queued();
        // 4. كبير (لصفحة المقال الرئيسية)
        //$this->addMediaConversion('large')->performOnCollections('featured_imgs')->width(1200)->optimize()->sharpen(5)->quality(85)->format('webp')->nonQueued();
        $this->addMediaConversion('large')->performOnCollections('featured_imgs')->width(1200)->optimize()->sharpen(5)->quality(85)->format('webp')->queued();
    }
}
