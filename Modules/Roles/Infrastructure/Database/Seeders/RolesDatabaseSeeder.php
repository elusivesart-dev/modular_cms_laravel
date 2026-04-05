<?php

declare(strict_types=1);

namespace Modules\Roles\Infrastructure\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Roles\Infrastructure\Models\Role;

final class RolesDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Super Admin',
                'slug' => 'super-admin',
                'description' => 'Пълен системен достъп.',
                'is_system' => true,
            ],
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Административен достъп.',
                'is_system' => true,
            ],
            [
                'name' => 'Editor',
                'slug' => 'editor',
                'description' => 'Достъп за редакция на съдържание.',
                'is_system' => true,
            ],
        ];

        foreach ($roles as $role) {
            Role::query()->updateOrCreate(
                ['slug' => $role['slug']],
                $role
            );
        }
    }
}