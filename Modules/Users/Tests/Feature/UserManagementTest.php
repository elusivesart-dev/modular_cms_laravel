<?php

declare(strict_types=1);

namespace Modules\Users\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Users\Application\Providers\UsersServiceProvider;
use Modules\Users\Infrastructure\Models\User;
use Orchestra\Testbench\TestCase;

final class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function getPackageProviders($app): array
    {
        return [
            UsersServiceProvider::class,
        ];
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../Infrastructure/Database/Migrations');
    }

    public function test_user_record_can_be_created(): void
    {
        User::query()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => 'password123',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'admin@example.com',
        ]);
    }
}