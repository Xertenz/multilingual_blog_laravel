<?php

namespace Database\Seeders;

use App\Models\CommunityCaseCategory;
use Illuminate\Database\Seeder;

class CommunityCaseCategorySeeder extends Seeder
{
    public function run(): void
    {
        // الفئة الأولى: جرائم غامضة / Unsolved Crimes
        $cat1 = CommunityCaseCategory::create(['is_active' => true, 'label' => 'unsolved crimes']);
        $cat1->translations()->createMany([
            ['locale' => 'ar', 'name' => 'جرائم غامضة', 'slug' => 'unsolved-crimes', 'description' => 'قضايا جرائم لم يتم حلها وتحتاج إلى تحليل وتعاون مجتمعي.'],
            ['locale' => 'en', 'name' => 'Unsolved Crimes', 'slug' => 'unsolved-crimes', 'description' => 'Unsolved criminal cases requiring community analysis and collaboration.'],
        ]);

        // الفئة الثانية: اختفاء محير / Mysterious Disappearances
        $cat2 = CommunityCaseCategory::create(['is_active' => true, 'label' => 'mysterious disappearances']);
        $cat2->translations()->createMany([
            ['locale' => 'ar', 'name' => 'اختفاء محير', 'slug' => 'mysterious-disappearances', 'description' => 'حالات اختفاء أشخاص في ظروف غامضة.'],
            ['locale' => 'en', 'name' => 'Mysterious Disappearances', 'slug' => 'mysterious-disappearances', 'description' => 'Cases of people disappearing under mysterious circumstances.'],
        ]);
    }
}
