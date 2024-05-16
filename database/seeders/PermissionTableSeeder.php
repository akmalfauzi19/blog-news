<?php

namespace Database\Seeders;

use App\Models\PremissionCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            "Role Management" => [
                'role-list',
                'role-create',
                'role-edit',
                'role-delete'
            ],
            "User Management" => [
                'user-list',
                'user-create',
                'user-edit',
                'user-delete',
            ],
            "Article Management" => [
                'article-list',
                'article-create',
                'article-edit',
                'article-delete'
            ],
            "Category Article Management" => [
                'category-list',
                'category-create',
                'category-edit',
                'category-delete'
            ]
        ];

        foreach ($permissions as $key => $permission) {
            $permissionCategory = PremissionCategory::create([
                'name' => $key
            ]);

            foreach ($permission as  $item) {
                Permission::create([
                    'permission_category_id' => $permissionCategory->id,
                    'name' => $item
                ]);
            }
        }
    }
}
