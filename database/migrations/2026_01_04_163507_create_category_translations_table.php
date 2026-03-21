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
        Schema::create('category_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('categories')->cascadeOnUpdate()->cascadeOnDelete();
            $table->enum('language_code', ['en', 'ar', 'de', 'es', 'fr', 'ru', 'pt', 'it', 'nl', 'sv', 'da', 'nb']);
            $table->unique(['category_id', 'language_code']);
            $table->string("slug");//->unique();
            $table->unique(['language_code', 'slug']);
            $table->string('name');
            $table->text('description');
            $table->string('meta_title');
            $table->text('meta_description');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_translations');
    }
};
