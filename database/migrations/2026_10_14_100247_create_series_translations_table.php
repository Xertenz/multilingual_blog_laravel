<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('series_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('series_id')->constrained('series')->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('slug');
            $table->timestamps();

            // منع تكرار نفس اللغة لنفس السلسلة
            $table->unique(['series_id', 'locale']);
            // منع تكرار نفس الـ slug داخل نفس اللغة (لكي لا تتشابه روابط السلاسل لنفس اللغة)
            $table->unique(['locale', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('series_translations');
    }
};
