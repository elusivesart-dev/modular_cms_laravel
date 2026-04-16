<?php

declare(strict_types=1);

namespace Modules\Settings\Infrastructure\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Schema;
use Modules\Permissions\Infrastructure\Models\Permission;
use Modules\Permissions\Infrastructure\Models\PermissionTranslation;
use Modules\Roles\Infrastructure\Models\RoleAssignment;

final class SettingsPermissionSeeder extends Seeder
{
    public function run(): void
    {
        if (
            ! Schema::hasTable('permissions')
            || ! Schema::hasTable('roles')
            || ! Schema::hasTable('role_permissions')
        ) {
            return;
        }

        $permissions = [
            ['name' => 'settings.view', 'label' => 'permissions::permissions.items.settings.view'],
            ['name' => 'settings.create', 'label' => 'permissions::permissions.items.settings.create'],
            ['name' => 'settings.update', 'label' => 'permissions::permissions.items.settings.update'],
            ['name' => 'settings.delete', 'label' => 'permissions::permissions.items.settings.delete'],
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

            $this->syncTranslations($model, (string) $permission['label']);

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

    private function syncTranslations(Permission $permission, string $labelKey): void
    {
        if (! Schema::hasTable('permission_translations')) {
            return;
        }

        foreach ($this->supportedLocales() as $locale) {
            if (! Lang::has($labelKey, $locale)) {
                continue;
            }

            PermissionTranslation::query()->updateOrCreate(
                [
                    'permission_id' => (int) $permission->getKey(),
                    'locale' => $locale,
                ],
                [
                    'label' => (string) __($labelKey, [], $locale),
                    'description' => null,
                ]
            );
        }
    }

    /**
     * @return array<int, string>
     */
    private function supportedLocales(): array
    {
        return array_values(array_unique(array_filter([
            (string) config('app.locale'),
            (string) config('app.fallback_locale'),
        ])));
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