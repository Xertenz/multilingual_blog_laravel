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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('internal_title')->unique();
            $table->integer('published_year')->nullable(); // سنة النشر
            $table->integer('pages_count')->nullable(); // عدد الصفحات
            $table->timestamps();
        });


        Schema::create('book_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 5); // ar, en, de...

            // البيانات النصية
            $table->string('title'); // عنوان الكتاب
            $table->string('author'); // اسم المؤلف حسب اللغة
            $table->string('slug'); // الرابط الصديق
            $table->text('summary')->nullable(); // ملخص الكتاب

            // البيانات المالية والتسويقية حسب اللغة
            $table->string('affiliate_url')->nullable(); // رابط الأفلييت المخصص لكل منطقة/لغة
            $table->decimal('price', 8, 2)->nullable(); // السعر
            $table->string('currency', 10)->default('USD'); // رمز العملة (USD, SAR, EUR, EGP...)

            $table->timestamps();

            // قيود الفرادة
            $table->unique(['book_id', 'locale']);
            $table->unique(['slug', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
        Schema::dropIfExists('book_translations');
    }
};
