<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\App;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'locatable_type',
        'locatable_id',
        'latitude',
        'longitude',
    ];

    public function locatable(): MorphTo
    {
        return $this->morphTo();
    }

    public function translations()
    {
        return $this->hasMany(LocationTranslation::class);
    }

    // دالة مساعدة لجلب الترجمة حسب لغة التصفح الحالية
    public function getTranslationAttribute()
    {
        $locale = App::getLocale();

        return $this->translations->firstWhere('locale', $locale)
            ?? $this->translations->first();
    }
}
