<?php

namespace App\Livewire\Admin;

use App\Models\CommunityCase;
use Livewire\Component;
use Livewire\WithPagination;

class CaseModeration extends Component
{
    use WithPagination;

    public string $filter = 'pending'; // التبويب الافتراضي: القضايا المعلقة

    /**
     * قبول القضية ونشرها في لوحة المحققين
     */
    public function approveCase(int $caseId)
    {
        $case = CommunityCase::findOrFail($caseId);
        $case->update([
            'moderation_status' => 'approved',
        ]);

        session()->flash('message', __('تم قبول القضية ونشرها بنجاح.'));
    }

    /**
     * رفض القضية
     */
    public function rejectCase(int $caseId)
    {
        $case = CommunityCase::findOrFail($caseId);
        $case->update([
            'moderation_status' => 'rejected',
        ]);

        session()->flash('message', __('تم رفض القضية.'));
    }

    public function render()
    {
        $cases = CommunityCase::query()
            ->where('moderation_status', $this->filter)
            ->with(['user', 'category.translations'])
            ->latest()
            ->paginate(10);

        return view('livewire.admin.case-moderation', [
            'cases' => $cases,
            //])->layout('layouts.app'); // أو layouts.admin إن كان لديك لعيّنة الإدارة
        ])->layout('layouts.admin.master'); // أو layouts.admin إن كان لديك لعيّنة الإدارة
    }
}
