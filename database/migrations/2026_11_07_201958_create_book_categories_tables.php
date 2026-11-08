<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. الجدول الأساسي لفئات الكتب
        Schema::create('book_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('label')->unique(); // رمز إداري فريد (مثل: true-crime)
            $table->timestamps();
        });

        // 2. جدول ترجمات فئات الكتب
        Schema::create('book_category_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_category_id')->constrained('book_categories')->cascadeOnDelete();
            $table->string('locale', 5); // ar, en, fr...
            $table->string('slug');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();

            $table->unique(['book_category_id', 'locale']);
            $table->unique(['locale', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_category_translations');
        Schema::dropIfExists('book_categories');
    }
};
