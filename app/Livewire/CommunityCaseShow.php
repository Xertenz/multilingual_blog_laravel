<?php

namespace App\Livewire;

use App\Enums\CommunityCaseStatus;
use App\Models\CommunityCase;
use App\Models\CommunityCaseCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

    // مواقع القضية عند التعديل (متعدد)
    public array $edit_locations = [];

    public function mount(int $id, ?string $slug = null)
    {
        $this->communityCase = CommunityCase::where('id', $id)
            ->where('moderation_status', 'approved')
            ->with(['author', 'category.translations', 'locations.translations'])
            ->firstOrFail();

        $this->communityCase->increment('views_count');
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

        $this->edit_locations = $this->communityCase->locations->map(function ($loc) {
            $t = $loc->translation;
            return [
                'id'          => $loc->id,
                'title'       => $t?->title ?? '',
                'latitude'    => $loc->latitude,
                'longitude'   => $loc->longitude,
                'description' => $t?->description ?? '',
            ];
        })->values()->all();

        $this->edit_title                      = $this->communityCase->title;
        $this->edit_community_case_category_id = (string) $this->communityCase->community_case_category_id;
        $this->edit_body                       = $this->communityCase->body;
        $this->edit_case_status                = $this->communityCase->case_status?->value ?? $this->communityCase->case_status ?? 'open';

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

        $this->syncCaseLocations($this->communityCase, $this->edit_locations);

        $this->communityCase->load('locations.translations');
        $this->dispatch('case-locations-updated', locations: $this->formatCaseLocations());

        $this->reset(['edit_locations']);
        $this->edit_locations = [];

        $this->showEditModal = false;
        session()->flash('message', __('Your case has been updated successfully!'));
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

        DB::transaction(function () {
            foreach ($this->communityCase->locations as $location) {
                $location->translations()->delete();
                $location->delete();
            }

            $this->communityCase->votes()->delete();
            $this->communityCase->delete();
        });

        session()->flash('message', __('Case has been deleted successfully!'));

        return redirect()->route('community.cases.index');
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

        $locationsToDelete = empty($keptIds)
            ? $case->locations()->with('translations')->get()
            : $case->locations()->whereNotIn('id', $keptIds)->with('translations')->get();

        foreach ($locationsToDelete as $oldLoc) {
            $oldLoc->translations()->delete();
            $oldLoc->delete();
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

    public function vote(int $requestedVote)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (!in_array($requestedVote, [1, -1], true)) {
            return;
        }

        $userId = Auth::id();
        $existingVote = $this->communityCase->votes()
            ->where('user_id', $userId)
            ->first();

        if ($existingVote) {
            if ((int) $existingVote->vote === $requestedVote) {
                $existingVote->delete();
            } else {
                $existingVote->update(['vote' => $requestedVote]);
            }
        } else {
            $this->communityCase->votes()->create([
                'user_id' => $userId,
                'vote'    => $requestedVote,
            ]);
        }

        $this->communityCase->load('votes');
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
