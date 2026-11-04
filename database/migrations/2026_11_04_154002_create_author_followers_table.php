<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('author_followers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // المستخدم الذي يقوم بالمتابعة
            $table->foreignId('author_id')->constrained('users')->cascadeOnDelete(); // الكاتب المُتَابَع
            $table->timestamps();

            // لمنع تكرار المتابعة لنفس الكاتب من نفس المستخدم
            $table->unique(['user_id', 'author_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('author_followers');
    }
};
