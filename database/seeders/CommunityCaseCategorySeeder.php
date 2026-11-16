<?php

namespace Database\Seeders;

use App\Models\CommunityCaseCategory;
use Illuminate\Database\Seeder;

class CommunityCaseCategorySeeder extends Seeder
{
    public function run(): void
    {
        // الفئة الأولى: جرائم غامضة / Unsolved Crimes
        $cat1 = CommunityCaseCategory::create([
            'user_id' => 1,
            'label' => 'unsolved crimes',
            'is_active' => true,
        ]);

        $cat1->translations()->createMany([
            [
                'locale' => 'ar',
                'name' => 'جرائم غامضة',
                'slug' => 'قضايا-غير-محلولة',
                'description' => 'قضايا جرائم لم يتم حلها وتحتاج إلى تحليل وتعاون مجتمعي.',
                'meta_title' => 'عنوان الميتا هنا',
                'meta_description' => 'وصف الميتا هنا'
            ],
            [
                'locale' => 'en',
                'name' => 'Unsolved Crimes',
                'slug' => 'unsolved-crimes',
                'description' => 'Unsolved criminal cases requiring community analysis and collaboration.',
                'meta_title' => 'meta title here',
                'meta_description' => 'meta description here'
            ],
        ]);

        // الفئة الثانية: اختفاء محير / Mysterious Disappearances
        $cat2 = CommunityCaseCategory::create([
            'user_id' => 1,
            'label' => 'mysterious disappearances',
            'is_active' => true,
        ]);
        $cat2->translations()->createMany([
            [
                'locale' => 'ar',
                'name' => 'اختفاء غامض',
                'slug' => 'اختفاء-غامض',
                'description' => 'حالات اختفاء أشخاص في ظروف غامضة.',
                'meta_title' => 'عنوان الميتا هنا',
                'meta_description' => 'وصف الميتا هنا'
            ],
            [
                'locale' => 'en',
                'name' => 'Mysterious Disappearances',
                'slug' => 'mysterious-disappearances',
                'description' => 'Cases of people disappearing under mysterious circumstances.',
                'meta_title' => 'meta title here',
                'meta_description' => 'meta description here'
            ],
        ]);
    }
}
