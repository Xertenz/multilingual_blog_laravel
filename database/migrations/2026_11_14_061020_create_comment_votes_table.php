<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            //$table->foreignId('comment_id')->constrained()->cascadeOnDelete();
            $table->morphs('votable');
            $table->tinyInteger('vote'); // 1 تعني Upvote، و -1 تعني Downvote
            $table->timestamps();

            // منع المستخدم من التصويت أكثر من مرة على نفس التعليق
            $table->unique(['user_id', 'votable_type', 'votable_id'], 'user_votable_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
};
