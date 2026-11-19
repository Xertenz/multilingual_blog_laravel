<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    protected $model = Article::class;

    public function definition(): array
    {
        $word = fake()->unique()->words(3, true);

        return [
            'article_category_id' => ArticleCategory::query()->inRandomOrder()->value('id'),
            'user_id'             => User::query()->inRandomOrder()->value('id') ?? 1,
            'label'               => Str::slug($word) . '-' . fake()->unique()->numberBetween(1000, 999999),
            'views'               => fake()->numberBetween(0, 5000),
            'status'              => 'published',
        ];
    }
}
