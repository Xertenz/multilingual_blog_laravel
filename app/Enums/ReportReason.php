<?php

namespace App\Enums;

enum ReportReason: string
{
    case SPAM = 'spam';
    case HARASSMENT = 'harassment';
    case INAPPROPRIATE = 'inappropriate_content';
    case MISINFORMATION = 'misinformation';
    case OTHER = 'other';

    // دالة لجلب النص المترجم حسب لغة الموقع الحالية
    public function label(): string
    {
        return __('reports.reasons.' . $this->value);
    }
}
