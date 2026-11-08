<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookCategoryTranslation extends Model
{
    protected $guarded = [];

    public function category(): BelongsTo
    {
        return $this->belongsTo(BookCategory::class, 'book_category_id');
    }
}
