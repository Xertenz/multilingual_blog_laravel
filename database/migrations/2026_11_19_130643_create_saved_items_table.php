<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saved_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('savable_id');
            $table->string('savable_type');
            $table->timestamps();

            $table->unique(['user_id', 'savable_id', 'savable_type'], 'saved_items_user_savable_unique');
            $table->index(['savable_type', 'savable_id'], 'saved_items_savable_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saved_items');
    }
};
