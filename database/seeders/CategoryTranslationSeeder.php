<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
            'language_code' => 'en',
            'slug' => 'general',
            'name' => 'General',
            'description' => 'General Cases',
            'meta_title' => 'These are just general case',
            'meta_description' => 'laksdjf lksadjf lkasdjflkjsdflk aslkdfj',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('category_translations')->insert([
            'category_id' => 1,
            'language_code' => 'ar',
            'slug' => 'منوعات',
            'name' => 'منوعات',
            'description' => 'معلومات عامة ومنوعات',
            'meta_title' => 'شمينت مشنسيتبمنشتب  منيبتتمشب',
            'meta_description' => 'مشيسبت نيبتم من اشب ث هشيبه',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('category_translations')->insert([
            'category_id' => 2,
            'language_code' => 'en',
            'slug' => 'crimes',
            'name' => 'Crimes',
            'description' => 'Crimes Cases',
            'meta_title' => 'these are crimes about anything',
            'meta_description' => 'laksdfj lskdfj lskdfj lskdfj lskdfjlall lskdfj',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('category_translations')->insert([
            'category_id' => 2,
            'language_code' => 'ar',
            'slug' => 'جرائم',
            'name' => 'جرائم',
            'description' => 'قضايا جرائم',
            'meta_title' => 'هذه قضايا جرائم من اي نوع',
            'meta_description' => 'هذه قضايا جرائم من اي نوع',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
