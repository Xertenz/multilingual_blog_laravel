<?php

namespace App\Livewire;

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
    public $title = '';
    public $community_case_category_id = '';
    public $body = '';

    // متغيرات الفلترة والبحث في اللوحة
    public $selectedCategory = '';

    // قواعد التحقق من البيانات المدخلة
    protected function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'community_case_category_id' => 'required|exists:community_case_categories,id',
            'body' => 'required|string|min:50',
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

        // إنشاء القضية بحالة معلقة للمراجعة مع الربط الفئة الصحيح
        CommunityCase::create([
            'user_id' => Auth::id(),
            'locale' => app()->getLocale(),
            'community_case_category_id' => $this->community_case_category_id,
            'title' => $this->title,
            'slug' => Str::slug($this->title) . '-' . uniqid(),
            'body' => $this->body,
            'moderation_status' => 'pending', // بانتظار مراجعة الإدارة
            //'moderation_status' => [new Enum(ModerationStatus::class)],
            'case_status' => 'open',
        ]);

        // تصفير الحقول وإظهار رسالة نجاح
        $this->reset(['title', 'community_case_category_id', 'body']);
        session()->flash('message', __('Your case has been submitted successfully! It will appear on the detective board once reviewed by the administration.'));

        // إرسال إشارة للواجهة لإغلاق الـ Modal فقط عند النجاح
        $this->dispatch('close-case-modal');
    }

    public function render()
    {
        // جلب القضايا المقبولة فقط وحسب لغة الموقع أو الفلتر
        $query = CommunityCase::query()
            ->where('moderation_status', 'approved')
            ->where('locale', app()->getLocale())
            ->with(['user', 'category.translations'])
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
        ])->layout('layouts.main');
    }
}
