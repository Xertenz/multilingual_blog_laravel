<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Article extends Model // implements HasMedia
{
    //use InteractsWithMedia;
    protected $fillable = ['category_id', 'featured_img', 'views', 'status'];
    public $timestamps = false;
    //
    public function translations(): HasMany
    {
        return $this->hasMany(ArticleTranslation::class);
    }

    /*
    public function categories_imgs() {
        return $this->getMedia('categories');
    }
        */

}
