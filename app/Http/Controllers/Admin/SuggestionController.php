<?php

namespace App\Http\Controllers\Admin;

use App\Enums\SuggestionStatus;
use App\Http\Controllers\Controller;
use App\Models\Suggestion;
use App\Notifications\SuggestionStatusUpdated;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class SuggestionController extends Controller
{
    public function index()
    {
        $suggestions = Suggestion::with(['author', 'votes'])
            ->withCount('votes')
            ->latest()
            ->paginate(10);

        return view('admin.suggestions.index', compact('suggestions'));
    }

    public function updateStatus(Request $request, Suggestion $suggestion)
    {
        $request->validate([
            'status' => ['required', new Enum(SuggestionStatus::class)],
        ]);

        $author = $suggestion->author;

        $suggestion->update([
            'status' => $request->status
        ]);

        if ($author) {
            $author->notify(new SuggestionStatusUpdated($suggestion));
        }

        return redirect()->route('admin.suggestions.index')
            ->with('success', 'Suggestion status updated successfully.');
    }

    public function destroy(Suggestion $suggestion)
    {
        $suggestion->delete();

        return redirect()->route('admin.suggestions.index')
            ->with('success', 'Suggestion deleted successfully.');
    }
}
