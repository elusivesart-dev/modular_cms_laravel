<?php

declare(strict_types=1);

namespace Modules\Users\Infrastructure\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Users\Infrastructure\Models\User;

final class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => 'password123',
            'remember_token' => Str::random(10),
            'is_active' => true,
        ];
    }
}