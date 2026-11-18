<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ContentStatus;
use App\Http\Controllers\Controller;
use App\Models\MediaSummary;
use App\Models\MediaSummaryTranslation;
use DOMDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaSummaryTranslationController extends Controller
{
    public function create(Request $request, int $media_summary_id)
    {
        if (!$request->user() || !$request->user()->hasRole(['super-admin', 'editor', 'writer'])) {
            abort(403, 'غير مسموح لك بالوصول إلى هذه الصفحة.');
        }

        $mediaSummary = MediaSummary::with('translations')->findOrFail($media_summary_id);
        $article_content_imgs = $mediaSummary->getMedia('content_imgs');
        $selectedLocale = $request->query('locale');

        return view('admin.media_summaries.create-trans', compact(
            'mediaSummary',
            'media_summary_id',
            'article_content_imgs',
            'selectedLocale'
        ));
    }

    public function store(Request $request)
    {
        if (!$request->user() || !$request->user()->hasRole(['super-admin', 'editor', 'writer'])) {
            abort(403, 'غير مسموح لك بإنشاء ترجمة جديدة.');
        }

        $status = 'pending';
        if ($request->user()->hasRole(['super-admin', 'editor']) || $request->user()->can('publish-articles')) {
            $status = $request->input('status', 'draft');
        }

        $slugSource = trim($request->input('slug')) !== '' ? $request->input('slug') : $request->input('title');
        $finalSlug = (string) Str::of($slugSource)->slug('-');
        $request->merge(['slug' => $finalSlug]);

        $supportedLocales = implode(',', LaravelLocalization::getSupportedLanguagesKeys());

        $request->validate([
            'media_summary_id' => 'required|integer|exists:media_summaries,id',
            'locale' => [
                'required',
                'string',
                'in:' . $supportedLocales,
                Rule::unique('media_summary_translations', 'locale')->where(function ($query) use ($request) {
                    return $query->where('media_summary_id', (int) $request->media_summary_id);
                }),
            ],
            'slug' => [
                'required',
                'string',
                Rule::unique('media_summary_translations', 'slug')->where(function ($query) use ($request) {
                    return $query->where('locale', $request->locale);
                }),
            ],
            'title' => 'required|string|min:3',
            'synopsis' => 'nullable|string',
            'review_analysis' => 'nullable|string',
            'meta_title' => 'required|string|min:3',
            'meta_description' => 'required|string|min:20',
            'status' => 'sometimes|in:draft,pending,published',
        ]);

        try {
            $trans = MediaSummaryTranslation::create([
                'media_summary_id' => (int) $request->media_summary_id,
                'user_id' => Auth::id(),
                'locale' => $request->locale,
                'title' => trim($request->title),
                'slug' => $finalSlug,
                'synopsis' => $request->filled('synopsis') ? trim($request->synopsis) : null,
                'review_analysis' => $request->filled('review_analysis') ? trim($request->review_analysis) : null,
                'meta_title' => trim($request->meta_title),
                'meta_description' => trim($request->meta_description),
                'status' => $status,
                'views' => 0,
            ]);

            $summary = $trans->mediaSummary;
            if ($summary && $summary->status === 'published' && $trans->status === 'published' && $summary->category) {
                \App\Jobs\SendAuthorNewContentJob::dispatch($trans, 'media_summary')->onQueue('default');
            }

            return to_route('admin.media-summaries.index')
                ->with('success', 'Media Translation Added Successfully.');
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()
                ->with('error', 'Failed to add translation: ' . $e->getMessage());
        }
    }

    public function edit(Request $request, int $id)
    {
        $trans = MediaSummaryTranslation::with('mediaSummary.media')->findOrFail($id);
        $mediaSummary = $trans->mediaSummary;

        if (!$request->user() || !$request->user()->hasRole(['super-admin', 'editor', 'writer'])) {
            abort(403);
        }
        if (!$request->user()->hasRole(['super-admin', 'editor']) && $trans->user_id !== $request->user()->id) {
            abort(403);
        }

        $article_content_imgs = $mediaSummary->getMedia('content_imgs');

        return view('admin.media_summaries.edit-trans', compact('trans', 'mediaSummary', 'article_content_imgs'));
    }

    public function update(Request $request, int $id)
    {
        $trans = MediaSummaryTranslation::findOrFail($id);

        if (!$request->user() || !$request->user()->hasRole(['super-admin', 'editor', 'writer'])) {
            abort(403);
        }
        if (!$request->user()->hasRole(['super-admin', 'editor']) && $trans->user_id !== $request->user()->id) {
            abort(403);
        }

        $previousStatus = $trans->status?->value ?? $trans->status;

        $status = $trans->status?->value ?? $trans->status;
        if ($request->user()->hasRole(['super-admin', 'editor']) || $request->user()->can('publish-articles')) {
            $status = $request->input('status', $status);
        } else {
            $status = 'pending';
        }

        $slugSource = trim($request->input('slug')) !== '' ? $request->input('slug') : $request->input('title');
        $finalSlug = (string) Str::of($slugSource)->slug('-');
        $request->merge(['slug' => $finalSlug]);

        $supportedLocales = implode(',', LaravelLocalization::getSupportedLanguagesKeys());

        $request->validate([
            'locale' => [
                'required',
                'string',
                'in:' . $supportedLocales,
                Rule::unique('media_summary_translations', 'locale')->where(function ($query) use ($trans) {
                    return $query->where('media_summary_id', $trans->media_summary_id);
                })->ignore($id),
            ],
            'slug' => [
                'required',
                'string',
                Rule::unique('media_summary_translations', 'slug')->where(function ($query) use ($request) {
                    return $query->where('locale', $request->locale);
                })->ignore($id),
            ],
            'title' => 'required|string|min:3',
            'synopsis' => 'nullable|string',
            'review_analysis' => 'nullable|string',
            'meta_title' => 'required|string|min:3',
            'meta_description' => 'required|string|min:20',
            'status' => 'sometimes|in:draft,pending,published',
        ]);

        try {
            $trans->update([
                'locale' => $request->locale,
                'title' => trim($request->title),
                'slug' => $finalSlug,
                'synopsis' => $request->filled('synopsis') ? trim($request->synopsis) : null,
                'review_analysis' => $request->filled('review_analysis') ? trim($request->review_analysis) : null,
                'meta_title' => trim($request->meta_title),
                'meta_description' => trim($request->meta_description),
                'status' => $status,
            ]);

            // ✅ إشعار المتابعين فقط عند التحول من غير منشور → منشور
            $becamePublished = ($previousStatus !== 'published' && ($trans->status?->value ?? $trans->status) === 'published');
            if (
                $becamePublished
                && $trans->mediaSummary
                && ($trans->mediaSummary->status?->value ?? $trans->mediaSummary->status) === 'published'
                && $trans->mediaSummary->category
            ) {
                // \App\Jobs\SendNewArticleNotificationJob::dispatch($trans)->onQueue('default');
            }

            return to_route('admin.media-summaries.index')
                ->with('success', 'Media Translation Updated Successfully.');
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()
                ->with('error', 'Failed to update translation: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request, int $id)
    {
        $trans = MediaSummaryTranslation::findOrFail($id);

        if (!$request->user() || !$request->user()->hasRole(['super-admin', 'editor', 'writer'])) {
            abort(403);
        }
        if (!$request->user()->hasRole(['super-admin', 'editor']) && $trans->user_id !== $request->user()->id) {
            abort(403);
        }

        $trans->delete();
        return to_route('admin.media-summaries.index')
            ->with('success', 'Media Translation Deleted Successfully.');
    }
}
