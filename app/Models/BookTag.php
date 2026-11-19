<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\App;

class BookTag extends Model
{
    protected $guarded = [];

    public function translations(): HasMany
    {
        return $this->hasMany(BookTagTranslation::class);
    }

    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class, 'book_tag', 'book_tag_id', 'book_id'); //->withTimestamps();
    }

    public function currentTranslation()
    {
        return $this->hasOne(BookTagTranslation::class)->where('locale', App::getLocale());
    }

    // داخل كلاس BookTag:
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
