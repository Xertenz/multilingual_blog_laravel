<?php

namespace App\Livewire;

use App\Enums\CommunityCaseStatus;
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

    // موقع القضية الجديدة (اختياري)
    public $location_title = '';
    public $location_description = '';
    public $location_latitude = '';
    public $location_longitude = '';

    // موقع القضية عند التعديل (اختياري)
    public $edit_location_title = '';
    public $edit_location_description = '';
    public $edit_location_latitude = '';
    public $edit_location_longitude = '';

    // قواعد التحقق من البيانات المدخلة عند الإنشاء
    protected function rules()
    {
        return [
            'title'                => 'required|string|max:255',
            'community_case_category_id' => 'required|exists:community_case_categories,id',
            'body'                 => 'required|string|min:50',
            'case_status'          => ['required', new Enum(CommunityCaseStatus::class)],

            'location_title'       => 'nullable|string|max:255',
            'location_description' => 'nullable|string|max:2000',
            'location_latitude'    => 'nullable|numeric|between:-90,90',
            'location_longitude'   => 'nullable|numeric|between:-180,180',
        ];
    }

    /**
     * حفظ القضية الجديدة
     */
    public function storeCase()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (Auth::user()->isBanned()) {
            $penalty = Auth::user()->activePenalty;
            session()->flash('error', "Your account is temporarily banned because: {$penalty->reason}");
            return;
        }

        $this->validate();

        $moderationStatus = Auth::user()->hasAnyRole(['super-admin', 'editor'])
            ? 'approved'
            : 'pending';

        $case = CommunityCase::create([
            'user_id'                      => Auth::id(),
            'locale'                       => app()->getLocale(),
            'community_case_category_id' => $this->community_case_category_id,
            'title'                        => $this->title,
            'slug'                         => Str::slug($this->title) . '-' . uniqid(),
            'body'                         => $this->body,
            'moderation_status'            => $moderationStatus,
            'case_status'                  => $this->case_status,
        ]);

        $this->syncCaseLocation(
            $case,
            $this->location_title,
            $this->location_description,
            $this->location_latitude,
            $this->location_longitude
        );

        if ($moderationStatus === 'pending') {
            $moderators = \App\Models\User::role(['super-admin', 'editor'])->get();
            foreach ($moderators as $moderator) {
                //$moderator->notify(new \App\Notifications\NewCommunityCasePending($case));
            }
        }

        $this->reset([
            'title',
            'community_case_category_id',
            'body',
            'case_status',
            'location_title',
            'location_description',
            'location_latitude',
            'location_longitude',
        ]);
        $this->case_status = 'open';

        $message = $moderationStatus === 'approved'
            ? __('Your case has been published successfully!')
            : __('Your case has been submitted successfully! It will appear on the detective board once reviewed by the administration.');

        session()->flash('message', $message);

        $this->dispatch('close-case-modal');
    }

    public function openEditModal(int $caseId)
    {
        $case = CommunityCase::with('locations.translations')->findOrFail($caseId);

        if ($case->user_id !== Auth::id() && !Auth::user()->hasAnyRole(['super-admin', 'editor'])) {
            abort(403, 'غير مسموح لك بتعديل هذه القضية.');
        }

        if ($case->moderation_status === 'rejected') {
            session()->flash('error', 'لا يمكن تعديل قضية مرفوضة.');
            return;
        }

        $firstLoc = $case->locations->first();
        $locTrans = $firstLoc?->translation;

        $this->edit_location_latitude    = $firstLoc?->latitude ?? '';
        $this->edit_location_longitude   = $firstLoc?->longitude ?? '';
        $this->edit_location_title       = $locTrans?->title ?? '';
        $this->edit_location_description = $locTrans?->description ?? '';

        $this->editingCaseId                = $case->id;
        $this->edit_title                   = $case->title;
        $this->edit_community_case_category_id = $case->community_case_category_id;
        $this->edit_body                    = $case->body;
        $this->edit_case_status             = $case->case_status?->value ?? $case->case_status ?? 'open';

        $this->dispatch('open-edit-case-modal');
    }

    public function updateCase()
    {
        $this->validate([
            'edit_title'                   => 'required|string|max:255',
            'edit_community_case_category_id' => 'required|exists:community_case_categories,id',
            'edit_body'                    => 'required|string|min:50',
            'edit_case_status'             => ['required', new Enum(CommunityCaseStatus::class)],

            'edit_location_title'          => 'nullable|string|max:255',
            'edit_location_description'    => 'nullable|string|max:2000',
            'edit_location_latitude'       => 'nullable|numeric|between:-90,90',
            'edit_location_longitude'      => 'nullable|numeric|between:-180,180',
        ]);

        $case = CommunityCase::findOrFail($this->editingCaseId);

        if ($case->user_id !== Auth::id() && !Auth::user()->hasAnyRole(['super-admin', 'editor'])) {
            abort(403);
        }

        $newStatus = $case->moderation_status;
        if (!Auth::user()->hasAnyRole(['super-admin', 'editor'])) {
            $newStatus = 'pending';
        }

        $case->update([
            'title'                        => $this->edit_title,
            'community_case_category_id' => $this->edit_community_case_category_id,
            'body'                         => $this->edit_body,
            'moderation_status'            => $newStatus,
            'case_status'                  => $this->edit_case_status,
        ]);

        $this->syncCaseLocation(
            $case,
            $this->edit_location_title,
            $this->edit_location_description,
            $this->edit_location_latitude,
            $this->edit_location_longitude
        );

        $this->reset([
            'editingCaseId',
            'edit_title',
            'edit_community_case_category_id',
            'edit_body',
            'edit_case_status',
            'edit_location_title',
            'edit_location_description',
            'edit_location_latitude',
            'edit_location_longitude',
        ]);
        $this->edit_case_status = 'open';

        session()->flash('message', __('Your case has been updated successfully!'));
        $this->dispatch('close-edit-case-modal');
    }

    protected function syncCaseLocation(CommunityCase $case, ?string $title, ?string $description, $lat, $lng): void
    {
        $hasCoords = filled($lat) && filled($lng);
        $hasTitle = filled(trim((string) $title));

        if (!$hasCoords && !$hasTitle) {
            return;
        }

        $location = $case->locations()->first();

        if ($hasCoords) {
            if ($location) {
                $location->update([
                    'latitude'  => (float) $lat,
                    'longitude' => (float) $lng,
                ]);
            } else {
                $location = $case->locations()->create([
                    'latitude'  => (float) $lat,
                    'longitude' => (float) $lng,
                ]);
            }

            $locale = $case->locale ?: app()->getLocale();
            $cleanTitle = $hasTitle ? trim((string) $title) : __('Case Location');
            $cleanDesc = filled(trim((string) $description)) ? trim((string) $description) : null;

            $location->translations()->updateOrCreate(
                ['locale' => $locale],
                [
                    'title'       => $cleanTitle,
                    'description' => $cleanDesc,
                ]
            );
        }
    }


    public function deleteCase(int $caseId)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $case = CommunityCase::with('locations.translations')->findOrFail($caseId);

        if ($case->user_id !== Auth::id() && !Auth::user()->hasAnyRole(['super-admin', 'editor'])) {
            abort(403, 'غير مسموح لك بحذف هذه القضية.');
        }

        foreach ($case->locations as $location) {
            $location->translations()->delete();
            $location->delete();
        }

        $case->delete();

        session()->flash('message', __('Case has been deleted successfully!'));
    }


    public function render()
    {
        $query = CommunityCase::query()
            ->where('moderation_status', 'approved')
            ->where('locale', app()->getLocale())
            ->with(['author', 'category.translations'])
            ->latest();

        if ($this->selectedCategory) {
            $query->where('community_case_category_id', $this->selectedCategory);
        }

        $cases = $query->paginate(9);

        $categories = CommunityCaseCategory::where('is_active', true)
            ->with(['translations' => function ($q) {
                $q->where('locale', app()->getLocale());
            }])
            ->get();

        return view('livewire.community-board', [
            'cases' => $cases,
            'categories' => $categories,
        ]);
    }
}
