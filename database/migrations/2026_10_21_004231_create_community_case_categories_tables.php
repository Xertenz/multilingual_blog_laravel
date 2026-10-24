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
        // 1. الجدول الرئيسي للفئات
        Schema::create('community_case_categories', function (Blueprint $table) {
            $table->id();
            $table->string('label')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. جدول ترجمات الفئات (يحتوي على الـ slug)
        Schema::create('community_case_category_translations', function (Blueprint $table) {
            $table->id();
            // قمنا بتمرير اسم مختصر وثاني معامل لكي لا يتجاوز حد الـ 64 حرفاً
            $table->foreignId('community_case_category_id', 'cc_cat_id_foreign')
                ->constrained('community_case_categories', 'id', 'cc_cat_id_fk')
                ->cascadeOnDelete();

            $table->string('locale', 5);
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();

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
