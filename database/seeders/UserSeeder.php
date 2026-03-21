<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table("users")->insert([
            "name" => "Admin",
            "email" => "admin@admin.com",
            "password" => Hash::make("1234"),
            'email_verified_at' => now(),
            'is_admin' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table("users")->insert([
            "name" => "john",
            "email" => "john@john.com",
            "password" => Hash::make("john1234"),
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table("users")->insert([
            "name" => "ali",
            "email" => "ali@ali.com",
            "password" => Hash::make("ali1234"),
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

    }
}
