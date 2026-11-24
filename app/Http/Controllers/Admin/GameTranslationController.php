<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ContentStatus;
use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\GameTranslation;
use App\Services\ContentNotificationDispatcher;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class GameTranslationController extends Controller
{
    public function create(Request $request, int $game_id)
    {
        $game = Game::with('translations')->findOrFail($game_id);
        $game_content_imgs = $game->getMedia('content_imgs');

        $supportedLocales = LaravelLocalization::getSupportedLocales();
        $existingLocales = $game->translations->pluck('locale')->toArray();
        $selectedLocale = $request->query('locale');

        return view('admin.games.create-trans', compact(
            'game_id',
            'game',
            'game_content_imgs',
            'supportedLocales',
            'existingLocales',
            'selectedLocale'
        ));
    }

    public function store(Request $request)
    {
        $supportedLocales = implode(',', LaravelLocalization::getSupportedLanguagesKeys());

        $request->validate([
            'game_id'          => 'required|numeric|exists:games,id',
            'locale'           => [
                'required',
                'in:' . $supportedLocales,
                Rule::unique('game_translations', 'locale')->where(fn($q) => $q->where('game_id', (int) $request->game_id))
            ],
            'slug'             => [
                'required',
                'string',
                Rule::unique('game_translations', 'slug')->where(fn($q) => $q->where('locale', $request->locale))
            ],
            'title'            => 'required|string|min:3',
            'content'          => 'required|string|min:20',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'status'           => ['sometimes', new Enum(ContentStatus::class)],
            'affiliate_url'    => 'nullable|url',
            'price'            => 'nullable|numeric|min:0',
            'currency'         => 'nullable|string|max:10',
        ]);

        // ✅ الكاتب → pending، المحرر/المدير → حسب الاختيار
        $status = ContentStatus::PENDING->value;
        if ($request->user()->hasRole(['super-admin', 'editor']) || $request->user()->can('publish-articles')) {
            $status = $request->input('status', ContentStatus::DRAFT->value);
        }

        $slugSource = trim($request->input('slug')) !== '' ? $request->input('slug') : $request->input('title');
        $finalSlug = (string) Str::of($slugSource)->slug('-');
        $request->merge(['slug' => $finalSlug]);

        $game_trans = GameTranslation::create([
            'game_id'          => $request->game_id,
            'user_id'          => $request->user()->id,
            'locale'           => $request->locale,
            'slug'             => trim($request->slug),
            'title'            => trim($request->title),
            'content'          => trim($request->content),
            'meta_title'       => trim($request->meta_title ?? $request->title),
            'meta_description' => trim($request->meta_description ?? Str::limit(strip_tags($request->content), 150)),
            'status'           => $status,
            'affiliate_url'    => $request->affiliate_url,
            'price'            => $request->price,
            'currency'         => $request->currency ?? 'USD',
        ]);

        app(ContentNotificationDispatcher::class)->dispatchIfReady(
            translation: $game_trans,
            contentType: 'game',
            translationTable: 'game_translations',
            parentRelation: 'game',
            parentTable: 'games',
            parentForeignKey: 'game_id',
        );

        return redirect()->route('admin.games.index')->with('success', 'Game Translation Added Successfully.');
    }

    public function edit(Request $request, int $id)
    {
        $game_trans = GameTranslation::with('game')->findOrFail($id);
        $game = $game_trans->game;

        if (!$request->user()->hasRole(['super-admin', 'editor']) && $game_trans->user_id !== $request->user()->id) {
            abort(403, 'غير مسموح لك بتعديل ترجمة لم تقم بإنشائها.');
        }

        $game_content_imgs = $game_trans->game->getMedia('content_imgs'); // 👈 جلب صور المعرض للتعديل
        return view('admin.games.edit-trans', compact('game_trans', 'game', 'game_content_imgs'));
    }

    public function update(Request $request, int $id)
    {
        $game_trans = GameTranslation::findOrFail($id);

        if (!$request->user()->hasRole(['super-admin', 'editor']) && $game_trans->user_id !== $request->user()->id) {
            abort(403, 'غير مسموح لك بتحديث ترجمة لم تقم بإنشائها.');
        }

        $supportedLocales = implode(',', LaravelLocalization::getSupportedLanguagesKeys());

        $request->validate([
            'game_id'          => 'required|numeric|exists:games,id',
            'locale'           => [
                'required',
                'in:' . $supportedLocales,
                Rule::unique('game_translations')->where(fn($q) => $q->where('game_id', (int) $request->game_id))->ignore($id)
            ],
            'slug'             => [
                'required',
                'string',
                Rule::unique('game_translations')->where(fn($q) => $q->where('locale', $request->locale))->ignore($id)
            ],
            'title'            => 'required|string|min:3',
            'content'          => 'required|string|min:20',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'status'           => ['sometimes', new Enum(ContentStatus::class)],
            'affiliate_url'    => 'nullable|url',
            'price'            => 'nullable|numeric|min:0',
            'currency'         => 'nullable|string|max:10',
        ]);

        // ✅ الكاتب: التعديل يعيد الحالة إلى pending
        $status = $game_trans->status?->value ?? (string) $game_trans->status;
        if ($request->user()->hasRole(['super-admin', 'editor']) || $request->user()->can('publish-articles')) {
            $status = $request->input('status', $status);
        } else {
            $status = ContentStatus::PENDING->value;
        }

        $slugSource = trim($request->input('slug')) !== '' ? $request->input('slug') : $request->input('title');
        $finalSlug = (string) Str::of($slugSource)->slug('-');
        $request->merge(['slug' => $finalSlug]);

        $game_trans->update([
            'locale'           => $request->locale,
            'slug'             => trim($request->slug),
            'title'            => trim($request->title),
            'content'          => trim($request->content),
            'meta_title'       => trim($request->meta_title ?? $request->title),
            'meta_description' => trim($request->meta_description ?? Str::limit(strip_tags($request->content), 150)),
            'status'           => $status,
            'affiliate_url'    => $request->affiliate_url,
            'price'            => $request->price,
            'currency'         => $request->currency ?? 'USD',
        ]);

        app(ContentNotificationDispatcher::class)->dispatchIfReady(
            translation: $game_trans,
            contentType: 'game',
            translationTable: 'game_translations',
            parentRelation: 'game',
            parentTable: 'games',
            parentForeignKey: 'game_id',
        );

        return redirect()->route('admin.games.index')->with('success', 'Game Translation Updated Successfully.');
    }

    public function destroy(Request $request, int $id)
    {
        $game_trans = GameTranslation::findOrFail($id);

        if (!$request->user() || !$request->user()->hasRole(['super-admin', 'editor', 'writer'])) {
            abort(403, 'غير مسموح لك بحذف الترجمات.');
        }

        if (!$request->user()->hasRole(['super-admin', 'editor']) && $game_trans->user_id !== $request->user()->id) {
            abort(403, 'غير مسموح لك بحذف ترجمة لم تقم بإنشائها.');
        }

        $game_trans->delete();
        return redirect()->route('admin.games.index')->with('success', 'Game Translation Deleted Successfully.');
    }
}
