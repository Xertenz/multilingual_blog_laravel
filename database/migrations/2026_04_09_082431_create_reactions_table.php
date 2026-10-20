<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->morphs('reactionable');

            //$table->enum('type', ['like', 'dislike', 'happy', 'sad', 'funny', 'love', 'angry']); // نوع التفاعل (like, dislike, funny, sad, etc.)
            $table->enum('type', [
                'like',
                'dislike',
                'shocked',
                'confused',
                'sad',
                'angry',
                'mind_blown'
            ]);
            $table->timestamps();
            $table->unique(['user_id', 'reactionable_id', 'reactionable_type'], 'user_reaction_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reactions');
    }
};
