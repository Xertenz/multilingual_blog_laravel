<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoryTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('category_translations')->insert([
            'category_id' => 1,
            'language_code' => 'ar',
            'slug' => 'منوعات',
            'name' => 'منوعات',
            'description' => 'معلومات عامة ومنوعات',
            'meta_title' => 'شمينت مشنسيتبمنشتب  منيبتتمشب',
            'meta_description' => 'مشيسبت نيبتم من اشب ث هشيبه',
        ]);
        DB::table('category_translations')->insert([
            'category_id' => 2,
            'language_code' => 'ar',
            'slug' => 'جرائم',
            'name' => 'جرائم',
            'description' => 'قضايا جنائية وجرائم',
            'meta_title' => 'مشبيت منيبت كشخهثعق لاشس شميثث',
            'meta_description' => 'ينسب صخهث ششثق ئىر ضخهثق خضثق',
        ]);
    }
}
