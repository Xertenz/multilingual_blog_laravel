<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('timelines', function (Blueprint $table) {
            $table->id();
            $table->morphs('timelineable');
            // تاريخ فعلي اختياري يساعد في الترتيب والاستعلام الزمني
            $table->date('event_date')->nullable();
            $table->unsignedSmallInteger('event_year')->nullable();
            $table->unsignedTinyInteger('event_month')->nullable();
            // ترتيب العرض اليدوي
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['timelineable_type', 'timelineable_id', 'sort_order'], 'timelines_morph_order_idx');
        });

        Schema::create('timeline_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('timeline_id')->constrained('timelines')->cascadeOnDelete();
            $table->string('locale', 5);
            // زمن أو تاريخ أو فترة بصيغة نصية مترجمة حسب اللغة
            $table->string('time_label', 150)->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['timeline_id', 'locale'], 'timeline_translations_unique_locale');
            $table->index('locale', 'timeline_translations_locale_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timeline_translations');
        Schema::dropIfExists('timelines');
    }
};
