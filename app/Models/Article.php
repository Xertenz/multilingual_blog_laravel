<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\App;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Image\Enums\Fit;

class Article extends Model  implements HasMedia
{
    use InteractsWithMedia;
    protected $fillable = ['article_category_id', 'user_id', 'internal_title', 'views', 'status'];
    //public $timestamps = false;
    //
    public function translations(): HasMany
    {
        return $this->hasMany(ArticleTranslation::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ArticleCategory::class, 'article_category_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function reactions()
    {
        return $this->morphMany(Reaction::class, 'reactionable');
    }

    public function tags()
    {
        return $this->belongsToMany(ArticleTag::class, 'article_tag', 'article_id', 'article_tag_id');
    }

    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'article_user')->withTimestamps();
    }

    /*
    public function registerMediaConversions(?Media $media = null): void
    {
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
    }
    */

    // 🖼️ تسجيل تحويلات صور المقالات للجودة والنقاء المتوافق مع باقي الموديولات
    public function registerMediaConversions(?Media $media = null): void
    {
        // 1. thumbnail صغير
        $this->addMediaConversion('thumb')
            ->performOnCollections('featured_imgs')
            ->fit(Fit::Contain, 150, 225)
            ->quality(90)
            ->format('webp')
            ->nonQueued();

        // 2. متوسط (للقوائم وصفحة الكاتب)
        $this->addMediaConversion('medium')
            ->performOnCollections('featured_imgs')
            ->fit(Fit::Contain, 400, 600)
            ->quality(92)
            ->format('webp')
            ->nonQueued();

        // 3. كبير (لصفحة المقال الرئيسي)
        $this->addMediaConversion('large')
            ->performOnCollections('featured_imgs')
            ->fit(Fit::Contain, 800, 1200)
            ->quality(95)
            ->format('webp')
            ->nonQueued();

        // 📱 أحجام صور المحتوى الداخلي (Content Images)
        $this->addMediaConversion('content_375w')->performOnCollections('content_imgs')->width(375)->quality(90)->format('webp')->nonQueued();
        $this->addMediaConversion('content_768w')->performOnCollections('content_imgs')->width(768)->quality(90)->format('webp')->nonQueued();
        $this->addMediaConversion('content_1200w')->performOnCollections('content_imgs')->width(1200)->quality(90)->format('webp')->nonQueued();
    }


    public function scopeSearch(object $query, ?string $term)
    {
        if (empty(trim($term))) {
            return $query;
        }

        $locale = App::getLocale();

        return $query->whereHas("translations", function ($q) use ($term, $locale) {
            $q->where('locale', $locale)
                ->where(function ($subQuery) use ($term) {
                    $subQuery->where('title', 'LIKE', "%{$term}%")
                        ->orWhere('description', 'LIKE', "%{$term}%");
                });
        });
    }

    public function series(): BelongsToMany
    {
        return $this->belongsToMany(Series::class, 'article_series', 'article_id', 'series_id')
            ->withPivot('sort_order')
            ->withTimestamps();
    }

    /*
    public function suggestions(): MorphMany
    {
        return $this->morphMany(Suggestion::class, 'suggestable');
    }
    */

    public function locations(): HasMany
    {
        return $this->hasMany(ArticleLocation::class);
    }
}
