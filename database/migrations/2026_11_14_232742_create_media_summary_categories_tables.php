<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. الجدول الأساسي لفئات ملخصات الوسائط
        Schema::create('media_summary_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('label')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. جدول ترجمات فئات ملخصات الوسائط
        Schema::create('media_summary_category_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('media_summary_category_id', 'ms_cat_id_foreign')
                ->constrained('media_summary_categories', 'id', 'ms_cat_id_fk')
                ->cascadeOnDelete();

            $table->string('locale', 5);
            $table->string('slug');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();

            $table->unique(['media_summary_category_id', 'locale'], 'ms_cat_id_locale_uq');
            $table->unique(['locale', 'slug'], 'ms_cat_locale_slug_uq');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_summary_category_translations');
        Schema::dropIfExists('media_summary_categories');
    }
};
