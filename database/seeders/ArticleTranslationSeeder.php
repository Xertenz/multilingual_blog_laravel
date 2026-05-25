<?php

namespace Database\Seeders;

use App\Models\ArticleTranslation;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ArticleTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ArticleTranslation::create([
            'article_id' => 1,
            'language_code' => 'en',
            'slug' => 'the-miyazawa-family-massacre',
            'meta_title' => 'meta title here',
            'meta_description' => 'meta descripion here',
            'title' => 'the miyazawa family massacre',

            'description' => <<<EOF
            <p>this is the article description here</p>
            EOF,

            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
}
