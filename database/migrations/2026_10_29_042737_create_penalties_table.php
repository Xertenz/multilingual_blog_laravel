<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penalties', function (Blueprint $table) {
            $table->id();

            // المستخدم المخالف
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // المشرف أصدر العقوبة
            $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();

            // درجة العقوبة (من 1 إلى 5)
            $table->unsignedTinyInteger('level');

            // سبب العقوبة
            $table->text('reason');

            // تاريخ انتهاء الحظر (للدرجات 2, 3, 4)
            $table->timestamp('expires_at')->nullable();

            // حالة العقوبة (نشطة أو انتهت/تم إلغاؤها)
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(['user_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penalties');
    }
};
