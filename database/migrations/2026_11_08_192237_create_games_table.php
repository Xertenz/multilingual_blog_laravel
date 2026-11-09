<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. الجدول الأساسي للعبة (بيانات عامة)
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('game_category_id')->constrained()->cascadeOnDelete(); // فئة اللعبة الزامية
            $table->string('internal_title')->unique(); // معرف داخلي
            $table->year('published_year')->nullable(); // سنة الإصدار
            $table->string('developer')->nullable(); // المطور أو الاستوديو
            $table->unsignedBigInteger('views')->default(0);
            $table->timestamps();
        });

        // 2. جدول ترجمات ومقالات اللعبة (يدعم المحتوى الغني وصور الشرح)
        Schema::create('game_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('locale')->index();
            $table->string('title');
            $table->string('slug');
            $table->unsignedBigInteger('views')->default(0);
            // المقال الشامل والملخص التفاعلي للعبة
            $table->longText('content')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            // روابط وأسعار متجر اللعبة (Steam/Epic/etc)
            $table->string('affiliate_url')->nullable();
            $table->decimal('price', 8, 2)->nullable();
            $table->string('currency', 10)->default('USD');
            $table->timestamps();

            $table->unique(['game_id', 'locale']);
            $table->unique(['locale', 'slug']);
        });

        // 3. الجدول الوسيط للوسوم (Many-to-Many)
        Schema::create('game_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained()->cascadeOnDelete();
            $table->foreignId('game_tag_id')->constrained()->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_tag');
        Schema::dropIfExists('game_translations');
        Schema::dropIfExists('games');
    }
};
