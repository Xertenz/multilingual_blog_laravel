<?php

namespace App\Enums;

enum MediaType: string
{
    case MOVIE = 'movie';
    case SERIES = 'series';
    case DOCUMENTARY = 'documentary';
    case PODCAST = 'podcast';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
