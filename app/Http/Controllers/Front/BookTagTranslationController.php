<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\BookTagTranslation;
use App\Models\BookTranslation;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class BookTagTranslationController extends Controller
{
    public function show(int $id, ?string $slug = null)
    {
        $tag_trans = BookTagTranslation::with([
            'tag' => function ($query) {
                $query->where('is_active', true);
            },
            'tag.translations',
        ])->find($id);

        if (!$tag_trans || !$tag_trans->tag) {
            return to_route('books.index');
        }

        $currentLocale = LaravelLocalization::getCurrentLocale();

        // التحويل إلى ترجمة الوسم نفسها باللغة الحالية إن وُجدت
        if ($tag_trans->locale !== $currentLocale) {
            $targetTrans = $tag_trans->tag->translations
                ? $tag_trans->tag->translations->firstWhere('locale', $currentLocale)
                : null;

            if ($targetTrans) {
                return redirect()->route('book-tags.show', [
                    'id'   => $targetTrans->id,
                    'slug' => $targetTrans->slug,
                ]);
            }
        }

        // تصحيح الـ slug في الرابط لدعم SEO
        if (!empty($tag_trans->slug) && $slug !== $tag_trans->slug) {
            return redirect()->route('book-tags.show', [
                'id'   => $tag_trans->id,
                'slug' => $tag_trans->slug,
            ], 301);
        }

        $tagId = $tag_trans->book_tag_id ?? $tag_trans->tag->id;

        $translations = BookTranslation::where('locale', $currentLocale)
            ->whereHas('book', function ($query) use ($tagId) {
                $query->whereHas('tags', function ($q) use ($tagId) {
                    $q->where('book_tags.id', $tagId);
                });
            })
            ->with([
                'book.category.translations',
                'book.tags.translations',
                'book.media',
                'media',
                'author',
            ])
            ->latest()
            ->paginate(12);

        return view('books.tag', compact('tag_trans', 'translations'));
    }
}
