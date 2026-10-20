<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\App;

class Series extends Model
{
    protected $table = 'series';

    protected $fillable = ['user_id', 'internal_title', 'status'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(SeriesTranslation::class, 'series_id');
    }

    public function currentTranslation(): HasOne
    {
        $currentLocale = App::getLocale();
        return $this->hasOne(SeriesTranslation::class)->where('locale', $currentLocale);
    }

    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'article_series', 'series_id', 'article_id')
            ->withPivot('sort_order')
            ->withTimestamps()
            ->orderBy('article_series.sort_order', 'asc');
    }
}
