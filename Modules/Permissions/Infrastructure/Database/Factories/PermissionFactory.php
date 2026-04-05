<?php

declare(strict_types=1);

namespace Modules\Permissions\Infrastructure\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Permissions\Infrastructure\Models\Permission;

/**
 * @extends Factory<Permission>
 */
final class PermissionFactory extends Factory
{
    protected $model = Permission::class;

    public function definition(): array
    {
        $module = $this->faker->randomElement(['users', 'roles', 'permissions', 'settings', 'media']);
        $action = $this->faker->randomElement(['view', 'create', 'update', 'delete']);

        return [
            'name' => sprintf('%s.%s', $module, $action),
            'label' => ucfirst($module) . ' ' . ucfirst($action),
            'description' => $this->faker->sentence(),
        ];
    }
}