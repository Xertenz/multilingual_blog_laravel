<?php

namespace App\Livewire;

use App\Enums\SuggestionStatus;
use App\Models\Suggestion;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class Suggestions extends Component
{
    use WithPagination;

    // حقول النموذج لإضافة أو تعديل اقتراح
    public $title = '';
    public $description = '';
    public ?int $editingSuggestionId = null;

    // الفرز والتصفية
    public $filter = 'popular'; // popular أو latest
    public string $selectedLocale;
    public $mySuggestionsOnly = false; // فلتر مقترحات العضو الحالي
    public $showModal = false;

    protected $rules = [
        'title'       => 'required|string|min:5|max:150',
        'description' => 'nullable|string|max:1000',
    ];

    public function mount()
    {
        // تعيين اللغة الحالية كقيمة افتراضية أولية عند فتح الصفحة
        $this->selectedLocale = LaravelLocalization::getCurrentLocale();
    }

    public function updatingFilter()
    {
        $this->resetPage();
    }

    public function updatingSelectedLocale()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $this->resetValidation();
        $this->reset(['title', 'description', 'editingSuggestionId']);
        $this->showModal = true;
    }

    public function openEditModal(int $suggestionId)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $suggestion = Suggestion::findOrFail($suggestionId);

        if ($suggestion->user_id !== Auth::id() || $suggestion->status !== SuggestionStatus::PENDING) {
            return;
        }

        $this->resetValidation();
        $this->editingSuggestionId = $suggestion->id;
        $this->title = $suggestion->title;
        $this->description = $suggestion->description ?? '';
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->resetValidation();
        $this->reset(['title', 'description', 'editingSuggestionId', 'showModal']);
    }

    public function toggleVote(int $suggestionId)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $suggestion = Suggestion::findOrFail($suggestionId);

        // التبديل بين إضافة وإلغاء التصويت
        $suggestion->votes()->toggle(Auth::id());
    }

    public function saveSuggestion()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $this->validate();

        $cleanDescription = trim((string) $this->description);
        $formattedDescription = $cleanDescription !== '' ? $cleanDescription : null;
        $formattedTitle = trim((string) $this->title);

        if ($this->editingSuggestionId !== null) {
            $suggestion = Suggestion::findOrFail($this->editingSuggestionId);

            if ($suggestion->user_id !== Auth::id() || $suggestion->status !== SuggestionStatus::PENDING) {
                $this->closeModal();
                return;
            }

            $suggestion->update([
                'title'       => $formattedTitle,
                'description' => $formattedDescription,
            ]);

            $this->closeModal();
            session()->flash('message', __('Suggestion updated successfully.') ?? 'تم تحديث اقتراحك بنجاح.');
            return;
        }

        Suggestion::create([
            'user_id'     => Auth::id(),
            'type'        => 'article',
            'locale'      => app()->getLocale(),
            'title'       => $formattedTitle,
            'description' => $formattedDescription,
            'status'      => SuggestionStatus::PENDING->value,
        ]);

        $this->closeModal();
        session()->flash('message', __('nav.suggestion_submitted_success') ?? 'تم تقديم اقتراحك بنجاح وهو قيد المراجعة.');
    }

    public function deleteSuggestion(int $suggestionId)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $suggestion = Suggestion::findOrFail($suggestionId);

        if ($suggestion->user_id !== Auth::id() || $suggestion->status !== SuggestionStatus::PENDING) {
            return;
        }

        if ($this->editingSuggestionId === $suggestion->id) {
            $this->closeModal();
        }

        $suggestion->delete();
        session()->flash('message', __('Suggestion deleted successfully.') ?? 'تم حذف اقتراحك بنجاح.');
    }

    public function render()
    {
        $query = Suggestion::query()
            ->with(['author', 'votes'])
            ->withCount('votes');

        // 1. فلترة مقترحات العضو الحالي فقط إذا تم تفعيل الخيار
        if ($this->mySuggestionsOnly && Auth::check()) {
            $query->where('user_id', Auth::id());
        }

        // 2. التصفية حسب اللغة فقط إذا تم اختيار لغة محددة ولم تكن 'all'
        if ($this->selectedLocale !== 'all') {
            $query->where('locale', $this->selectedLocale);
        }

        // 3. الترتيب
        if ($this->filter === 'popular') {
            $query->orderBy('votes_count', 'desc')->orderBy('created_at', 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $suggestions = $query->paginate(10);

        return view('livewire.suggestions', [
            'suggestions' => $suggestions,
            'locales'     => LaravelLocalization::getSupportedLocales(),
        ]);
    }
}
