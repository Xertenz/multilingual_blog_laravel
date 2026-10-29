<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();

            // المُبلِّغ
            $table->foreignId('reporter_id')->constrained('users')->cascadeOnDelete();

            // العنصر المُبْلَغ عنه (CommunityCase, Comment, Book, etc)
            $table->morphs('reportable');

            $table->string('reason_type'); // spam, harassment, inappropriate_content, etc.
            $table->text('details')->nullable();

            // حالة البلاغ: pending, resolved, dismissed
            $table->string('status')->default('pending');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
