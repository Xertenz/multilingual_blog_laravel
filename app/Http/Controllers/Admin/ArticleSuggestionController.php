<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArticleSuggestion;
use Illuminate\Http\Request;

class ArticleSuggestionController extends Controller
{
    public function index()
    {
        $suggestions = ArticleSuggestion::with(['user', 'votes'])
            ->withCount('votes')
            ->latest()
            ->paginate(10);

        return view('admin.suggestions.index', compact('suggestions'));
    }

    public function updateStatus(Request $request, ArticleSuggestion $suggestion)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,written',
        ]);

        $suggestion->update([
            'status' => $request->status,
        ]);

        return redirect()->route('admin.suggestions.index')
            ->with('success', 'Suggestion status updated successfully.');
    }

    public function destroy(ArticleSuggestion $suggestion)
    {
        $suggestion->delete();

        return redirect()->route('admin.suggestions.index')
            ->with('success', 'Suggestion deleted successfully.');
    }
}
