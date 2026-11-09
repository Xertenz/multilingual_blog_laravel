<?php

// database/migrations/xxxx_xx_xx_create_game_categories_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('game_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('label')->unique(); // System Label (مثل: crime-investigation)
            $table->timestamps();
        });

        Schema::create('game_category_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_category_id')->constrained()->cascadeOnDelete();
            $table->string('locale')->index();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->timestamps();

            $table->unique(['game_category_id', 'locale']);
            $table->unique(['locale', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_category_translations');
        Schema::dropIfExists('game_categories');
    }
};
