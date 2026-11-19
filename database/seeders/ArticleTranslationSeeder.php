<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\ArticleTranslation;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ArticleTranslationSeeder extends Seeder
{
    public function run(): void
    {
        // 1. ترجمة المقالة الأساسية (التي أنشأها ArticleSeeder برقم 1)
        ArticleTranslation::firstOrCreate(
            [
                'article_id' => 1,
                'locale'     => 'en',
            ],
            [
                'user_id'          => 1,
                'slug'             => 'the-miyazawa-family-massacre',
                'status'           => 'published',
                'meta_title'       => 'meta title here',
                'meta_description' => 'meta descripion here',
                'title'            => 'the miyazawa family massacre',
                'description'      => <<<EOF
                    <p>this is the article description here</p>
                    EOF,
                'views'            => 0,
                'created_at'       => Carbon::now(),
                'updated_at'       => Carbon::now(),
            ]
        );

        // 2. إنشاء 25 مقالة منشورة جديدة عبر ArticleFactory + ترجمتها عبر ArticleTranslationFactory
        $categoryIds = ArticleCategory::query()->pluck('id');

        Article::factory()
            ->count(25)
            ->state(fn() => [
                'article_category_id' => $categoryIds->isNotEmpty() ? $categoryIds->random() : null,
                'user_id'             => 1,
                'status'              => 'published',
            ])
            ->create()
            ->each(function (Article $article) {
                ArticleTranslation::factory()->create([
                    'article_id' => $article->id,
                    'user_id'    => $article->user_id ?? 1,
                    'locale'     => 'en',
                    'status'     => 'published',
                ]);
            });
    }
}
