<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ArticleCategoryTranslation extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'article_category_id',
        'locale',
        'slug',
        'name',
        'description',
        'meta_title',
        'meta_description'
    ];

    //public $timestamps = false;


    public function category(): BelongsTo
    {
        return $this->belongsTo(ArticleCategory::class, 'article_category_id');
    }
}
