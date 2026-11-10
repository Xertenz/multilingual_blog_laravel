<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\ArticleTranslation;
use App\Models\BookTranslation;
use App\Models\GameTranslation;
use App\Models\MediaSummary;
use App\Models\MediaSummaryTranslation; // أو اسم نموذج ترجمة الميديا لديك
use Livewire\Component;
use Livewire\WithPagination;

class AuthorProfile extends Component
{
    use WithPagination;

    public User $author;
    public string $tab = 'articles';

    protected $queryString = ['tab'];

    public function setTab(string $tab)
    {
        $this->tab = $tab;
        $this->resetPage();
    }

    public function mount(int $id)
    {
        $this->author = User::withCount('authorFollowers')->findOrFail($id);
    }

    public function render()
    {
        $currentLocale = app()->getLocale();

        // 1. المقالات
        $articles = ArticleTranslation::where('user_id', $this->author->id)
            ->where('locale', $currentLocale)
            ->with(['article.category.translations', 'article.media'])
            ->latest()
            ->paginate(9, ['*'], 'articlesPage');

        // 2. الكتب
        $books = BookTranslation::where('user_id', $this->author->id)
            ->where('locale', $currentLocale)
            ->with(['book.bookCategory.translations', 'book.media'])
            ->latest()
            ->paginate(9, ['*'], 'booksPage');

        // 3. الألعاب
        $games = GameTranslation::where('user_id', $this->author->id)
            ->where('locale', $currentLocale)
            ->with(['game.gameCategory.translations', 'game.media'])
            ->latest()
            ->paginate(9, ['*'], 'gamesPage');

        // 4. ميديا الأفلام والمسلسلات والوثائقيات (تأكد من مطابقة اسم النموذج لديك إن لم يكن MediaItemTranslation)
        // ملاحظة: إذا كنت تستخدم جدول ترجمات ميديا آخر، قم بتعديل اسم الموديل بما يتوافق مع هيكلتك
        $mediaItems = \App\Models\MediaSummaryTranslation::where('user_id', $this->author->id)
            ->where('locale', $currentLocale)
            ->with(['mediaSummary.media']) // 👈 جلب الأب والوسائط المرتبطة به
            ->latest()
            ->paginate(9, ['*'], 'mediaPage');

        // إجمالي عدد الإسهامات الشامل
        $totalContributions = $articles->total() + $books->total() + $games->total() + $mediaItems->total();

        return view('livewire.author-profile', [
            'articles'   => $articles,
            'books'      => $books,
            'games'      => $games,
            'mediaItems' => $mediaItems,
            'totalContributions' => $totalContributions,
        ])->layout('layouts.main');
    }
}
