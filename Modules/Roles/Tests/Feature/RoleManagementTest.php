<?php

declare(strict_types=1);

namespace Modules\Roles\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Roles\Application\Providers\RolesServiceProvider;
use Modules\Roles\Infrastructure\Models\Role;
use Orchestra\Testbench\TestCase;

final class RoleManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function getPackageProviders($app): array
    {
        return [
            RolesServiceProvider::class,
        ];
    }

    public function test_role_record_can_be_created(): void
    {
        Role::query()->create([
            'name' => 'Editor',
            'slug' => 'editor',
            'description' => 'Редактор',
            'is_system' => true,
        ]);

        $this->assertDatabaseHas('roles', [
            'slug' => 'editor',
        ]);
    }
}