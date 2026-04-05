<?php

declare(strict_types=1);

namespace Modules\Settings\Infrastructure\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Permissions\Infrastructure\Models\Permission;
use Modules\Roles\Infrastructure\Models\RoleAssignment;

final class SettingsPermissionSeeder extends Seeder
{
    public function run(): void
    {
        if (
            !Schema::hasTable('permissions')
            || !Schema::hasTable('roles')
            || !Schema::hasTable('role_permissions')
        ) {
            return;
        }

        $permissions = [
            ['name' => 'settings.view', 'label' => 'Settings View'],
            ['name' => 'settings.create', 'label' => 'Settings Create'],
            ['name' => 'settings.update', 'label' => 'Settings Update'],
            ['name' => 'settings.delete', 'label' => 'Settings Delete'],
        ];

        $permissionIdsByName = [];

        foreach ($permissions as $permission) {
            $model = Permission::query()->updateOrCreate(
                ['name' => $permission['name']],
                [
                    'label' => $permission['label'],
                    'description' => null,
                ]
            );

            $permissionIdsByName[$permission['name']] = (int) $model->getKey();
        }

        $superAdminRoleId = DB::table('roles')
            ->where('slug', 'super-admin')
            ->value('id');

        if ($superAdminRoleId !== null) {
            foreach ($permissionIdsByName as $permissionId) {
                DB::table('role_permissions')->updateOrInsert(
                    [
                        'role_id' => (int) $superAdminRoleId,
                        'permission_id' => $permissionId,
                    ],
                    [
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }

            $this->forgetPermissionCachesForRoleId((int) $superAdminRoleId);
        }

        $adminRoleId = DB::table('roles')
            ->where('slug', 'admin')
            ->value('id');

        if ($adminRoleId !== null) {
            foreach (['settings.view', 'settings.update'] as $permissionName) {
                DB::table('role_permissions')->updateOrInsert(
                    [
                        'role_id' => (int) $adminRoleId,
                        'permission_id' => $permissionIdsByName[$permissionName],
                    ],
                    [
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }

            $this->forgetPermissionCachesForRoleId((int) $adminRoleId);
        }
    }

    private function forgetPermissionCachesForRoleId(int $roleId): void
    {
        $assignments = RoleAssignment::query()
            ->where('role_id', $roleId)
            ->get(['subject_type', 'subject_id']);

        foreach ($assignments as $assignment) {
            cache()->forget(
                'rbac.permissions.' . md5(
                    (string) $assignment->subject_type . ':' . (string) $assignment->subject_id
                )
            );
        }
    }
}