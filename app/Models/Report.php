<?php

namespace App\Models;

use App\Enums\ReportReason;
use App\Enums\ReportStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Report extends Model
{
    protected $guarded = [];

    protected $casts = [
        'status'      => ReportStatus::class,
        'reason_type' => ReportReason::class,
    ];

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function reportable(): MorphTo
    {
        return $this->morphTo();
    }

    public function targetUser(): ?User
    {
        $item = $this->reportable;

        if (!$item) {
            return null;
        }

        if ($item instanceof User) {
            return $item;
        }

        if (isset($item->user) && $item->user instanceof User) {
            return $item->user;
        }

        if (isset($item->author) && $item->author instanceof User) {
            return $item->author;
        }

        return null;
    }
}
