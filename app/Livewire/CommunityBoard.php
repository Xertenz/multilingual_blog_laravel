<?php

namespace App\Livewire;

use App\Enums\CommunityCaseStatus;
use App\Enums\ModerationStatus;
use App\Models\CommunityCase;
use App\Models\CommunityCaseCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Enum;
use Livewire\Component;
use Livewire\WithPagination;

class CommunityBoard extends Component
{
    use WithPagination;

    // متغيرات نموذج إضافة قضية جديدة
    public $community_case_category_id = '';
    public $case_status = 'open';
    public $title = '';
    public $body = '';
    // متغيرات الفلترة والبحث في اللوحة
    public $selectedCategory = '';


    // لتعديل القضية
    public ?int $editingCaseId = null;
    public $edit_title = '';
    public $edit_community_case_category_id = '';
    public $edit_body = '';
    public $edit_case_status = 'open';

    // قواعد التحقق من البيانات المدخلة
    protected function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'community_case_category_id' => 'required|exists:community_case_categories,id',
            'body' => 'required|string|min:50',
            'case_status' => ['required', new Enum(CommunityCaseStatus::class)],
        ];
    }

    /**
     * حفظ القضية الجديدة
     */
    public function storeCase()
    {
        // التأكد من تسجيل دخول المستخدم
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // 1. التحقق مما إذا كان المستخدم محظوراً (عقوبة نشطة)
        if (Auth::user()->isBanned()) {
            $penalty = Auth::user()->activePenalty;
            session()->flash('error', "Your account is temporarily banned because: {$penalty->reason}");
            return;
        }

        $this->validate();

        // ✅ المشرف/المدير: نشر مباشر. غيرهما: pending
        $moderationStatus = Auth::user()->hasAnyRole(['super-admin', 'editor'])
            ? 'approved'
            : 'pending';

        // إنشاء القضية بحالة معلقة للمراجعة مع الربط الفئة الصحيح
        CommunityCase::create([
            'user_id' => Auth::id(),
            'locale' => app()->getLocale(),
            'community_case_category_id' => $this->community_case_category_id,
            'title' => $this->title,
            'slug' => Str::slug($this->title) . '-' . uniqid(),
            'body' => $this->body,
            'moderation_status' => $moderationStatus, // بانتظار مراجعة الإدارة
            //'moderation_status' => [new Enum(ModerationStatus::class)],
            'case_status' =>  $this->case_status
        ]);

        if ($moderationStatus === 'pending') {
            // إشعار المشرفين والمحررين بنشر قضية مجتمع جديدة
            $moderators = \App\Models\User::role(['super-admin', 'editor'])->get();
            foreach ($moderators as $moderator) {
                //$moderator->notify(new \App\Notifications\NewCommunityCasePending($case));
            }
        }

        // تصفير الحقول وإظهار رسالة نجاح
        $this->reset(['title', 'community_case_category_id', 'body']);
        $message = $moderationStatus === 'approved'
            ? __('Your case has been published successfully!')
            : __('Your case has been submitted successfully! It will appear on the detective board once reviewed by the administration.');

        session()->flash('message', $message);

        // إرسال إشارة للواجهة لإغلاق الـ Modal فقط عند النجاح
        $this->dispatch('close-case-modal');
    }

    public function openEditModal(int $caseId)
    {
        $case = CommunityCase::findOrFail($caseId);

        // ✅ التحقق من الصلاحية: صاحب القضية أو مشرف/مدير
        if ($case->user_id !== Auth::id() && !Auth::user()->hasAnyRole(['super-admin', 'editor'])) {
            abort(403, 'غير مسموح لك بتعديل هذه القضية.');
        }

        // ✅ منع التعديل إذا كانت القضية مرفوضة (اختياري)
        if ($case->moderation_status === 'rejected') {
            session()->flash('error', 'لا يمكن تعديل قضية مرفوضة.');
            return;
        }

        $this->editingCaseId = $case->id;
        $this->edit_title = $case->title;
        $this->edit_community_case_category_id = $case->community_case_category_id;
        $this->edit_body = $case->body;
        $this->edit_case_status = $case->case_status?->value ?? $case->case_status ?? 'open';

        $this->dispatch('open-edit-case-modal');
    }

    public function updateCase()
    {
        $this->validate([
            'edit_title' => 'required|string|max:255',
            'edit_community_case_category_id' => 'required|exists:community_case_categories,id',
            'edit_body' => 'required|string|min:50',
            'edit_case_status' => ['required', new Enum(CommunityCaseStatus::class)],
        ]);

        $case = CommunityCase::findOrFail($this->editingCaseId);

        // ✅ التحقق من الصلاحية مرة أخرى (أمان)
        if ($case->user_id !== Auth::id() && !Auth::user()->hasAnyRole(['super-admin', 'editor'])) {
            abort(403);
        }

        // ✅ التعديل يعيد الحالة إلى pending (إلا للمشرف/المدير)
        $newStatus = $case->moderation_status;
        if (!Auth::user()->hasAnyRole(['super-admin', 'editor'])) {
            $newStatus = 'pending';
        }

        $case->update([
            'title' => $this->edit_title,
            'community_case_category_id' => $this->edit_community_case_category_id,
            'body' => $this->edit_body,
            'moderation_status' => $newStatus,
            'case_status' => $this->edit_case_status,
        ]);

        // ✅ تصفير الحقول
        $this->reset(['editingCaseId', 'edit_title', 'edit_community_case_category_id', 'edit_body', 'edit_case_status']);

        session()->flash('message', __('Your case has been updated successfully!'));
        $this->dispatch('close-edit-case-modal');
    }

    public function render()
    {
        // جلب القضايا المقبولة فقط وحسب لغة الموقع أو الفلتر
        $query = CommunityCase::query()
            ->where('moderation_status', 'approved')
            ->where('locale', app()->getLocale())
            ->with(['author', 'category.translations'])
            ->latest();

        if ($this->selectedCategory) {
            $query->where('community_case_category_id', $this->selectedCategory);
        }

        $cases = $query->paginate(9);

        // جلب الفئات المفعلة مع ترجماتها للغة الحالية لملء القائمة المنسدلة (Dropdown)
        $categories = CommunityCaseCategory::where('is_active', true)
            ->with(['translations' => function ($q) {
                $q->where('locale', app()->getLocale());
            }])
            ->get();

        return view('livewire.community-board', [
            'cases' => $cases,
            'categories' => $categories,
        ]); //->layout('layouts.main');
    }
}
