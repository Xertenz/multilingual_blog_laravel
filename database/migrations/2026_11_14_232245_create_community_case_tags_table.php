<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. الجدول الأساسي لوسوم قضايا المجتمع
        Schema::create('community_case_tags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('label')->unique(); // رمز إداري فريد
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        // 2. جدول ترجمات وسوم قضايا المجتمع
        Schema::create('community_case_tag_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('community_case_tag_id', 'cc_tag_id_foreign')
                ->constrained('community_case_tags', 'id', 'cc_tag_id_fk')
                ->cascadeOnDelete();

            $table->string('locale', 5);
            $table->string('slug');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();

            $table->unique(['community_case_tag_id', 'locale'], 'cc_tag_id_locale_uq');
            $table->unique(['locale', 'slug'], 'cc_tag_locale_slug_uq');
        });

        // 3. الجدول الوسيط لربط قضايا المجتمع بوسومها
        Schema::create('community_case_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('community_case_id', 'cc_pivot_case_id')
                ->constrained('community_cases', 'id', 'cc_pivot_case_fk')
                ->cascadeOnDelete();

            $table->foreignId('community_case_tag_id', 'cc_pivot_tag_id')
                ->constrained('community_case_tags', 'id', 'cc_pivot_tag_fk')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(['community_case_id', 'community_case_tag_id'], 'cc_case_tag_uq');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('community_case_tag');
        Schema::dropIfExists('community_case_tag_translations');
        Schema::dropIfExists('community_case_tags');
    }
};
