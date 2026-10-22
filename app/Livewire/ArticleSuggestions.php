<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ArticleSuggestion;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class ArticleSuggestions extends Component
{
    use WithPagination;

    // حقول النموذج لإضافة اقتراح جديد
    public $title = '';
    public $description = '';

    // الفرز والتصفية
    public $filter = 'popular'; // popular أو latest
    #public $selectedLocale = 'all';
    public $selectedLocale;
    public $mySuggestionsOnly = false; // فلتر مقترحات العضو الحالي
    public $showModal = false;

    protected $rules = [
        'title' => 'required|string|min:5|max:150',
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

    public function toggleVote($suggestionId)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $suggestion = ArticleSuggestion::findOrFail($suggestionId);

        // التبديل بين إضافة وإلغاء التصويت
        $suggestion->votes()->toggle(Auth::id());
    }

    public function createSuggestion()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $this->validate();

        ArticleSuggestion::create([
            'user_id' => Auth::id(),
            'locale' => app()->getLocale(),
            'title' => $this->title,
            'description' => $this->description,
            'status' => 'pending',
        ]);

        $this->reset(['title', 'description', 'showModal']);
        session()->flash('message', __('nav.suggestion_submitted_success') ?? 'تم تقديم اقتراحك بنجاح وهو قيد المراجعة.');
    }

    public function render()
    {
        $query = ArticleSuggestion::query()
            ->with(['user', 'votes'])
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

        return view('livewire.article-suggestions', [
            'suggestions' => $suggestions,
            'locales' => LaravelLocalization::getSupportedLocales()
        ]);
    }
}
