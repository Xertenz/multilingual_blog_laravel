<?php

// database/migrations/xxxx_xx_xx_create_game_tags_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('game_tags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('label')->unique();
            $table->timestamps();
        });

        Schema::create('game_tag_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_tag_id')->constrained()->cascadeOnDelete();
            $table->string('locale')->index();
            $table->string('name');
            $table->string('slug');
            $table->timestamps();

            $table->unique(['game_tag_id', 'locale']);
            $table->unique(['locale', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_tag_translations');
        Schema::dropIfExists('game_tags');
    }
};
