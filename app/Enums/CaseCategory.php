<?php

namespace App\Enums;

enum CaseCategory: string
{
    case TRUE_CRIME = 'true_crime';
    case HISTORY_MYSTERIES = 'history_mysteries';
    case CYBERSECURITY = 'cybersecurity';
    case PARANORMAL = 'paranormal';

    /**
     * إرجاع العنوان المترجم بحسب لغة الموقع الحالية
     */
    public function label(): string
    {
        return __("enums.case_categories.{$this->value}");
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
