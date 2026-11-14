<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. الجدول الأساسي لوسوم الألعاب
        Schema::create('game_tags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('label')->unique();
            $table->timestamps();
        });

        // 2. جدول ترجمات وسوم الألعاب (مُحدّث بحقول الـ SEO)
        Schema::create('game_tag_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_tag_id')->constrained()->cascadeOnDelete();
            $table->string('locale')->index();
            $table->string('name');
            $table->string('slug');

            // 🌐 حقول الـ SEO الجديدة
            $table->text('description')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();

            $table->timestamps();

            $table->unique(['game_tag_id', 'locale']);
            $table->unique(['locale', 'slug']);
        });

        // 3. جدول الربط الوسيط (Many-to-Many بين الألعاب والوسوم)
        Schema::create('game_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained('games')->cascadeOnDelete();
            $table->foreignId('game_tag_id')->constrained('game_tags')->cascadeOnDelete();

            $table->unique(['game_id', 'game_tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_tag');
        Schema::dropIfExists('game_tag_translations');
        Schema::dropIfExists('game_tags');
    }
};
