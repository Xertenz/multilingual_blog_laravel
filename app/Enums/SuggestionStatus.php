<?php

namespace App\Enums;

enum SuggestionStatus: string
{
    case PENDING  = 'pending';
    case APPROVED = 'approved';
    case WRITTEN  = 'written';

    public function label(): string
    {
        return match ($this) {
            self::PENDING  => __('Pending'),
            self::APPROVED => __('In Research'),
            self::WRITTEN  => __('Investigated'),
        };
    }
}
