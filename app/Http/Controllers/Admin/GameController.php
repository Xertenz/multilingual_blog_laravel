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
        $games = Game::with(['translations', 'user', 'gameCategory.translations'])->latest()->paginate(10);
        return view('admin.games.index', compact('games'));
    }

    public function create(Request $request)
    {
        if (!$request->user()->hasRole(['super-admin', 'editor'])) {
            abort(403, 'غير مسموح لك بإنشاء الألعاب الأساسية.');
        }

        $cats = GameCategory::with('translations')->get();
        $tags = GameTag::with('translations')->get();
        return view('admin.games.create', compact('cats', 'tags'));
    }

    public function store(Request $request)
    {
        if (!$request->user()->hasRole(['super-admin', 'editor'])) {
            abort(403, 'غير مسموح لك بإنشاء الألعاب الأساسية.');
        }

        $request->validate([
            'game_category_id' => 'required|numeric|exists:game_categories,id',
            'internal_title'   => 'required|string|unique:games,internal_title',
            'published_year'   => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'developer'        => 'nullable|string|max:255',
            'featured_img'     => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
            'tags'             => 'nullable|array',
            'tags.*'           => 'exists:game_tags,id',
        ]);

        $game = Game::create([
            'game_category_id' => (int)$request->game_category_id,
            'user_id'          => Auth::id(),
            'internal_title'   => trim($request->internal_title),
            'published_year'   => $request->published_year,
            'developer'        => $request->developer,
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
        if (!$request->user()->hasRole(['super-admin', 'editor'])) {
            abort(403, 'غير مسموح لك بتعديل الألعاب الأساسية.');
        }

        $game = Game::with('gameCategory', 'gameTags')->findOrFail($id);
        $cats = GameCategory::with('translations')->get();
        $tags = GameTag::with('translations')->get();
        return view('admin.games.edit', compact('game', 'cats', 'tags'));
    }

    public function update(Request $request, int $id)
    {
        if (!$request->user()->hasRole(['super-admin', 'editor'])) {
            abort(403, 'غير مسموح لك بتحديث الألعاب الأساسية.');
        }

        $game = Game::findOrFail($id);

        $request->validate([
            'game_category_id' => 'required|numeric|exists:game_categories,id',
            'internal_title'   => ['required', 'string', Rule::unique('games', 'internal_title')->ignore($id)],
            'published_year'   => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'developer'        => 'nullable|string|max:255',
            'featured_img'     => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
            'tags'             => 'nullable|array',
            'tags.*'           => 'exists:game_tags,id',
        ]);

        $game->update([
            'game_category_id' => (int)$request->game_category_id,
            'internal_title'   => trim($request->internal_title),
            'published_year'   => $request->published_year,
            'developer'        => $request->developer,
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

        return redirect()->route('admin.games.index')->with('success', 'Game Updated Successfully.');
    }

    public function destroy(Request $request, int $id)
    {
        if (!$request->user()->hasRole(['super-admin', 'editor'])) {
            abort(403, 'غير مسموح لك بحذف الألعاب.');
        }

        $game = Game::findOrFail($id);
        $game->clearMediaCollection('cover_imgs');
        $game->clearMediaCollection('content_imgs');
        $game->delete();

        return redirect()->route('admin.games.index')->with('success', 'Game Deleted Successfully.');
    }

    // رفع وإدارة صور المحتوى داخل TinyMCE
    public function uploadContentImg(Request $request)
    {
        if (!$request->user()->hasRole(['super-admin', 'editor', 'writer'])) {
            abort(403, 'غير مسموح لك برفع صور المحتوى.');
        }

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
}
