<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. إعادة تعيين صلاحيات الـ Cache (مهم جداً في Spatie)
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. تعريف الصلاحيات بصيغة Slug
        $permissions = [
            'manage-users',
            'manage-categories',
            'create-articles',
            'edit-articles',
            'delete-articles',
            'publish-articles',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 3. إنشاء الرتب بصيغة Slug وربط الصلاحيات بها

        // أ. المشرف العام
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']);
        $superAdminRole->givePermissionTo(Permission::all());

        // ب. المحرر
        $editorRole = Role::firstOrCreate(['name' => 'editor']);
        $editorRole->givePermissionTo([
            'manage-categories',
            'create-articles',
            'edit-articles',
            'delete-articles',
            'publish-articles',
        ]);

        // ج. الكاتب
        $writerRole = Role::firstOrCreate(['name' => 'writer']);
        $writerRole->givePermissionTo([
            'create-articles',
            'edit-articles',
        ]);

        // 4. تعيين دور 'super-admin' لحسابك الأساسي
        $adminEmail = 'admin@admin.com'; // 👈 ضع بريدك هنا
        $adminUser = User::where('email', $adminEmail)->first();

        if ($adminUser) {
            $adminUser->assignRole('super-admin');
        }
    }
}
