<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Article extends Model  implements HasMedia
{
    use InteractsWithMedia;
    protected $fillable = ['category_id', 'user_id', 'sign', 'views', 'status'];
    //public $timestamps = false;
    //
    public function translations(): HasMany
    {
        return $this->hasMany(ArticleTranslation::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->latest();
    }

    public function reactions()
    {
        return $this->morphMany(Reaction::class, 'reactionable');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'article_user')->withTimestamps();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        /*
        // 1. thumbnail صغير (للقوائم)
        $this->addMediaConversion('thumb')->performOnCollections('featured_imgs')->width(150)->optimize() ->sharpen(5)->quality(80)->format('webp')->nonQueued();
        // 2. صغير (للمعاينات)
        $this->addMediaConversion('small')->performOnCollections('featured_imgs')->width(300)->optimize() ->sharpen(5)->quality(85)->format('webp')->nonQueued();
        // 3. متوسط (للصفحة الرئيسية)
        $this->addMediaConversion('medium')->performOnCollections('featured_imgs')->width(600)->optimize() ->sharpen(5)->quality(85)->format('webp')->nonQueued();
        // 4. كبير (لصفحة المقال الرئيسية)
        $this->addMediaConversion('large')->performOnCollections('featured_imgs')->width(1200)->optimize() ->sharpen(5)->quality(85)->format('webp')->nonQueued();

            // 📱 Mobile أحجام
        $this->addMediaConversion('content_375w')->performOnCollections('content_imgs')->width(375)->optimize()->sharpen(5)->quality(90)->format('webp')->nonQueued();
        $this->addMediaConversion('content_480w')->performOnCollections('content_imgs')->width(480)->optimize()->sharpen(5)->quality(90)->format('webp')->nonQueued();
        
        // 📟 Tablet أحجام
        $this->addMediaConversion('content_768w')->performOnCollections('content_imgs')->width(768)->optimize()->sharpen(5)->quality(90)->format('webp')->nonQueued();
        $this->addMediaConversion('content_800w')->performOnCollections('content_imgs')->width(800)->optimize()->sharpen(5)->quality(90)->format('webp')->nonQueued();
        // 💻 Desktop أحجام
        $this->addMediaConversion('content_1024w')->performOnCollections('content_imgs')->width(1024)->optimize()->sharpen(5)->quality(90)->format('webp')->nonQueued();
        $this->addMediaConversion('content_1200w')->performOnCollections('content_imgs')->width(1200)->optimize()->sharpen(5)->quality(90)->format('webp')->nonQueued();
        // 🖥️ Large Screens
        $this->addMediaConversion('content_1600w')->performOnCollections('content_imgs')->width(1600)->optimize()->sharpen(5)->quality(90)->format('webp')->nonQueued();
        */
    }
}
