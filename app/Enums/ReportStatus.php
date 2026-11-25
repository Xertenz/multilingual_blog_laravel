<?php

namespace App\Enums;

enum ReportStatus: string
{
    case PENDING   = 'pending';
    case RESOLVED  = 'resolved';
    case DISMISSED = 'dismissed';

    public function label(): string
    {
        return match ($this) {
            self::PENDING   => __('Pending'),
            self::RESOLVED  => __('Resolved'),
            self::DISMISSED => __('Dismissed'),
        };
    }
}
