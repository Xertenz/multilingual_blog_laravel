<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            ArticleCategorySeeder::class,
            ArticleCategoryTranslationSeeder::class,
            ArticleSeeder::class,
            ArticleTranslationSeeder::class,
            CommentSeeder::class,
            RolePermissionSeeder::class,
            CommunityCaseCategorySeeder::class,
            CommunityCaseSeeder::class,
        ]);
    }
}
