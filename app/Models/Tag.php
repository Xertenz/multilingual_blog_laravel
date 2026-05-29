<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class Tag extends Model
{
    //
    protected $fillable = ['sign'];
    public function articles()
    {
        return $this->belongsToMany(Article::class);
    }

    public function translations()
    {
        return $this->hasMany(TagTranslation::class, 'tag_id');
    }

    // دالة مساعدة لجلب ترجمة الوسم الحالية بناءً على لغة الموقع
    public function currentTranslation()
    {
        return $this->hasOne(TagTranslation::class)->where('language_code', App::getLocale());
    }

    public function translate(string $localeCode)
    {
        return $this->translations()->where('language_code', $localeCode)->first();
    }
}
