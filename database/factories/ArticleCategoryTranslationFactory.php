<?php

namespace Database\Factories;

use App\Models\ArticleCategory;
use App\Models\ArticleCategoryTranslation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ArticleCategoryTranslation>
 */
class ArticleCategoryTranslationFactory extends Factory
{
    protected $model = ArticleCategoryTranslation::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);
        $slug = Str::slug($name) . '-' . fake()->unique()->numberBetween(100, 99999);
        $desc = fake()->sentence(14);

        return [
            'article_category_id' => ArticleCategory::query()->inRandomOrder()->value('id') ?? 1,
            'locale'              => 'en',
            'slug'                => $slug,
            'name'                => Str::title($name),
            'description'         => $desc,
            'meta_title'          => Str::limit(Str::title($name), 120),
            'meta_description'    => Str::limit($desc, 200),
        ];
    }
}
