<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\GameCategory;
use App\Models\GameTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class GameController extends Controller
{
    public function index()
    {
        $games = Game::with(['translations', 'author', 'gameCategory.translations'])
            ->withCount(['media as content_imgs_count' => function ($q) {
                $q->where('collection_name', 'content_imgs');
            }])
            ->latest()
            ->paginate(10);

        $supportedLocales = \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getSupportedLocales();

        return view('admin.games.index', compact('games', 'supportedLocales'));
    }

    public function create(Request $request)
    {
        $cats = GameCategory::with('translations')->get();
        $tags = GameTag::with('translations')->get();
        return view('admin.games.create', compact('cats', 'tags'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'game_category_id' => 'required|numeric|exists:game_categories,id',
            'label'            => 'required|string|unique:games,label',
            'published_year'   => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'developer'        => 'nullable|string|max:255',
            'status'           => 'sometimes|in:draft,pending,published',
            'featured_img'     => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
            'tags'             => 'nullable|array',
            'tags.*'           => 'exists:game_tags,id',
        ]);

        $status = 'pending';
        if ($request->user()->hasRole(['super-admin', 'editor'])) {
            $status = $request->input('status', 'draft');
        }

        $game = Game::create([
            'game_category_id' => (int)$request->game_category_id,
            'user_id'          => Auth::id(),
            'label'            => trim($request->label),
            'published_year'   => $request->published_year,
            'developer'        => $request->developer,
            'status'           => $status,
        ]);

        if ($request->has('tags')) {
            $game->gameTags()->sync($request->tags);
        }

        if ($request->hasFile('featured_img')) {
            $file = $request->file('featured_img');
            $fileName = uniqid() . '.' . strtolower($file->getClientOriginalExtension());

            $game->addMedia($file)
                ->usingFileName($fileName)
                ->withResponsiveImages()
                ->toMediaCollection('cover_imgs');
        }

        return redirect()->route('admin.games.index')->with('success', 'Game Created Successfully.');
    }

    public function edit(Request $request, int $id)
    {
        $game = Game::with('gameCategory', 'gameTags')->findOrFail($id);
        $cats = GameCategory::with('translations')->get();
        $tags = GameTag::with('translations')->get();
        return view('admin.games.edit', compact('game', 'cats', 'tags'));
    }

    public function update(Request $request, int $id)
    {
        $game = Game::findOrFail($id);

        $previousStatus = $game->status;

        $request->validate([
            'game_category_id' => 'required|numeric|exists:game_categories,id',
            'label'            => ['required', 'string', Rule::unique('games', 'label')->ignore($id)],
            'published_year'   => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'developer'        => 'nullable|string|max:255',
            'status'           => 'sometimes|in:draft,pending,published',
            'featured_img'     => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
            'tags'             => 'nullable|array',
            'tags.*'           => 'exists:game_tags,id',
        ]);
        $status = $game->status;
        if ($request->user()->hasRole(['super-admin', 'editor'])) {
            $status = $request->input('status', $game->status);
        } else {
            $status = 'pending';
        }

        $game->update([
            'game_category_id' => (int)$request->game_category_id,
            'label'            => trim($request->label),
            'published_year'   => $request->published_year,
            'developer'        => $request->developer,
            'status'           => $status,
        ]);

        if ($request->hasFile('featured_img')) {
            if ($game->hasMedia('cover_imgs')) {
                $game->clearMediaCollection('cover_imgs');
            }
            $file = $request->file('featured_img');
            $fileName = uniqid() . '.' . strtolower($file->getClientOriginalExtension());

            $game->addMedia($file)
                ->usingFileName($fileName)
                ->withResponsiveImages()
                ->toMediaCollection('cover_imgs');
        }

        $game->gameTags()->sync($request->input('tags', []));

        // ✅ إشعار المتابعين فقط عند التحول إلى published
        if ($previousStatus !== 'published' && $game->status === 'published') {
            // \App\Jobs\SendNewGameNotificationJob::dispatch($game)->onQueue('default');
        }

        return redirect()->route('admin.games.index')->with('success', 'Game Updated Successfully.');
    }

    public function destroy(Request $request, int $id)
    {

        $game = Game::findOrFail($id);
        $game->clearMediaCollection('cover_imgs');
        $game->clearMediaCollection('content_imgs');
        $game->delete();

        return redirect()->route('admin.games.index')->with('success', 'Game Deleted Successfully.');
    }

    // رفع وإدارة صور المحتوى داخل TinyMCE
    public function uploadContentImg(Request $request)
    {

        $request->validate([
            'game_id'     => 'required|numeric|exists:games,id',
            'content_img' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048'
        ]);

        $game = Game::findOrFail($request->game_id);
        if ($request->hasFile('content_img')) {
            $file = $request->file('content_img');
            $fileName = uniqid() . '.' . strtolower($file->getClientOriginalExtension());

            $game->addMedia($file)
                ->usingFileName($fileName)
                ->withResponsiveImages()
                ->toMediaCollection('content_imgs');
        }

        return response()->json(['content_imgs' => $game->getMedia('content_imgs')]);
    }

    public function getGameContentImgs(Request $request)
    {
        $content_imgs = Game::findOrFail($request->game_id)->getMedia('content_imgs')->all();
        return response()->json($content_imgs);
    }

    public function deleteContentImg(Request $request)
    {
        $content_img = Media::find($request->content_img_id);
        if ($content_img) {
            $content_img->delete();
            return response()->json(['message' => 'media deleted successfully']);
        }
        return response()->json(['error' => 'error in delete media']);
    }


    public function pending(Request $request)
    {
        if (!$request->user()->hasRole(['super-admin', 'editor'])) {
            abort(403);
        }

        $games = Game::with(['translations', 'author'])
            ->where('status', 'pending')
            ->latest()
            ->paginate(10);

        return view('admin.games.pending', compact('games'));
    }

    public function approve(Request $request, int $id)
    {
        if (!$request->user()->hasRole(['super-admin', 'editor'])) {
            abort(403);
        }

        $game = Game::findOrFail($id);
        $game->update(['status' => 'published']);

        return redirect()->route('admin.games.pending')
            ->with('success', 'Game approved and published successfully.');
    }

    public function reject(Request $request, int $id)
    {
        if (!$request->user()->hasRole(['super-admin', 'editor'])) {
            abort(403);
        }

        $game = Game::findOrFail($id);
        $game->update(['status' => 'draft']);

        return redirect()->route('admin.games.pending')
            ->with('success', 'Game rejected and moved back to draft.');
    }
}
