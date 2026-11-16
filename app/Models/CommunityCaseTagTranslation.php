<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommunityCaseTagTranslation extends Model
{
    //
    protected $guarded = [];
    public function tag(): BelongsTo
    {
        return $this->belongsTo(CommunityCaseTag::class, 'community_case_tag_id');
    }
}
