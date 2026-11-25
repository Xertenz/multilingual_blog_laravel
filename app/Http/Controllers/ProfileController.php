<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Article;
use App\Models\Book;
use App\Models\Game;
use App\Models\MediaSummary;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */


    public function savedItems(Request $request)
    {
        $user = auth()->user();
        $type = $request->query('type', 'all');
        $allowedTypes = ['article', 'book', 'game', 'media_summary'];

        if (!in_array($type, array_merge(['all'], $allowedTypes), true)) {
            $type = 'all';
        }

        $items = collect();

        if ($type === 'all' || $type === 'article') {
            $articles = $user->savedArticles()
                ->with(['translations', 'category.translations', 'media'])
                ->get()
                ->map(fn($model) => $this->wrapSavedModel($model, 'article'));

            $items = $items->concat($articles);
        }

        if ($type === 'all' || $type === 'book') {
            $books = $user->savedBooks()
                ->with(['translations', 'author', 'category.translations', 'media'])
                ->get()
                ->map(fn($model) => $this->wrapSavedModel($model, 'book'));

            $items = $items->concat($books);
        }

        if ($type === 'all' || $type === 'game') {
            $games = $user->savedGames()
                ->with(['translations', 'category.translations', 'media'])
                ->get()
                ->map(fn($model) => $this->wrapSavedModel($model, 'game'));

            $items = $items->concat($games);
        }

        if ($type === 'all' || $type === 'media_summary') {
            $mediaSummaries = $user->savedMediaSummaries()
                ->with(['translations', 'category.translations', 'media'])
                ->get()
                ->map(fn($model) => $this->wrapSavedModel($model, 'media_summary'));

            $items = $items->concat($mediaSummaries);
        }

        // ترتيب الأحدث حفظاً أولاً بناءً على created_at في جدول saved_items (pivot)
        $sorted = $items->sortByDesc(function (array $entry) {
            return optional($entry['saved_at'])->timestamp ?? 0;
        })->values();

        $perPage = 12;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $sorted->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $savedItems = new LengthAwarePaginator(
            $currentItems,
            $sorted->count(),
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        $userLocale = LaravelLocalization::getCurrentLocale()
            ?? ($user->locale ?? app()->getLocale());

        return view('profile.saved-items', [
            'savedItems' => $savedItems,
            'activeType' => $type,
            'userLocale' => $userLocale,
        ]);
    }

    private function wrapSavedModel($model, string $type): array
    {
        return [
            'type' => $type,
            'model' => $model,
            'saved_at' => $model->pivot->created_at ?? $model->created_at,
        ];
    }


    public function following(Request $request)
    {
        $user = auth()->user();
        $type = $request->query('type', 'all');
        $allowedTypes = [
            'author',
            'article_category',
            'article_tag',
            'book_category',
            'book_tag',
            'game_category',
            'game_tag',
            'media_summary_category',
            'media_summary_tag',
        ];

        if (!in_array($type, array_merge(['all'], $allowedTypes), true)) {
            $type = 'all';
        }

        $items = collect();

        if ($type === 'all' || $type === 'author') {
            $authors = $user->followingAuthors()
                ->with(['media'])
                ->get()
                ->map(fn($model) => $this->wrapFollowModel($model, 'author'));

            $items = $items->concat($authors);
        }

        if ($type === 'all' || $type === 'article_category') {
            $articleCategories = $user->followedArticleCategories()
                ->with(['translations', 'media'])
                ->get()
                ->map(fn($model) => $this->wrapFollowModel($model, 'article_category'));

            $items = $items->concat($articleCategories);
        }

        if ($type === 'all' || $type === 'article_tag') {
            $articleTags = $user->followedArticleTags()
                ->with(['translations'])
                ->get()
                ->map(fn($model) => $this->wrapFollowModel($model, 'article_tag'));

            $items = $items->concat($articleTags);
        }

        if ($type === 'all' || $type === 'book_category') {
            $bookCategories = $user->followedBookCategories()
                ->with(['translations', 'media'])
                ->get()
                ->map(fn($model) => $this->wrapFollowModel($model, 'book_category'));

            $items = $items->concat($bookCategories);
        }

        if ($type === 'all' || $type === 'book_tag') {
            $bookTags = $user->followedBookTags()
                ->with(['translations'])
                ->get()
                ->map(fn($model) => $this->wrapFollowModel($model, 'book_tag'));

            $items = $items->concat($bookTags);
        }

        if ($type === 'all' || $type === 'game_category') {
            $gameCategories = $user->followedGameCategories()
                ->with(['translations', 'media'])
                ->get()
                ->map(fn($model) => $this->wrapFollowModel($model, 'game_category'));

            $items = $items->concat($gameCategories);
        }

        if ($type === 'all' || $type === 'game_tag') {
            $gameTags = $user->followedGameTags()
                ->with(['translations'])
                ->get()
                ->map(fn($model) => $this->wrapFollowModel($model, 'game_tag'));

            $items = $items->concat($gameTags);
        }

        if ($type === 'all' || $type === 'media_summary_category') {
            $mediaSummaryCategories = $user->followedMediaSummaryCategories()
                ->with(['translations', 'media'])
                ->get()
                ->map(fn($model) => $this->wrapFollowModel($model, 'media_summary_category'));

            $items = $items->concat($mediaSummaryCategories);
        }

        if ($type === 'all' || $type === 'media_summary_tag') {
            $mediaSummaryTags = $user->followedMediaSummaryTags()
                ->with(['translations'])
                ->get()
                ->map(fn($model) => $this->wrapFollowModel($model, 'media_summary_tag'));

            $items = $items->concat($mediaSummaryTags);
        }

        $sorted = $items->sortByDesc(function (array $entry) {
            return optional($entry['followed_at'])->timestamp ?? 0;
        })->values();

        $perPage = 12;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $sorted->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $followingItems = new LengthAwarePaginator(
            $currentItems,
            $sorted->count(),
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        $userLocale = LaravelLocalization::getCurrentLocale()
            ?? ($user->locale ?? app()->getLocale());

        return view('profile.following', [
            'followingItems' => $followingItems,
            'activeType' => $type,
            'userLocale' => $userLocale,
        ]);
    }

    private function wrapFollowModel($model, string $type): array
    {
        return [
            'type' => $type,
            'model' => $model,
            'followed_at' => $model->pivot->created_at ?? $model->created_at,
        ];
    }


















    /*
    public function favoriteCategories(Request $request)
    {
        $currentLocale = LaravelLocalization::getCurrentLocale();

        // جلب المستخدم الحالي مع فئاته المفضلة، تراجعمها باللغة الحالية، وصورها البارزة لمنع N+1
        $user = $request->user()->load([
            'favoriteCategories.translations' => function ($query) use ($currentLocale) {
                $query->where('locale', $currentLocale);
            },
            'favoriteCategories.media' // 👈 جلب وسائط الفئات (featured_imgs)
        ]);

        return view('profile.favorite-categories', [
            'favoriteCategories' => $user->favoriteCategories,
        ]);
    }

    public function savedArticles(Request $request)
    {
        $currentLocale = LaravelLocalization::getCurrentLocale();

        $user = $request->user()->load([
            'savedArticles.translations' => function ($query) use ($currentLocale) {
                $query->where('locale', $currentLocale);
            },
            'savedArticles.media',                  // 👈 الصور البارزة للمقال (featured_imgs)
            'savedArticles.category.translations' => function ($query) use ($currentLocale) {
                $query->where('locale', $currentLocale);
            }
        ]);

        return view('profile.saved-articles', [
            'savedArticles' => $user->savedArticles,
        ]);
    }

    public function followingAuthors(Request $request)
    {
        // جلب الكتّاب المتابَعين مع صور الـ Avatar الخاصة بهم لمنع N+1
        $user = $request->user()->load('followingAuthors.media');

        return view('profile.following-authors', [
            'followingAuthors' => $user->followingAuthors,
        ]);
    }
    */

    public function edit(Request $request): View
    {
        $user = $request->user();
        $profile = $user->profile()->firstOrCreate([
            'user_id' => $user->id,
        ]);

        return view('profile.edit', [
            'user' => $user,
            'profile' => $profile,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = $request->user();
        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        if ($request->hasFile('avatar')) {
            $user->clearMediaCollection('avatar_imgs');
            $user->addMediaFromRequest('avatar')->toMediaCollection('avatar_imgs');
        }

        $socialLinks = collect($validated['social_links'] ?? [])
            ->map(fn($url) => is_string($url) ? trim($url) : null)
            ->filter(fn($url) => !empty($url))
            ->all();

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'bio' => $validated['bio'] ?? null,
                'location' => $validated['location'] ?? null,
                'website' => $validated['website'] ?? null,
                'social_links' => !empty($socialLinks) ? $socialLinks : null,
            ]
        );

        return Redirect::route('profile.edit')
            ->with('status', 'profile-updated')
            ->with('success', __('Profile updated successfully.'));
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
