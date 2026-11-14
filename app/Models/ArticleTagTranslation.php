<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArticleTagTranslation extends Model
{
    //
    protected $fillable = ['user_id', 'article_tag_id', 'locale', 'name', 'slug'];
    public function tag(): BelongsTo
    {
        return $this->belongsTo(ArticleTag::class, 'article_tag_id');
    }
}
