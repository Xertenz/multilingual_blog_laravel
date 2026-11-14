<?php

namespace Database\Seeders;

use App\Models\Article;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Article::create([
            'article_category_id' => 2,
            'user_id' => 1,
            'internal_title' => 'a',
            'views' => 0,
            'status' => 'published',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
}
