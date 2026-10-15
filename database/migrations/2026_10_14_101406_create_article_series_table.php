<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('article_series', function (Blueprint $table) {
            $table->id();
            $table->foreignId('series_id')->constrained('series')->cascadeOnDelete();
            $table->foreignId('article_id')->constrained('articles')->cascadeOnDelete();
            $table->integer('sort_order')->default(1); // ترتيب ظهور المقال داخل السلسلة
            $table->timestamps();

            $table->unique(['series_id', 'article_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('article_series');
    }
};
