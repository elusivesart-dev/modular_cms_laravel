<?php

declare(strict_types=1);

namespace Modules\Permissions\Infrastructure\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Permissions\Infrastructure\Models\Permission;

final class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'users.view', 'label' => 'Users View'],
            ['name' => 'users.create', 'label' => 'Users Create'],
            ['name' => 'users.update', 'label' => 'Users Update'],
            ['name' => 'users.delete', 'label' => 'Users Delete'],

            ['name' => 'roles.view', 'label' => 'Roles View'],
            ['name' => 'roles.create', 'label' => 'Roles Create'],
            ['name' => 'roles.update', 'label' => 'Roles Update'],
            ['name' => 'roles.delete', 'label' => 'Roles Delete'],

            ['name' => 'permissions.view', 'label' => 'Permissions View'],
            ['name' => 'permissions.create', 'label' => 'Permissions Create'],
            ['name' => 'permissions.update', 'label' => 'Permissions Update'],
            ['name' => 'permissions.delete', 'label' => 'Permissions Delete'],

            ['name' => 'settings.view', 'label' => 'Settings View'],
            ['name' => 'settings.update', 'label' => 'Settings Update'],
			
			['name' => 'localization.view', 'label' => 'permissions::permissions.items.localization.view'],
            ['name' => 'localization.manage', 'label' => 'permissions::permissions.items.localization.manage'],
            ['name' => 'localization.install', 'label' => 'permissions::permissions.items.localization.install'],
            ['name' => 'localization.delete', 'label' => 'permissions::permissions.items.localization.delete'],
            ['name' => 'localization.update', 'label' => 'permissions::permissions.items.localization.update'],
			
			['name' => 'themes.view', 'label' => 'themes::themes.items.themes.update'],
			['name' => 'themes.update', 'label' => 'themes::themes.items.themes.update'],
			['name' => 'themes.manage', 'label' => 'themes::themes.items.themes.update'],
        ];

        foreach ($permissions as $permission) {
            Permission::query()->updateOrCreate(
                ['name' => $permission['name']],
                [
                    'label' => $permission['label'],
                    'description' => null,
                ]
            );
        }

        if (!Schema::hasTable('roles') || !Schema::hasTable('role_permissions')) {
            return;
        }

        $adminRoleIds = DB::table('roles')
            ->whereIn('slug', ['super-admin', 'admin'])
            ->pluck('id')
            ->all();

        if ($adminRoleIds === []) {
            return;
        }

        $permissionIds = Permission::query()->pluck('id')->all();

        foreach ($adminRoleIds as $roleId) {
            foreach ($permissionIds as $permissionId) {
                DB::table('role_permissions')->updateOrInsert(
                    [
                        'role_id' => $roleId,
                        'permission_id' => $permissionId,
                    ],
                    [
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }
}      