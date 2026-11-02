<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleLocationTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'article_location_id',
        'locale',
        'title',
        'description',
    ];

    public function location()
    {
        return $this->belongsTo(ArticleLocation::class, 'article_location_id');
    }
}
