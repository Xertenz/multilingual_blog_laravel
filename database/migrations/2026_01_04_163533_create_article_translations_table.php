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
        Schema::create('article_translations', function (Blueprint $table) {
            $table->id();
            //$table->unsignedBigInteger('article_id');
            $table->foreignId('article_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->enum('language_code', ['ar', 'en']);
            $table->string("slug");
            $table->string('title');
            $table->longText('description');
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
        Schema::dropIfExists('article_translations');
    }
};
