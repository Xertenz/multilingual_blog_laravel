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
        Schema::create('tag_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tag_id')->constrained('tags')->onDelete('cascade');
            $table->string('locale', 5); // ar, en, en-US
            $table->string('name');
            $table->string('slug');
            $table->timestamps();

            // منع تكرار الترجمة لنفس الوسم باللغة نفسها
            $table->unique(['tag_id', 'locale']);
            // ضمان فرادة السلوج داخل اللغة الواحدة لأجل الـ SEO
            $table->unique(['slug', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tag_translations');
    }
};
