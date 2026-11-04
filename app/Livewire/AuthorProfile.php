<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\ArticleTranslation;
use Livewire\Component;
use Livewire\WithPagination;

class AuthorProfile extends Component
{
    use WithPagination;

    public User $author;

    public function mount($id)
    {
        $this->author = User::withCount('authorFollowers')->findOrFail($id);
    }

    public function render()
    {
        // جلب المقالات المترجمة التابعة لهذا الكاتب وبحسب اللغة الحالية
        $articles = ArticleTranslation::where('user_id', $this->author->id)
            ->where('locale', app()->getLocale())
            ->with(['article', 'article.category.translations'])
            ->latest()
            ->paginate(9);

        return view('livewire.author-profile', [
            'articles' => $articles,
        ])->layout('layouts.main');
    }
}
