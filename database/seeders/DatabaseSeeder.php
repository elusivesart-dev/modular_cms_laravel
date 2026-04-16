<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Media\Infrastructure\Database\Seeders\MediaPermissionSeeder;
use Modules\Permissions\Infrastructure\Database\Seeders\PermissionSeeder;
use Modules\Roles\Infrastructure\Database\Seeders\RolesDatabaseSeeder;
use Modules\Settings\Infrastructure\Database\Seeders\SettingSeeder;
use Modules\Users\Infrastructure\Models\User;

final class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            RolesDatabaseSeeder::class,
            PermissionSeeder::class,
            MediaPermissionSeeder::class,
            SettingSeeder::class,
        ]);

        User::query()->firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
                'is_active' => true,
            ]
        );
    }
}