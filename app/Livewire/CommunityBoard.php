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

    // مواقع القضية الجديدة (اختياري - متعدد)
    public array $locations = [];

    // مواقع القضية عند التعديل (اختياري - متعدد)
    public array $edit_locations = [];

    public function addLocation(): void
    {
        $this->locations[] = [
            'title'       => '',
            'latitude'    => '',
            'longitude'   => '',
            'description' => '',
        ];
    }

    public function removeLocation(int $index): void
    {
        if (isset($this->locations[$index])) {
            unset($this->locations[$index]);
            $this->locations = array_values($this->locations);
        }
    }

    public function addEditLocation(): void
    {
        $this->edit_locations[] = [
            'id'          => null,
            'title'       => '',
            'latitude'    => '',
            'longitude'   => '',
            'description' => '',
        ];
    }

    public function removeEditLocation(int $index): void
    {
        if (isset($this->edit_locations[$index])) {
            unset($this->edit_locations[$index]);
            $this->edit_locations = array_values($this->edit_locations);
        }
    }

    protected function rules()
    {
        return [
            'title'                         => 'required|string|max:255',
            'community_case_category_id'  => 'required|exists:community_case_categories,id',
            'body'                          => 'required|string|min:50',
            'case_status'                   => ['required', new Enum(CommunityCaseStatus::class)],

            'locations'                     => 'nullable|array',
            'locations.*.title'             => 'nullable|string|max:255',
            'locations.*.description'       => 'nullable|string|max:2000',
            'locations.*.latitude'          => 'nullable|numeric|between:-90,90|required_with:locations.*.longitude',
            'locations.*.longitude'         => 'nullable|numeric|between:-180,180|required_with:locations.*.latitude',
        ];
    }

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

        $this->syncCaseLocations($case, $this->locations);

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
            'locations',
        ]);
        $this->case_status = 'open';
        $this->locations = [];

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

        $this->edit_locations = $case->locations->map(function ($loc) {
            $t = $loc->translation;
            return [
                'id'          => $loc->id,
                'title'       => $t?->title ?? '',
                'latitude'    => $loc->latitude,
                'longitude'   => $loc->longitude,
                'description' => $t?->description ?? '',
            ];
        })->values()->all();

        $this->editingCaseId                   = $case->id;
        $this->edit_title                      = $case->title;
        $this->edit_community_case_category_id = $case->community_case_category_id;
        $this->edit_body                       = $case->body;
        $this->edit_case_status                = $case->case_status?->value ?? $case->case_status ?? 'open';

        $this->dispatch('open-edit-case-modal');
    }

    public function updateCase()
    {
        $this->validate([
            'edit_title'                      => 'required|string|max:255',
            'edit_community_case_category_id' => 'required|exists:community_case_categories,id',
            'edit_body'                       => 'required|string|min:50',
            'edit_case_status'                => ['required', new Enum(CommunityCaseStatus::class)],

            'edit_locations'                  => 'nullable|array',
            'edit_locations.*.id'             => 'nullable|integer',
            'edit_locations.*.title'          => 'nullable|string|max:255',
            'edit_locations.*.description'    => 'nullable|string|max:2000',
            'edit_locations.*.latitude'       => 'nullable|numeric|between:-90,90|required_with:edit_locations.*.longitude',
            'edit_locations.*.longitude'      => 'nullable|numeric|between:-180,180|required_with:edit_locations.*.latitude',
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

        $this->syncCaseLocations($case, $this->edit_locations);

        $this->reset([
            'editingCaseId',
            'edit_title',
            'edit_community_case_category_id',
            'edit_body',
            'edit_case_status',
            'edit_locations',
        ]);
        $this->edit_case_status = 'open';
        $this->edit_locations = [];

        session()->flash('message', __('Your case has been updated successfully!'));
        $this->dispatch('close-edit-case-modal');
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

    protected function syncCaseLocations(CommunityCase $case, array $items): void
    {
        $locale = $case->locale ?: app()->getLocale();
        $keptIds = [];

        foreach ($items as $item) {
            $lat = $item['latitude'] ?? null;
            $lng = $item['longitude'] ?? null;
            $title = trim((string) ($item['title'] ?? ''));
            $desc = trim((string) ($item['description'] ?? ''));

            if (!filled($lat) || !filled($lng)) {
                continue;
            }

            $locationId = $item['id'] ?? null;
            $location = null;

            if ($locationId) {
                $location = $case->locations()->where('id', $locationId)->first();
            }

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

            $keptIds[] = $location->id;

            $location->translations()->updateOrCreate(
                ['locale' => $locale],
                [
                    'title'       => $title !== '' ? $title : __('Case Location'),
                    'description' => $desc !== '' ? $desc : null,
                ]
            );
        }

        // حذف المواقع التي أزالها المستخدم أثناء التعديل
        $locationsToDelete = empty($keptIds)
            ? $case->locations()->with('translations')->get()
            : $case->locations()->whereNotIn('id', $keptIds)->with('translations')->get();

        foreach ($locationsToDelete as $oldLoc) {
            $oldLoc->translations()->delete();
            $oldLoc->delete();
        }
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
