<?php

namespace App\Enums;

enum ModerationStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';

    // دالة مساعدة للحصول على الألوان أو التسميات (اختياري لتسهيل العرض في الواجهات)
    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'قيد الانتظار',
            self::APPROVED => 'مقبولة',
            self::REJECTED => 'مرفوضة',
        };
    }
}
