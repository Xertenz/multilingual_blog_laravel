<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Timeline;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class TimelineController extends Controller
{
    public function index(Request $request)
    {
        $articles = Article::with(['translations', 'category', 'timelines.translations'])
            ->latest()
            ->paginate(15);

        $selectedArticle = $request->filled('article_id')
            ? Article::with(['translations', 'timelines.translations'])->find($request->article_id)
            : null;

        $locales = [];
        $supportedLocales = LaravelLocalization::getSupportedLocales();
        foreach ($supportedLocales as $localeCode => $properties) {
            $locales[$localeCode] = $properties['native'] ?? $properties['name'];
        }

        return view('admin.article_timelines.index', compact('articles', 'selectedArticle', 'locales'));
    }

    public function store(Request $request, Article $article)
    {
        $request->validate([
            'event_date'                 => 'nullable|date',
            'event_year'                 => 'nullable|integer|min:1|max:9999',
            'event_month'                => 'nullable|integer|between:1,12',
            'sort_order'                 => 'nullable|integer|min:0',
            'translations'               => 'required|array',
            'translations.*.time_label'  => 'nullable|string|max:150',
            'translations.*.title'       => 'nullable|string|max:255',
            'translations.*.description' => 'nullable|string',
        ]);

        $hasAnyTitle = collect($request->input('translations', []))
            ->contains(fn($t) => !empty(trim($t['title'] ?? '')));

        if (!$hasAnyTitle) {
            return back()
                ->withInput()
                ->with('error', 'Please enter an event title in at least one language.');
        }

        DB::transaction(function () use ($request, $article) {
            $timeline = $article->timelines()->create([
                'event_date'  => $request->filled('event_date') ? $request->event_date : null,
                'event_year'  => $request->filled('event_year') ? (int) $request->event_year : null,
                'event_month' => $request->filled('event_month') ? (int) $request->event_month : null,
                'sort_order'  => (int) $request->input('sort_order', 0),
            ]);

            foreach ($request->input('translations', []) as $locale => $transData) {
                $title = trim($transData['title'] ?? '');
                $timeLabel = trim($transData['time_label'] ?? '');
                $description = trim($transData['description'] ?? '');

                if ($title !== '') {
                    $timeline->translations()->create([
                        'locale'      => $locale,
                        'time_label'  => $timeLabel !== '' ? $timeLabel : null,
                        'title'       => $title,
                        'description' => $description !== '' ? $description : null,
                    ]);
                }
            }
        });

        return back()->with('success', 'Timeline event and translations added successfully.');
    }

    public function update(Request $request, Timeline $timeline)
    {
        $request->validate([
            'event_date'                 => 'nullable|date',
            'event_year'                 => 'nullable|integer|min:1|max:9999',
            'event_month'                => 'nullable|integer|between:1,12',
            'sort_order'                 => 'nullable|integer|min:0',
            'translations'               => 'nullable|array',
            'translations.*.time_label'  => 'nullable|string|max:150',
            'translations.*.title'       => 'nullable|string|max:255',
            'translations.*.description' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $timeline) {
            $timeline->update([
                'event_date'  => $request->filled('event_date') ? $request->event_date : null,
                'event_year'  => $request->filled('event_year') ? (int) $request->event_year : null,
                'event_month' => $request->filled('event_month') ? (int) $request->event_month : null,
                'sort_order'  => (int) $request->input('sort_order', $timeline->sort_order),
            ]);

            if ($request->has('translations') && is_array($request->translations)) {
                foreach ($request->translations as $locale => $transData) {
                    $title = trim($transData['title'] ?? '');
                    $timeLabel = trim($transData['time_label'] ?? '');
                    $description = trim($transData['description'] ?? '');

                    if ($title !== '') {
                        $timeline->translations()->updateOrCreate(
                            ['locale' => $locale],
                            [
                                'time_label'  => $timeLabel !== '' ? $timeLabel : null,
                                'title'       => $title,
                                'description' => $description !== '' ? $description : null,
                            ]
                        );
                    } else {
                        $timeline->translations()->where('locale', $locale)->delete();
                    }
                }
            }
        });

        return back()->with('success', 'Timeline event and translations updated successfully.');
    }

    public function destroy(Timeline $timeline)
    {
        DB::transaction(function () use ($timeline) {
            $timeline->translations()->delete();
            $timeline->delete();
        });

        return back()->with('success', 'Timeline event deleted successfully.');
    }
}
