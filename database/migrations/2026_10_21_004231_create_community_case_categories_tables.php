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
        // 1. الجدول الرئيسي للفئات (مطابق لبقية الأقسام)
        Schema::create('community_case_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('label')->unique(); // System Label
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. جدول ترجمات الفئات (مطابق لبقية الأقسام مع حقول SEO)
        Schema::create('community_case_category_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('community_case_category_id')
                ->constrained('community_case_categories', 'id', 'cc_cat_id_fk')
                ->cascadeOnDelete();

            $table->string('locale', 5);
            $table->string('slug');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();

            // ضمان وجود ترجمة واحدة فقط للغة معينة لكل فئة
            $table->unique(['community_case_category_id', 'locale'], 'cc_cat_id_locale_uq');
            // ضمان عدم تكرار الـ slug لنفس اللغة
            $table->unique(['locale', 'slug'], 'cc_locale_slug_uq');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('community_case_category_translations');
        Schema::dropIfExists('community_case_categories');
    }
};
