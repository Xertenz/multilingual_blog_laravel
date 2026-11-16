<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\CommunityCaseTagTranslation;
use Illuminate\Http\Request;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class CommunityCaseTagController extends Controller
{
    //
    public function show(int $tag_trans_id, ?string $slug = null)
    {
        $tag_trans = CommunityCaseTagTranslation::with('tag.translations')->find($tag_trans_id);

        if (!$tag_trans) {
            return to_route('home.index');
        }

        $currentLocale = LaravelLocalization::getCurrentLocale();

        if ($tag_trans->locale !== $currentLocale) {
            $target = $tag_trans->tag->translations->where('locale', $currentLocale)->first();
            if ($target) {
                return redirect()->route('community-case-tags.show', [
                    'id' => $target->id,
                    'slug' => $target->slug,
                ]);
            }
            return to_route('home.index')->with('info', __('This tag is not available in your language.'));
        }

        if (empty($slug) || $tag_trans->slug !== $slug) {
            return redirect()->route('community-case-tags.show', [
                'id' => $tag_trans->id,
                'slug' => $tag_trans->slug,
            ]);
        }

        $communityCases = $tag_trans->tag->approvedCommunityCases()
            ->with(['author', 'category.translations'])
            ->latest()
            ->paginate(12);

        return view('community_case_tag', compact('tag_trans', 'communityCases'));
    }
}
