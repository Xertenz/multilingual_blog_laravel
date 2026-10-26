<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Models\Suggestion;

class SuggestionStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    public Suggestion $suggestion;

    public function __construct(Suggestion $suggestion)
    {
        $this->suggestion = $suggestion;
    }

    // تحديد قنوات الإرسال (سنكتفي بقناة قاعدة البيانات لحفظ الإشعارات داخل الموقع)
    public function via($notifiable): array
    {
        return ['database'];
    }

    // البيانات التي سيتم تخزينها في جدول الإشعارات (JSON)
    public function toDatabase($notifiable): array
    {
        return [
            'suggestion_id' => $this->suggestion->id,
            'title' => $this->suggestion->title,
            'status' => $this->suggestion->status,
            'message' => 'The status of your suggestion: ' . $this->suggestion->title . ' has been changed to ' . $this->getStatusLabel($this->suggestion->status),
        ];
    }

    // دالة مساعدة لترجمة الحالة للغة العربية أو عرضها بشكل لطيف
    private function getStatusLabel($status)
    {
        return match ($status) {
            'approved' => 'Under Research 🔍',
            'written' => 'Investigated & Published ⭐',
            default => 'Pending Review ⏳',
        };
    }
}
