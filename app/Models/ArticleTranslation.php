<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ArticleTranslation extends Model implements HasMedia
{
    use InteractsWithMedia;
    protected $fillable = [
        'article_id',
        'user_id',
        'locale',
        'slug',
        'status',
        'title',
        'description',
        'meta_title',
        'meta_description',
        'views'
    ];

    //
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class, 'article_id');
    }
}
