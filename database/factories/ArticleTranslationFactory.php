<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\ArticleTranslation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ArticleTranslation>
 */
class ArticleTranslationFactory extends Factory
{
    protected $model = ArticleTranslation::class;

    public function definition(): array
    {
        $title = fake()->unique()->sentence(4);
        $slug = Str::slug($title) . '-' . fake()->unique()->numberBetween(1000, 999999);
        $summary = fake()->sentence(18);

        $paragraphs = collect(range(1, fake()->numberBetween(3, 5)))
            ->map(fn() => '<p>' . fake()->paragraph(fake()->numberBetween(3, 5)) . '</p>')
            ->implode("\n");

        return [
            'article_id'       => Article::query()->inRandomOrder()->value('id') ?? 1,
            'user_id'          => User::query()->inRandomOrder()->value('id') ?? 1,
            'locale'           => 'en',
            'slug'             => $slug,
            'status'           => 'published',
            'meta_title'       => Str::limit($title, 120),
            'meta_description' => Str::limit($summary, 200),
            'title'            => $title,
            'description'      => $paragraphs,
            'views'            => fake()->numberBetween(0, 5000),
        ];
    }
}
