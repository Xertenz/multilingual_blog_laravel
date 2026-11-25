<?php

namespace App\Http\Controllers\Front;

use App\Enums\ContentStatus;
use App\Http\Controllers\Controller;
use App\Models\ArticleTranslation;
use DOMDocument;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ArticleController extends Controller
{
    public function show(int $article_trans_id, ?string $article_trans_slug = null)
    {
        $currentLocale = LaravelLocalization::getCurrentLocale();

        $trans = ArticleTranslation::with([
            'article.comments' => function ($query) {
                $query->whereNull('parent_id')->with('replies');
            },
            'article.reactions' => function ($query) {
                $query->whereIn('type', ['like', 'dislike']);
            },
            'article.tags.translations' => function ($query) use ($currentLocale) {
                $query->where('locale', $currentLocale);
            },
            'article.category.translations' => function ($query) use ($currentLocale) {
                $query->where('locale', $currentLocale);
            },
            'article',
            'article.translations',
            'article.category',
            'article.tags',
            'article.media',
            'article.timelines.translations',
        ])->find($article_trans_id);

        if (
            !$trans
            || $trans->article?->status !== ContentStatus::PUBLISHED
            || $trans->status !== ContentStatus::PUBLISHED
        ) {
            return to_route('home.index')->with('info', __('This article is not available at the moment.'));
        }

        $trans->increment('views');
        $trans->article->increment('views');

        $doc = new DOMDocument();
        @$doc->loadHTML(
            mb_convert_encoding($trans->description, 'HTML-ENTITIES', 'UTF-8'),
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );

        $imgs = $doc->getElementsByTagName('img');
        foreach ($imgs as $img) {
            $content_img_id = $img->getAttribute('data-image-id');
            $media = Media::find($content_img_id);
            if ($media) {
                $img->setAttribute('srcset', $media->getSrcset());
                $img->setAttribute('sizes', '(max-width: 768px) 100vw, 1200px');
            }
        }

        $trans->description = $doc->saveHTML();

        if ($trans->locale !== $currentLocale) {
            $target_translation = $trans->article->translations
                ->where('locale', $currentLocale)
                ->where('status', ContentStatus::PUBLISHED)
                ->first();

            if ($target_translation) {
                return redirect()->route('articles.show', [
                    'id'   => $target_translation->id,
                    'slug' => $target_translation->slug,
                ]);
            }

            return to_route('home.index')->with('info', __('This article is not available in your language.'));
        }

        if (empty($article_trans_slug) || $trans->slug !== $article_trans_slug) {
            return redirect()->route('articles.show', [
                'id'   => $trans->id,
                'slug' => $trans->slug,
            ], 301);
        }

        $relatedArticles = ArticleTranslation::with([
            'article.media',
            'article.category.translations',
            'author',
        ])
            ->where('locale', $currentLocale)
            ->where('status', ContentStatus::PUBLISHED->value)
            ->where('article_id', '!=', $trans->article_id)
            ->whereHas('article', function ($query) use ($trans) {
                $query->where('status', ContentStatus::PUBLISHED->value)
                    ->where('article_category_id', $trans->article->article_category_id);
            })
            ->latest()
            ->take(4)
            ->get();

        $previousArticle = ArticleTranslation::where('locale', $currentLocale)
            ->where('status', ContentStatus::PUBLISHED->value)
            ->where('id', '<', $trans->id)
            ->whereHas('article', function ($query) {
                $query->where('status', ContentStatus::PUBLISHED->value);
            })
            ->orderBy('id', 'desc')
            ->first();

        $nextArticle = ArticleTranslation::where('locale', $currentLocale)
            ->where('status', ContentStatus::PUBLISHED->value)
            ->where('id', '>', $trans->id)
            ->whereHas('article', function ($query) {
                $query->where('status', ContentStatus::PUBLISHED->value);
            })
            ->orderBy('id', 'asc')
            ->first();

        return view('articles.show', [
            'article_trans'   => $trans,
            'relatedArticles' => $relatedArticles,
            'previousArticle' => $previousArticle,
            'nextArticle'     => $nextArticle,
        ]);
    }
}
