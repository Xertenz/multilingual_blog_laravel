<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class ArticleTag extends Model
{
    //
    protected $fillable = ['label'];
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
}
