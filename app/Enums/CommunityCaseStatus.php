<?php

namespace App\Enums;

enum CommunityCaseStatus: string
{
    case OPEN = 'open';
    case SOLVED = 'solved';
    case CLOSED = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::OPEN => 'تحقيق نشط',
            self::SOLVED => 'قضية محلولة',
            self::CLOSED => 'مغلقة',
        };
    }
}
