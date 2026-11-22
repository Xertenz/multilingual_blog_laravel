<?php

namespace App\Livewire\Profile;

use App\Enums\CommunityCaseStatus;
use App\Models\CommunityCase;
use App\Models\CommunityCaseCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Enum;
use Livewire\Component;
use Livewire\WithPagination;

class MyCommunityCases extends Component
{
    use WithPagination;

    public string $statusFilter = 'all'; // all, approved, pending, rejected

    // خصائص Modal التعديل
    public bool $showEditModal = false;
    public ?int $editingCaseId = null;
    public string $edit_title = '';
    public string $edit_community_case_category_id = '';
    public string $edit_body = '';
    public string $edit_case_status = 'open';
    public array $edit_locations = [];

    public function setStatusFilter(string $status): void
    {
        if (!in_array($status, ['all', 'approved', 'pending', 'rejected'], true)) {
            $status = 'all';
        }

        $this->statusFilter = $status;
        $this->resetPage();
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

    public function openEditModal(int $caseId): void
    {
        $case = CommunityCase::where('user_id', Auth::id())
            ->with('locations.translations')
            ->findOrFail($caseId);

        if ($case->moderation_status === 'rejected') {
            session()->flash('error', __('You cannot edit a rejected case.'));
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
        $this->edit_community_case_category_id = (string) $case->community_case_category_id;
        $this->edit_body                       = $case->body;
        $this->edit_case_status                = $case->case_status?->value ?? $case->case_status ?? 'open';

        $this->showEditModal = true;
    }

    public function closeEditModal(): void
    {
        $this->showEditModal = false;
        $this->resetValidation();
    }

    public function updateCase(): void
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

        $case = CommunityCase::where('user_id', Auth::id())->findOrFail($this->editingCaseId);

        if ($case->moderation_status === 'rejected') {
            session()->flash('error', __('You cannot edit a rejected case.'));
            $this->closeEditModal();
            return;
        }

        $newStatus = Auth::user()->hasAnyRole(['super-admin', 'editor'])
            ? $case->moderation_status
            : 'pending';

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
        $this->showEditModal = false;

        session()->flash('message', __('Your case has been updated successfully!'));
    }

    public function deleteCase(int $caseId): void
    {
        $case = CommunityCase::where('user_id', Auth::id())
            ->with('locations.translations')
            ->findOrFail($caseId);

        foreach ($case->locations as $location) {
            $location->translations()->delete();
            $location->delete();
        }
        $case->votes()->delete();
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
            $location = $locationId
                ? $case->locations()->where('id', $locationId)->first()
                : null;

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
        $baseQuery = CommunityCase::where('user_id', Auth::id());

        $counts = [
            'all'      => (clone $baseQuery)->count(),
            'approved' => (clone $baseQuery)->where('moderation_status', 'approved')->count(),
            'pending'  => (clone $baseQuery)->where('moderation_status', 'pending')->count(),
            'rejected' => (clone $baseQuery)->where('moderation_status', 'rejected')->count(),
        ];

        $query = (clone $baseQuery)
            ->with(['category.translations', 'locations'])
            ->withCount('comments')
            ->latest();

        if ($this->statusFilter !== 'all') {
            $query->where('moderation_status', $this->statusFilter);
        }

        $cases = $query->paginate(9);

        $categories = CommunityCaseCategory::where('is_active', true)
            ->with(['translations' => fn($q) => $q->where('locale', app()->getLocale())])
            ->get();

        return view('livewire.profile.my-community-cases', [
            'cases'      => $cases,
            'counts'     => $counts,
            'categories' => $categories,
        ]); //->layout('layouts.main');
    }
}
