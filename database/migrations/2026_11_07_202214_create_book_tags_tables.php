<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. الجدول الأساسي لوسوم الكتب
        Schema::create('book_tags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('label')->unique();
            $table->timestamps();
        });

        // 2. جدول ترجمات وسوم الكتب (مُحدّث بحقول الـ SEO)
        Schema::create('book_tag_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_tag_id')->constrained('book_tags')->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('name');
            $table->string('slug');

            // 🌐 حقول الـ SEO الجديدة
            $table->text('description')->nullable();         // نبذة تعريفية عن الوسم
            $table->string('meta_title')->nullable();       // عنوان الميتا لمحركات البحث
            $table->string('meta_description')->nullable(); // وصف الميتا لمحركات البحث

            $table->timestamps();

            $table->unique(['book_tag_id', 'locale']);
            $table->unique(['locale', 'slug']);
        });

        // 3. جدول الربط الوسيط (Many-to-Many بين الكتب والوسوم)
        Schema::create('book_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained('books')->cascadeOnDelete();
            $table->foreignId('book_tag_id')->constrained('book_tags')->cascadeOnDelete();

            $table->unique(['book_id', 'book_tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_tag');
        Schema::dropIfExists('book_tag_translations');
        Schema::dropIfExists('book_tags');
    }
};
