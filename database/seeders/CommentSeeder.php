<?php

namespace Database\Seeders;

use App\Models\Comment;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Comment::create([
            'user_id' => 1,
            'article_id' => 1,
            'parent_id' => null,
            'body' => 'this is admin comment',
            'locale' => 'en',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
        Comment::create([
            'user_id' => 3,
            'article_id' => 1,
            'parent_id' => 1,
            'body' => 'this is a user reply',
            'locale' => 'en',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
        Comment::create([
            'user_id' => 3,
            'article_id' => 1,
            'parent_id' => null,
            'body' => 'this is a user comment',
            'locale' => 'en',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
}
