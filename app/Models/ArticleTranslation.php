<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ArticleTranslation extends Model implements HasMedia
{
    use InteractsWithMedia;
    protected $fillable = ['article_id', 'language_code','slug', 'title', 'description', 'meta_title','meta_description','created_at','updated_at'];

    //
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
}
