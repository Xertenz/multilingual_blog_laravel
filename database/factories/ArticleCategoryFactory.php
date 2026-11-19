<?php

namespace Database\Factories;

use App\Models\ArticleCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ArticleCategory>
 */
class ArticleCategoryFactory extends Factory
{
    protected $model = ArticleCategory::class;

    public function definition(): array
    {
        $word = fake()->unique()->words(2, true);

        return [
            'user_id'   => User::query()->inRandomOrder()->value('id') ?? 1,
            'label'     => Str::slug($word) . '-' . fake()->unique()->numberBetween(100, 99999),
            'is_active' => true,
        ];
    }
}
