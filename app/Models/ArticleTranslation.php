<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ArticleTranslation extends Model implements HasMedia
{
    use InteractsWithMedia, HasFactory;
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
        'notification_sent_at',
        'views'
    ];

    protected $casts = [
        'status' => \App\Enums\ContentStatus::class,
    ];

    //
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class, 'article_id');
    }
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
