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
        // 1. إعادة تعيين صلاحيات الـ Cache في حزمة Spatie
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. تعريف كافة صلاحيات المنصة بدقة لكل مكون
        $permissions = [
            // إدارة المستخدمين
            'manage-users',

            // الفئات (Categories)
            'view-categories',
            'create-categories',
            'edit-categories',
            'delete-categories',

            // الوسوم (Tags)
            'view-tags',
            'create-tags',
            'edit-tags',
            'delete-tags',

            // السلاسل (Series)
            'view-series',
            'create-series',
            'edit-series',
            'delete-series',

            // الهيكل الأساسي للمقالات والقصص (Base Articles Structure - جدول articles)
            'view-articles',
            'create-articles',
            'edit-articles',
            'delete-articles',
            'publish-articles',

            // ترجمات المقالات (Article Translations - جدول article_translations)
            'view-article-translations',
            'create-article-translations',
            'edit-article-translations',
            'delete-article-translations',
            'translate-content',

            // الاقتراحات (Suggestions)
            'manage-suggestions',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 3. إنشاء الرتب وتحديد الصلاحيات الدقيقة لكل رتبة

        // أ. المشرف العام (Super Admin) - يملك كل الصلاحيات المطلقة
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']);
        $superAdminRole->givePermissionTo(Permission::all());

        // ب. المحرر (Editor) - إدارة كاملة للمحتوى، الفئات، الوسوم، السلاسل، النشر، والمقترحات
        $editorRole = Role::firstOrCreate(['name' => 'editor']);
        $editorRole->givePermissionTo([
            'view-categories',
            'create-categories',
            'edit-categories',
            'delete-categories',
            'view-tags',
            'create-tags',
            'edit-tags',
            'delete-tags',
            'view-series',
            'create-series',
            'edit-series',
            'delete-series',
            'view-articles',
            'create-articles',
            'edit-articles',
            'delete-articles',
            'publish-articles',
            'view-article-translations',
            'create-article-translations',
            'edit-article-translations',
            'delete-article-translations',
            'translate-content',
            'manage-suggestions',
        ]);

        // ج. الكاتب (Writer) - مخصص لإنشاء وتعديل ترجمات المقالات
        $writerRole = Role::firstOrCreate(['name' => 'writer']);
        $writerRole->givePermissionTo([
            // أضفنا صلاحيات الترجمة هنا لتتوافق مع الـ Routes الجديدة:
            'view-article-translations',
            'create-article-translations',
            'edit-article-translations',
            'translate-content',
        ]);

        /*
        // د. المترجم (Translator) - عرض المقالات وإضافة/تعديل نصوص الترجمات فقط دون تعديل المقال الأساسي
        $translatorRole = Role::firstOrCreate(['name' => 'translator']);
        $translatorRole->givePermissionTo([
            'view-articles',
            'view-article-translations',
            'create-article-translations',
            'edit-article-translations',
            'translate-content',
        ]);
        */

        // 4. تعيين دور 'super-admin' لحساب الأدمن الأساسي
        $adminEmail = 'admin@admin.com';
        $adminUser = User::where('email', $adminEmail)->first();

        if ($adminUser) {
            $adminUser->assignRole('super-admin');
        }
    }
}
