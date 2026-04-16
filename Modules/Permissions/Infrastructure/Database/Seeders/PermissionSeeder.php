<?php

declare(strict_types=1);

namespace Modules\Permissions\Infrastructure\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Schema;
use Modules\Permissions\Infrastructure\Models\Permission;
use Modules\Permissions\Infrastructure\Models\PermissionTranslation;

final class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'users.view', 'label' => 'permissions::permissions.items.users.view'],
            ['name' => 'users.create', 'label' => 'permissions::permissions.items.users.create'],
            ['name' => 'users.update', 'label' => 'permissions::permissions.items.users.update'],
            ['name' => 'users.delete', 'label' => 'permissions::permissions.items.users.delete'],

            ['name' => 'roles.view', 'label' => 'permissions::permissions.items.roles.view'],
            ['name' => 'roles.create', 'label' => 'permissions::permissions.items.roles.create'],
            ['name' => 'roles.update', 'label' => 'permissions::permissions.items.roles.update'],
            ['name' => 'roles.delete', 'label' => 'permissions::permissions.items.roles.delete'],

            ['name' => 'permissions.view', 'label' => 'permissions::permissions.items.permissions.view'],
            ['name' => 'permissions.create', 'label' => 'permissions::permissions.items.permissions.create'],
            ['name' => 'permissions.update', 'label' => 'permissions::permissions.items.permissions.update'],
            ['name' => 'permissions.delete', 'label' => 'permissions::permissions.items.permissions.delete'],

            ['name' => 'settings.view', 'label' => 'permissions::permissions.items.settings.view'],
            ['name' => 'settings.create', 'label' => 'permissions::permissions.items.settings.create'],
            ['name' => 'settings.update', 'label' => 'permissions::permissions.items.settings.update'],
            ['name' => 'settings.delete', 'label' => 'permissions::permissions.items.settings.delete'],

            ['name' => 'localization.view', 'label' => 'permissions::permissions.items.localization.view'],
            ['name' => 'localization.manage', 'label' => 'permissions::permissions.items.localization.manage'],
            ['name' => 'localization.install', 'label' => 'permissions::permissions.items.localization.install'],
            ['name' => 'localization.delete', 'label' => 'permissions::permissions.items.localization.delete'],
            ['name' => 'localization.update', 'label' => 'permissions::permissions.items.localization.update'],

            ['name' => 'themes.view', 'label' => 'permissions::permissions.items.themes.view'],
            ['name' => 'themes.update', 'label' => 'permissions::permissions.items.themes.update'],
            ['name' => 'themes.manage', 'label' => 'permissions::permissions.items.themes.manage'],
        ];

        foreach ($permissions as $permission) {
            $model = Permission::query()->updateOrCreate(
                ['name' => $permission['name']],
                [
                    'label' => $permission['label'],
                    'description' => null,
                ]
            );

            $this->syncTranslations($model, (string) $permission['label']);
        }

        if (! Schema::hasTable('roles') || ! Schema::hasTable('role_permissions')) {
            return;
        }

        $adminRoleIds = DB::table('roles')
            ->whereIn('slug', ['super-admin', 'admin'])
            ->pluck('id')
            ->map(static fn (mixed $id): int => (int) $id)
            ->all();

        if ($adminRoleIds === []) {
            return;
        }

        $permissionIds = Permission::query()
            ->pluck('id')
            ->map(static fn (mixed $id): int => (int) $id)
            ->all();

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
}