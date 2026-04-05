<?php

declare(strict_types=1);

namespace Tests\Feature\RBAC;

use App\Core\RBAC\Contracts\PermissionManagerInterface;
use App\Core\RBAC\Contracts\RBACResolverInterface;
use App\Core\RBAC\Contracts\RoleManagerInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Permissions\Infrastructure\Models\Permission;
use Modules\Roles\Infrastructure\Models\Role;
use Modules\Users\Infrastructure\Models\User;
use Tests\TestCase;

final class PermissionAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_permission_can_be_granted_through_role_assignment(): void
    {
        $role = Role::query()->create([
            'name' => 'Editor',
            'slug' => 'editor',
            'description' => 'Editor role',
            'is_system' => false,
        ]);

        $permission = Permission::query()->create([
            'name' => 'posts.view',
            'label' => 'Posts View',
            'description' => 'View posts',
        ]);

        $role->permissions()->sync([$permission->getKey()]);

        $user = User::factory()->create();

        app(RoleManagerInterface::class)->assignRoleToSubject(
            'editor',
            User::class,
            (int) $user->getKey(),
        );

        $this->assertTrue(
            app(PermissionManagerInterface::class)->hasPermissionForSubject(
                'posts.view',
                User::class,
                (int) $user->getKey(),
            )
        );

        $this->assertTrue(
            app(RBACResolverInterface::class)->can($user, 'posts.view')
        );
    }

    public function test_permission_is_removed_when_role_is_revoked(): void
    {
        $role = Role::query()->create([
            'name' => 'Editor',
            'slug' => 'editor',
            'description' => 'Editor role',
            'is_system' => false,
        ]);

        $permission = Permission::query()->create([
            'name' => 'posts.view',
            'label' => 'Posts View',
            'description' => 'View posts',
        ]);

        $role->permissions()->sync([$permission->getKey()]);

        $user = User::factory()->create();

        app(RoleManagerInterface::class)->assignRoleToSubject(
            'editor',
            User::class,
            (int) $user->getKey(),
        );

        $this->assertTrue(
            app(RBACResolverInterface::class)->can($user, 'posts.view')
        );

        app(RoleManagerInterface::class)->revokeRoleFromSubject(
            'editor',
            User::class,
            (int) $user->getKey(),
        );

        $this->assertFalse(
            app(PermissionManagerInterface::class)->hasPermissionForSubject(
                'posts.view',
                User::class,
                (int) $user->getKey(),
            )
        );

        $this->assertFalse(
            app(RBACResolverInterface::class)->can($user, 'posts.view')
        );
    }

    public function test_user_without_permission_cannot_access_permission_protected_route(): void
    {
        Permission::query()->create([
            'name' => 'permissions.view',
            'label' => 'Permissions View',
            'description' => 'View permissions',
        ]);

        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('permissions.index'))
            ->assertForbidden();
    }

    public function test_super_admin_can_access_permission_protected_route_when_role_has_permission(): void
    {
        $role = Role::query()->create([
            'name' => 'Super Admin',
            'slug' => 'super-admin',
            'description' => 'Super Admin',
            'is_system' => true,
        ]);

        $permission = Permission::query()->create([
            'name' => 'permissions.view',
            'label' => 'Permissions View',
            'description' => 'View permissions',
        ]);

        $role->permissions()->sync([$permission->getKey()]);

        $user = User::factory()->create();

        app(RoleManagerInterface::class)->assignRoleToSubject(
            'super-admin',
            User::class,
            (int) $user->getKey(),
        );

        $this->actingAs($user)
            ->get(route('permissions.index'))
            ->assertOk();
    }
}