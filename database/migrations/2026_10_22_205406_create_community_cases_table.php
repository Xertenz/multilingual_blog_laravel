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
        Schema::create('community_cases', function (Blueprint $table) {
            $table->id();

            // صاحب القضية (المستخدم)
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->foreignId('community_case_category_id')->constrained('community_case_categories')->cascadeOnDelete();

            // لغة القضية (ar, en, de, etc)
            $table->string('locale', 5)->default('en');

            // عنوان القضية والرابط الصديق (Slug)
            $table->string('title');
            $table->string('slug')->unique();

            // تفاصيل القضية والأدلة المطروحة
            $table->longText('body');

            // نظام الاعتماد والرقابة (pending = بانتظار المراجعة, approved = مقبولة ومقروءة للجميع, rejected = مرفوضة)
            $table->string('moderation_status')->default('pending');
            // حالة القضية بالنسبة للنقاش (open = مفتوحة, closed = مغلقة, solved = تم حلها)
            $table->string('case_status')->default('open');
            // سبب الرفض في حال رفضت الإدارة القضية (ليظهر للعضو)
            $table->text('rejection_reason')->nullable();

            // عدادات خفيفة الأداء للزيارات والتعليقات
            $table->unsignedInteger('views_count')->default(0);
            $table->unsignedInteger('comments_count')->default(0);

            $table->timestamps();

            // الفهارس لتسريع استعلامات القوائم والفلترة
            $table->index(['locale', 'moderation_status', 'community_case_category_id'], 'cc_locale_status_cat_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('community_cases');
    }
};
