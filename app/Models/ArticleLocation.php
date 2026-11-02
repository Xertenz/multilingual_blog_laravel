<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class ArticleLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'article_id',
        'latitude',
        'longitude',
    ];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function translations()
    {
        return $this->hasMany(ArticleLocationTranslation::class);
    }

    // دالة مساعدة لجلب الترجمة حسب لغة التصفح الحالية
    public function getTranslationAttribute()
    {
        $locale = App::getLocale();
        return $this->translations->where('locale', $locale)->first()
            ?? $this->translations->first();
    }
}
