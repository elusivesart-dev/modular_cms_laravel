<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Permissions\Infrastructure\Database\Seeders\PermissionSeeder;
use Modules\Users\Infrastructure\Models\User;

final class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::query()->firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
                'is_active' => true,
            ]
        );

        $this->call([
            PermissionSeeder::class,
        ]);
    }
}