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

    // حقول تعديل الموقع
    public $edit_location_title = '';
    public $edit_location_description = '';
    public $edit_location_latitude = '';
    public $edit_location_longitude = '';

    public function mount(string $slug)
    {
        $this->communityCase = CommunityCase::where('slug', $slug)
            ->where('moderation_status', 'approved')
            ->with(['author', 'category.translations', 'locations.translations'])
            ->firstOrFail();

        $this->communityCase->increment('views_count');
    }

    public function openEditModal()
    {
        if ($this->communityCase->user_id !== Auth::id() && !Auth::user()->hasAnyRole(['super-admin', 'editor'])) {
            abort(403, 'غير مسموح لك بتعديل هذه القضية.');
        }

        if ($this->communityCase->moderation_status === 'rejected') {
            session()->flash('error', __('You cannot edit a rejected case.'));
            return;
        }

        $this->communityCase->loadMissing('locations.translations');
        $firstLoc = $this->communityCase->locations->first();
        $locTrans = $firstLoc?->translation;

        $this->edit_location_latitude    = $firstLoc?->latitude ?? '';
        $this->edit_location_longitude   = $firstLoc?->longitude ?? '';
        $this->edit_location_title       = $locTrans?->title ?? '';
        $this->edit_location_description = $locTrans?->description ?? '';

        $this->edit_title                   = $this->communityCase->title;
        $this->edit_community_case_category_id = (string) $this->communityCase->community_case_category_id;
        $this->edit_body                    = $this->communityCase->body;
        $this->edit_case_status             = $this->communityCase->case_status?->value ?? $this->communityCase->case_status ?? 'open';

        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->resetValidation();
    }

    public function updateCase()
    {
        if ($this->communityCase->user_id !== Auth::id() && !Auth::user()->hasAnyRole(['super-admin', 'editor'])) {
            abort(403);
        }

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

        $newStatus = $this->communityCase->moderation_status;
        if (!Auth::user()->hasAnyRole(['super-admin', 'editor'])) {
            $newStatus = 'pending';
        }

        $this->communityCase->update([
            'title'                        => $this->edit_title,
            'community_case_category_id' => $this->edit_community_case_category_id,
            'body'                         => $this->edit_body,
            'moderation_status'            => $newStatus,
            'case_status'                  => $this->edit_case_status,
        ]);

        $this->syncCaseLocation(
            $this->communityCase,
            $this->edit_location_title,
            $this->edit_location_description,
            $this->edit_location_latitude,
            $this->edit_location_longitude
        );

        $this->communityCase->load('locations.translations');
        //$this->formatCaseLocations();
        $this->dispatch('case-locations-updated', locations: $this->formatCaseLocations());

        $this->reset([
            'edit_location_title',
            'edit_location_description',
            'edit_location_latitude',
            'edit_location_longitude',
        ]);

        $this->showEditModal = false;
        session()->flash('message', __('Your case has been updated successfully!'));
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

    protected function formatCaseLocations(): array
    {
        return $this->communityCase->locations->map(function ($loc) {
            $t = $loc->translation;
            return [
                'latitude'    => (float) $loc->latitude,
                'longitude'   => (float) $loc->longitude,
                'title'       => $t?->title ?? __('Location'),
                'description' => $t?->description ?? '',
            ];
        })->values()->all();
    }

    public function deleteCase()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if ($this->communityCase->user_id !== Auth::id() && !Auth::user()->hasAnyRole(['super-admin', 'editor'])) {
            abort(403, 'غير مسموح لك بحذف هذه القضية.');
        }

        $this->communityCase->loadMissing('locations.translations');

        foreach ($this->communityCase->locations as $location) {
            $location->translations()->delete();
            $location->delete();
        }

        $this->communityCase->delete();

        session()->flash('message', __('Case has been deleted successfully!'));

        return redirect()->route('community.cases.index');
    }

    public function render()
    {
        $categoryTrans = $this->communityCase->category->translations()
            ->where('locale', app()->getLocale())
            ->first() ?? $this->communityCase->category->translations()->first();

        $categories = CommunityCaseCategory::where('is_active', true)
            ->with(['translations' => fn($q) => $q->where('locale', app()->getLocale())])
            ->get();

        return view('livewire.community-case-show', [
            'categoryTrans' => $categoryTrans,
            'categories' => $categories,
        ]);
    }
}
