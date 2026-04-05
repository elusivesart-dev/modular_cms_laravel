<?php

declare(strict_types=1);

namespace Modules\Roles\Tests\Unit;

use Modules\Roles\Domain\Services\RoleAssignmentService;
use Modules\Roles\Infrastructure\Models\Role;
use Orchestra\Testbench\TestCase;
use Modules\Roles\Application\Providers\RolesServiceProvider;

final class RoleAssignmentServiceTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            RolesServiceProvider::class,
        ];
    }

    public function test_role_can_be_assigned_and_checked(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../Infrastructure/Database/Migrations');

        $role = Role::query()->create([
            'name' => 'Admin',
            'slug' => 'admin',
            'description' => 'Admin',
            'is_system' => true,
        ]);

        /** @var RoleAssignmentService $service */
        $service = $this->app->make(RoleAssignmentService::class);

        $service->assignBySlug('admin', \stdClass::class, 1);

        $this->assertTrue($service->hasRole('admin', \stdClass::class, 1));
    }
}