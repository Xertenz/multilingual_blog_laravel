<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // جدول الاقتراحات الأساسي
        Schema::create('article_suggestions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // العضو المقترح
            $table->string('locale', 5)->default(app()->getLocale()); // لتخزين لغة الاقتراح (مثلاً: en, de, ar)
            $table->string('title'); // عنوان القضية أو المقترح
            $table->text('description')->nullable(); // تفاصيل إضافية أو روابط مصادر
            $table->enum('status', ['pending', 'approved', 'written'])->default('pending'); // حالة الاقتراح: pending (قيد المراجعة), approved (قيد البحث), written (تمت الكتابة)
            $table->timestamps();
        });

        // جدول التصويتات (Pivot Table) لمنع تكرار التصويت وتنظيم علاقة Many-to-Many
        Schema::create('article_suggestion_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // المستخدم المصوت
            $table->foreignId('article_suggestion_id')->constrained()->cascadeOnDelete(); // الاقتراح المستهدف
            $table->unique(['user_id', 'article_suggestion_id']); // لضمان تصويت واحد فقط لكل مستخدم على نفس الاقتراح
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('article_suggestion_votes');
        Schema::dropIfExists('article_suggestions');
    }
};
