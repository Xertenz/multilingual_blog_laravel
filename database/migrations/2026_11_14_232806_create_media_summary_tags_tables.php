<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. الجدول الأساسي لوسوم ملخصات الوسائط
        Schema::create('media_summary_tags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('label')->unique();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        // 2. جدول ترجمات وسوم ملخصات الوسائط
        Schema::create('media_summary_tag_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('media_summary_tag_id', 'ms_tag_id_foreign')
                ->constrained('media_summary_tags', 'id', 'ms_tag_id_fk')
                ->cascadeOnDelete();

            $table->string('locale', 5);
            $table->string('slug');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();

            $table->unique(['media_summary_tag_id', 'locale'], 'ms_tag_id_locale_uq');
            $table->unique(['locale', 'slug'], 'ms_tag_locale_slug_uq');
        });

        // 3. الجدول الوسيط لربط ملخصات الوسائط بوسومها
        Schema::create('media_summary_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('media_summary_id', 'ms_tag_pivot_ms_id')
                ->constrained('media_summaries', 'id', 'ms_tag_pivot_ms_fk')
                ->cascadeOnDelete();

            $table->foreignId('media_summary_tag_id', 'ms_tag_pivot_tag_id')
                ->constrained('media_summary_tags', 'id', 'ms_tag_pivot_tag_fk')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(['media_summary_id', 'media_summary_tag_id'], 'ms_pivot_tag_uq');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_summary_tag');
        Schema::dropIfExists('media_summary_tag_translations');
        Schema::dropIfExists('media_summary_tags');
    }
};
