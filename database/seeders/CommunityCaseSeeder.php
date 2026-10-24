<?php

namespace Database\Seeders;

use App\Models\CommunityCase;
use App\Models\CommunityCaseCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CommunityCaseSeeder extends Seeder
{
    public function run(): void
    {
        // التأكد من وجود مستخدم على الأقل لربطه بالقضية
        $user = User::first() ?? User::create([
            'name' => 'Detective Demo',
            'email' => 'detective@demo.com',
            'password' => bcrypt('password'),
        ]);

        // جلب أول فئة متاحة لربط القضية بها
        $category = CommunityCaseCategory::first();

        if ($category) {
            CommunityCase::create([
                'user_id' => $user->id,
                'community_case_category_id' => $category->id,
                'locale' => app()->getLocale(),
                'title' => 'قضية تجريبية: سرقة لوحة الموناليزا الغامضة',
                'slug' => Str::slug('قضية تجريبية سرقة لوحة الموناليزا الغامضة') . '-' . uniqid(),
                'body' => '<p>هذه قضية تجريبية تم إنشاؤها تلقائياً عبر الـ Seeder لغرض اختبار العرض والتعليقات والتحليل المجتمعي.</p><p>تفاصيل الأدلة تشير إلى وجود ثغرة أمنية في المعرض وتواطؤ خفي.</p>',
                'moderation_status' => 'approved',
                'case_status' => 'open',
                'views_count' => 15,
                'comments_count' => 0,
            ]);
        }
    }
}
