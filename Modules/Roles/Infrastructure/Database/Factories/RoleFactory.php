<?php

declare(strict_types=1);

namespace Modules\Roles\Infrastructure\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Roles\Infrastructure\Models\Role;

final class RoleFactory extends Factory
{
    protected $model = Role::class;

    public function definition(): array
    {
        $name = ucfirst($this->faker->unique()->word());

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $this->faker->sentence(),
            'is_system' => false,
        ];
    }
}