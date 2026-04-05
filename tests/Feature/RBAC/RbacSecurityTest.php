<?php

declare(strict_types=1);

namespace Tests\Feature\RBAC;

use App\Core\RBAC\Contracts\RoleManagerInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Roles\Infrastructure\Models\Role;
use Modules\Users\Infrastructure\Models\User;
use Tests\TestCase;

final class RbacSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_editor_cannot_access_users_index(): void
    {
        $editorRole = Role::query()->create([
            'name' => 'Editor',
            'slug' => 'editor',
            'description' => 'Editor role',
            'is_system' => false,
        ]);

        $user = User::factory()->create();

        app(RoleManagerInterface::class)->assignRoleToSubject(
            $editorRole->slug,
            User::class,
            (int) $user->getKey(),
        );

        $this->actingAs($user)
            ->get(route('users.index'))
            ->assertForbidden();
    }

    public function test_admin_cannot_access_roles_index(): void
    {
        $adminRole = Role::query()->create([
            'name' => 'Admin',
            'slug' => 'admin',
            'description' => 'Admin role',
            'is_system' => false,
        ]);

        $user = User::factory()->create();

        app(RoleManagerInterface::class)->assignRoleToSubject(
            $adminRole->slug,
            User::class,
            (int) $user->getKey(),
        );

        $this->actingAs($user)
            ->get(route('roles.index'))
            ->assertForbidden();
    }

    public function test_super_admin_can_access_roles_index(): void
    {
        $superAdminRole = Role::query()->create([
            'name' => 'Super Admin',
            'slug' => 'super-admin',
            'description' => 'Super admin role',
            'is_system' => true,
        ]);

        $user = User::factory()->create();

        app(RoleManagerInterface::class)->assignRoleToSubject(
            $superAdminRole->slug,
            User::class,
            (int) $user->getKey(),
        );

        $this->actingAs($user)
            ->get(route('roles.index'))
            ->assertOk();
    }

    public function test_admin_cannot_assign_super_admin_role(): void
    {
        Role::query()->create([
            'name' => 'Super Admin',
            'slug' => 'super-admin',
            'description' => 'Super admin role',
            'is_system' => true,
        ]);

        Role::query()->create([
            'name' => 'Admin',
            'slug' => 'admin',
            'description' => 'Admin role',
            'is_system' => false,
        ]);

        $actor = User::factory()->create([
            'email' => 'admin@test.local',
        ]);

        $target = User::factory()->create([
            'email' => 'target@test.local',
        ]);

        app(RoleManagerInterface::class)->assignRoleToSubject(
            'admin',
            User::class,
            (int) $actor->getKey(),
        );

        $response = $this->from(route('users.edit', $target))
            ->actingAs($actor)
            ->put(route('users.update', $target), [
                'name' => $target->name,
                'email' => $target->email,
                'is_active' => 1,
                'role_slugs' => ['super-admin'],
            ]);

        $response
            ->assertRedirect(route('users.edit', $target))
            ->assertSessionHasErrors(['role_slugs.0']);

        $this->assertFalse(
            app(RoleManagerInterface::class)->hasRoleForSubject(
                'super-admin',
                User::class,
                (int) $target->getKey(),
            )
        );
    }

    public function test_user_cannot_delete_self(): void
    {
        $superAdminRole = Role::query()->create([
            'name' => 'Super Admin',
            'slug' => 'super-admin',
            'description' => 'Super admin role',
            'is_system' => true,
        ]);

        $actor = User::factory()->create([
            'email' => 'owner@test.local',
        ]);

        app(RoleManagerInterface::class)->assignRoleToSubject(
            $superAdminRole->slug,
            User::class,
            (int) $actor->getKey(),
        );

        $this->actingAs($actor)
            ->delete(route('users.destroy', $actor))
            ->assertForbidden();

        $this->assertDatabaseHas('users', [
            'id' => $actor->getKey(),
        ]);
    }

    public function test_super_admin_can_delete_another_super_admin_when_not_last(): void
    {
        $superAdminRole = Role::query()->create([
            'name' => 'Super Admin',
            'slug' => 'super-admin',
            'description' => 'Super admin role',
            'is_system' => true,
        ]);

        $actor = User::factory()->create([
            'email' => 'owner@test.local',
        ]);

        $target = User::factory()->create([
            'email' => 'second@test.local',
        ]);

        app(RoleManagerInterface::class)->assignRoleToSubject(
            $superAdminRole->slug,
            User::class,
            (int) $actor->getKey(),
        );

        app(RoleManagerInterface::class)->assignRoleToSubject(
            $superAdminRole->slug,
            User::class,
            (int) $target->getKey(),
        );

        $this->actingAs($actor)
            ->delete(route('users.destroy', $target))
            ->assertRedirect(route('users.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('users', [
            'id' => $target->getKey(),
        ]);
    }
}