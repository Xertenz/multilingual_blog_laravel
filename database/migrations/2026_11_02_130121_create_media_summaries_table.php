<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. الجدول الأساسي (البيانات المشتركة الثابتة)
        Schema::create('media_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_location_id')->nullable()->constrained('article_locations')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            //$table->enum('type', ['movie', 'series', 'documentary'])->default('documentary');
            $table->string('type')->default('documentary'); // نوع العمل (movie, series, podcast, etc.)
            $table->integer('release_year')->nullable(); // سنة الإصدار
            $table->decimal('imdb_rating', 3, 1)->nullable(); // تقييم IMDb
            $table->string('status')->default('draft'); // draft, pending, published
            $table->unsignedBigInteger('views')->default(0);

            $table->timestamps();
        });

        // 2. جدول الترجمات (النصوص، العناوين، والـ Slug الفريد لكل لغة)
        Schema::create('media_summary_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('media_summary_id')->constrained('media_summaries')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('locale', 5); // رمز اللغة (ar, en, de, ...)
            $table->string('title'); // عنوان العمل بهذه اللغة
            $table->string('slug'); // الرابط اللطيف لهذه اللغة
            $table->text('synopsis')->nullable(); // ملخص القصة بهذه اللغة
            $table->text('review_analysis')->nullable(); // التحليل الجنائي بهذه اللغة
            $table->string('meta_title');
            $table->text('meta_description');
            $table->string('status')->default('draft'); // draft, pending, published
            $table->unsignedBigInteger('views')->default(0);

            $table->unique(['media_summary_id', 'locale']);
            $table->unique(['locale', 'slug']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_summary_translations');
        Schema::dropIfExists('media_summaries');
    }
};
