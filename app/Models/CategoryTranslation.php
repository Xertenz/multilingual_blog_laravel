<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class CategoryTranslation extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'category_id',
        'language_code',
        'slug',
        'name',
        'description',
        'meta_title',
        'meta_description'
    ];

    //public $timestamps = false;


    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
