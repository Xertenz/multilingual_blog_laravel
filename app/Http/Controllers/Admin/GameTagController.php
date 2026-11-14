<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GameTag;
use App\Models\GameTagTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class GameTagController extends Controller
{
    public function index()
    {
        $tags = GameTag::with('translations')->paginate(10);
        return view('admin.game_tags.index', compact('tags'));
    }

    public function create()
    {
        $supportedLocales = LaravelLocalization::getSupportedLocales();
        return view('admin.game_tags.create', compact('supportedLocales'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'label'                           => 'required|string|unique:game_tags,label|max:50',
            'translations.*.name'             => 'nullable|string|max:50',
            'translations.*.slug'             => 'nullable|string|max:50',
            'translations.*.description'      => 'nullable|string|max:1000',
            'translations.*.meta_title'       => 'nullable|string|max:70',
            'translations.*.meta_description' => 'nullable|string|max:160',
        ]);

        $tag = GameTag::create([
            'user_id' => Auth::id(),
            'label'   => Str::slug($request->label, '-'),
        ]);

        foreach ($request->translations as $localeCode => $data) {
            if (!empty($data['name'])) {
                $slug = !empty($data['slug'])
                    ? Str::slug($data['slug'], '-')
                    : Str::slug($data['name'], '-', null);

                GameTagTranslation::create([
                    'game_tag_id'      => $tag->id,
                    'locale'           => $localeCode,
                    'name'             => trim($data['name']),
                    'slug'             => $slug,
                    'description'      => $data['description'] ?? null,
                    'meta_title'       => $data['meta_title'] ?? null,
                    'meta_description' => $data['meta_description'] ?? null,
                ]);
            }
        }

        return to_route('admin.game-tags.index')->with('success', 'Game Tag added successfully.');
    }

    public function edit(GameTag $tag)
    {
        $supportedLocales = LaravelLocalization::getSupportedLocales();
        $tag->load('translations');
        return view('admin.game_tags.edit', compact('tag', 'supportedLocales'));
    }

    public function update(Request $request, GameTag $tag)
    {
        $request->validate([
            'label'                           => 'required|string|max:50|unique:game_tags,label,' . $tag->id,
            'translations.*.name'             => 'nullable|string|max:50',
            'translations.*.slug'             => 'nullable|string|max:50',
            'translations.*.description'      => 'nullable|string|max:1000',
            'translations.*.meta_title'       => 'nullable|string|max:70',
            'translations.*.meta_description' => 'nullable|string|max:160',
        ]);

        $tag->update([
            'label' => Str::of($request->label)->slug('-')
        ]);

        foreach ($request->translations as $localeCode => $data) {
            if (empty($data['name'])) {
                continue;
            }

            $finalSlug = !empty($data['slug'])
                ? (string) Str::of($data['slug'])->slug('-')
                : (string) Str::of($data['name'])->slug('-');

            GameTagTranslation::updateOrCreate(
                [
                    'game_tag_id' => $tag->id,
                    'locale'      => $localeCode,
                ],
                [
                    'name'             => trim($data['name']),
                    'slug'             => $finalSlug,
                    'description'      => $data['description'] ?? null,
                    'meta_title'       => $data['meta_title'] ?? null,
                    'meta_description' => $data['meta_description'] ?? null,
                ]
            );
        }

        return to_route('admin.game-tags.index')->with('success', 'Game Tag updated successfully!');
    }

    public function destroy(int $id)
    {
        $tag = GameTag::findOrFail($id);

        if (method_exists($tag, 'games')) {
            $tag->games()->detach();
        }

        if (method_exists($tag, 'translations')) {
            $tag->translations()->delete();
        }

        $tag->delete();

        return to_route('admin.game-tags.index')->with('success', 'Game Tag deleted successfully!');
    }
}
