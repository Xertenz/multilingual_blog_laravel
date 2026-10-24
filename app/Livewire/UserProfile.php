<?php

namespace App\Livewire;

use App\Models\CategoryTranslation;
use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\App;

class UserProfile extends Component
{
    public User $user;
    public string $activeTab = 'articles'; // التبويب الافتراضي

    public function mount(int $id)
    {
        $this->user = User::with([
            'savedArticles.translations',
            'favoriteCategories.translations',
            // استخدام morphWith لجلب علاقات الـ Morph حسب نوع الكلاس
            'comments.commentable' => function ($morphTo) {
                $morphTo->morphWith([
                    \App\Models\Article::class => ['translations'],
                    \App\Models\CommunityCase::class => [], // إذا كنت تريد جلب بيانات القضايا أيضاً إن وجدت
                ]);
            },
            'votedSuggestions.votes'
        ])->findOrFail($id);
    }

    public function switchTab(string $tab)
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        return view('livewire.user-profile');
    }

    public function getCatTrans(object $category)
    {
        $cat_trans = $category->translations->where('locale', App::getLocale())->first();
        if ($cat_trans)
            return $cat_trans;
        else
            return $category->translations->first();
    }
    public function getArticleTrans(object $article)
    {

        $article_trans = $article->translations->where('locale', App::getLocale())->first();
        if ($article_trans)
            return $article_trans;
        else
            return $article->translations->first();
    }
}
