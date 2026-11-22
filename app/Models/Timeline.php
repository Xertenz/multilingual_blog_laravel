<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Timeline extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'event_date' => 'date',
    ];

    public function timelineable(): MorphTo
    {
        return $this->morphTo();
    }

    public function translations(): HasMany
    {
        return $this->hasMany(TimelineTranslation::class);
    }

    public function translation(): HasOne
    {
        $locale = app()->getLocale();

        return $this->hasOne(TimelineTranslation::class)
            ->where('locale', $locale);
    }

    public function getLocalizedTranslation(?string $locale = null): ?TimelineTranslation
    {
        $targetLocale = $locale ?: app()->getLocale();

        if ($this->relationLoaded('translations')) {
            return $this->translations->firstWhere('locale', $targetLocale)
                ?? $this->translations->first();
        }

        return $this->translations()->where('locale', $targetLocale)->first()
            ?? $this->translations()->first();
    }

    public function displayTime(?string $locale = null): ?string
    {
        $trans = $this->getLocalizedTranslation($locale);

        if (!empty($trans?->time_label)) {
            return $trans->time_label;
        }

        if (!empty($this->event_year) && !empty($this->event_month)) {
            $month = str_pad((string) $this->event_month, 2, '0', STR_PAD_LEFT);
            return "{$month}/{$this->event_year}";
        }

        if (!empty($this->event_year)) {
            return (string) $this->event_year;
        }

        return $this->event_date?->format('Y-m-d');
    }
}
