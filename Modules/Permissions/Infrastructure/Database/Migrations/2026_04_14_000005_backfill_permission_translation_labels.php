<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('permissions')) {
            return;
        }

        $mappings = [
            'users.view' => 'permissions::permissions.items.users.view',
            'users.create' => 'permissions::permissions.items.users.create',
            'users.update' => 'permissions::permissions.items.users.update',
            'users.delete' => 'permissions::permissions.items.users.delete',

            'roles.view' => 'permissions::permissions.items.roles.view',
            'roles.create' => 'permissions::permissions.items.roles.create',
            'roles.update' => 'permissions::permissions.items.roles.update',
            'roles.delete' => 'permissions::permissions.items.roles.delete',

            'permissions.view' => 'permissions::permissions.items.permissions.view',
            'permissions.create' => 'permissions::permissions.items.permissions.create',
            'permissions.update' => 'permissions::permissions.items.permissions.update',
            'permissions.delete' => 'permissions::permissions.items.permissions.delete',

            'settings.view' => 'permissions::permissions.items.settings.view',
            'settings.create' => 'permissions::permissions.items.settings.create',
            'settings.update' => 'permissions::permissions.items.settings.update',
            'settings.delete' => 'permissions::permissions.items.settings.delete',

            'localization.view' => 'permissions::permissions.items.localization.view',
            'localization.manage' => 'permissions::permissions.items.localization.manage',
            'localization.install' => 'permissions::permissions.items.localization.install',
            'localization.delete' => 'permissions::permissions.items.localization.delete',
            'localization.update' => 'permissions::permissions.items.localization.update',

            'themes.view' => 'permissions::permissions.items.themes.view',
            'themes.update' => 'permissions::permissions.items.themes.update',
            'themes.manage' => 'permissions::permissions.items.themes.manage',
        ];

        foreach ($mappings as $name => $label) {
            DB::table('permissions')
                ->where('name', $name)
                ->update([
                    'label' => $label,
                    'updated_at' => now(),
                ]);
        }

        $legacyLabelMappings = [
            'Users View' => 'permissions::permissions.items.users.view',
            'Users Create' => 'permissions::permissions.items.users.create',
            'Users Update' => 'permissions::permissions.items.users.update',
            'Users Delete' => 'permissions::permissions.items.users.delete',

            'Roles View' => 'permissions::permissions.items.roles.view',
            'Roles Create' => 'permissions::permissions.items.roles.create',
            'Roles Update' => 'permissions::permissions.items.roles.update',
            'Roles Delete' => 'permissions::permissions.items.roles.delete',

            'Permissions View' => 'permissions::permissions.items.permissions.view',
            'Permissions Create' => 'permissions::permissions.items.permissions.create',
            'Permissions Update' => 'permissions::permissions.items.permissions.update',
            'Permissions Delete' => 'permissions::permissions.items.permissions.delete',

            'Settings View' => 'permissions::permissions.items.settings.view',
            'Settings Create' => 'permissions::permissions.items.settings.create',
            'Settings Update' => 'permissions::permissions.items.settings.update',
            'Settings Delete' => 'permissions::permissions.items.settings.delete',
        ];

        foreach ($legacyLabelMappings as $legacyLabel => $label) {
            DB::table('permissions')
                ->where('label', $legacyLabel)
                ->update([
                    'label' => $label,
                    'updated_at' => now(),
                ]);
        }

        DB::table('permissions')
            ->where('name', 'themes.view')
            ->where('label', 'themes::themes.items.themes.update')
            ->update([
                'label' => 'permissions::permissions.items.themes.view',
                'updated_at' => now(),
            ]);

        DB::table('permissions')
            ->where('name', 'themes.update')
            ->where('label', 'themes::themes.items.themes.update')
            ->update([
                'label' => 'permissions::permissions.items.themes.update',
                'updated_at' => now(),
            ]);

        DB::table('permissions')
            ->where('name', 'themes.manage')
            ->where('label', 'themes::themes.items.themes.update')
            ->update([
                'label' => 'permissions::permissions.items.themes.manage',
                'updated_at' => now(),
            ]);

        if (! Schema::hasTable('permission_translations')) {
            return;
        }

        $fallbackLocale = (string) config('app.fallback_locale', 'en');

        $permissions = DB::table('permissions')
            ->select(['id', 'name', 'label', 'description'])
            ->get();

        foreach ($permissions as $permission) {
            $label = is_string($permission->label) ? trim($permission->label) : null;
            $description = is_string($permission->description) ? trim($permission->description) : null;

            if ($label === null || $label === '' || str_contains($label, '::')) {
                continue;
            }

            DB::table('permission_translations')->updateOrInsert(
                [
                    'permission_id' => (int) $permission->id,
                    'locale' => $fallbackLocale,
                ],
                [
                    'label' => $label,
                    'description' => $description !== '' ? $description : null,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('permissions')) {
            return;
        }

        $reverseMappings = [
            'users.view' => 'Users View',
            'users.create' => 'Users Create',
            'users.update' => 'Users Update',
            'users.delete' => 'Users Delete',

            'roles.view' => 'Roles View',
            'roles.create' => 'Roles Create',
            'roles.update' => 'Roles Update',
            'roles.delete' => 'Roles Delete',

            'permissions.view' => 'Permissions View',
            'permissions.create' => 'Permissions Create',
            'permissions.update' => 'Permissions Update',
            'permissions.delete' => 'Permissions Delete',

            'settings.view' => 'Settings View',
            'settings.create' => 'Settings Create',
            'settings.update' => 'Settings Update',
            'settings.delete' => 'Settings Delete',

            'themes.view' => 'Themes View',
            'themes.update' => 'Themes Update',
            'themes.manage' => 'Themes Manage',
        ];

        foreach ($reverseMappings as $name => $label) {
            DB::table('permissions')
                ->where('name', $name)
                ->update([
                    'label' => $label,
                    'updated_at' => now(),
                ]);
        }
    }
};