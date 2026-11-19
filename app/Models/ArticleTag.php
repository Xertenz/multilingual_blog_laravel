<?php

namespace App\Models;

use App\Models\Concerns\Followable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\App;

class ArticleTag extends Model
{
    //
    protected $guarded = [];
    public function articles()
    {
        return $this->belongsToMany(Article::class, 'article_tag', 'article_tag_id', 'article_id');
    }

    public function translations()
    {
        return $this->hasMany(ArticleTagTranslation::class, 'article_tag_id');
    }

    // دالة مساعدة لجلب ترجمة الوسم الحالية بناءً على لغة الموقع
    public function currentTranslation()
    {
        return $this->hasOne(ArticleTagTranslation::class)->where('locale', App::getLocale());
    }

    public function translate(string $localeCode)
    {
        return $this->translations()->where('locale', $localeCode)->first();
    }

    public function followers(): MorphToMany
    {
        return $this->morphToMany(
            User::class,
            'followable',
            'follows',
            'followable_id',
            'user_id',
            'id',
            'id'
        )->withTimestamps();
    }
}
