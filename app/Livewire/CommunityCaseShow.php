<?php

namespace App\Livewire;

use App\Enums\CommunityCaseStatus;
use App\Models\CommunityCase;
use App\Models\CommunityCaseCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Enum;
use Livewire\Component;

class CommunityCaseShow extends Component
{
    public CommunityCase $communityCase;

    // ✅ خصائص Modal التعديل
    public bool $showEditModal = false;
    public string $edit_title = '';
    public string $edit_community_case_category_id = '';
    public string $edit_body = '';
    public string $edit_case_status = 'open';

    public function mount(string $slug)
    {
        $this->communityCase = CommunityCase::where('slug', $slug)
            ->where('moderation_status', 'approved')
            ->with(['author', 'category.translations'])
            ->firstOrFail();

        $this->communityCase->increment('views_count');
    }

    /**
     * فتح Modal التعديل
     */
    public function openEditModal()
    {
        // ✅ التحقق من الصلاحية
        if ($this->communityCase->user_id !== Auth::id() && !Auth::user()->hasAnyRole(['super-admin', 'editor'])) {
            abort(403, 'غير مسموح لك بتعديل هذه القضية.');
        }

        // ✅ منع التعديل إذا كانت القضية مرفوضة
        if ($this->communityCase->moderation_status === 'rejected') {
            session()->flash('error', __('You cannot edit a rejected case.'));
            return;
        }

        // ✅ تعبئة الحقول بالقيم الحالية
        $this->edit_title = $this->communityCase->title;
        $this->edit_community_case_category_id = (string) $this->communityCase->community_case_category_id;
        $this->edit_body = $this->communityCase->body;
        $this->edit_case_status = $this->communityCase->case_status?->value ?? $this->communityCase->case_status ?? 'open';

        $this->showEditModal = true;
    }

    /**
     * إغلاق Modal التعديل
     */
    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->resetValidation();
    }

    /**
     * حفظ تعديلات القضية
     */
    public function updateCase()
    {
        // ✅ التحقق من الصلاحية
        if ($this->communityCase->user_id !== Auth::id() && !Auth::user()->hasAnyRole(['super-admin', 'editor'])) {
            abort(403);
        }

        $this->validate([
            'edit_title' => 'required|string|max:255',
            'edit_community_case_category_id' => 'required|exists:community_case_categories,id',
            'edit_body' => 'required|string|min:50',
            'edit_case_status' => ['required', new Enum(CommunityCaseStatus::class)],
        ]);

        // ✅ الكاتب يعيد القضية إلى pending، المشرف/المدير يحافظ على الحالة
        $newStatus = $this->communityCase->moderation_status;
        if (!Auth::user()->hasAnyRole(['super-admin', 'editor'])) {
            $newStatus = 'pending';
        }

        $this->communityCase->update([
            'title' => $this->edit_title,
            'community_case_category_id' => $this->edit_community_case_category_id,
            'body' => $this->edit_body,
            'moderation_status' => $newStatus,
            'case_status' => $this->edit_case_status,
        ]);

        $this->showEditModal = false;

        // ✅ رسالة نجاح
        session()->flash('message', __('Your case has been updated successfully!'));
    }

    public function render()
    {
        $categoryTrans = $this->communityCase->category->translations()
            ->where('locale', app()->getLocale())
            ->first() ?? $this->communityCase->category->translations()->first();

        // ✅ جلب الفئات المتاحة للـ Modal
        $categories = CommunityCaseCategory::where('is_active', true)
            ->with(['translations' => fn($q) => $q->where('locale', app()->getLocale())])
            ->get();

        return view('livewire.community-case-show', [
            'categoryTrans' => $categoryTrans,
            'categories' => $categories,
        ]);
    }
}
