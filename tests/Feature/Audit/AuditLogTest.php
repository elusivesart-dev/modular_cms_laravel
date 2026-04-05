<?php

declare(strict_types=1);

namespace Tests\Feature\Audit;

use App\Core\RBAC\Contracts\RoleManagerInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Roles\Infrastructure\Models\Role;
use Modules\Users\Infrastructure\Models\User;
use Tests\TestCase;

final class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_update_creates_audit_log_record(): void
    {
        Role::query()->create([
            'name' => 'Super Admin',
            'slug' => 'super-admin',
            'description' => 'Super admin role',
            'is_system' => true,
        ]);

        $actor = User::factory()->create([
            'email' => 'admin@test.local',
        ]);

        $target = User::factory()->create([
            'name' => 'Editor',
            'email' => 'editor@test.local',
        ]);

        app(RoleManagerInterface::class)->assignRoleToSubject(
            'super-admin',
            User::class,
            (int) $actor->getKey(),
        );

        $this->actingAs($actor)->put(route('users.update', $target), [
            'name' => 'Editor Updated',
            'email' => 'editor@test.local',
            'is_active' => 1,
            'role_slugs' => [],
        ])->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('audit_logs', [
            'event' => 'users.updated',
            'actor_type' => User::class,
            'actor_id' => (string) $actor->getKey(),
            'subject_type' => User::class,
            'subject_id' => (string) $target->getKey(),
        ]);
    }

    public function test_role_assignment_creates_audit_log_record(): void
    {
        Role::query()->create([
            'name' => 'Super Admin',
            'slug' => 'super-admin',
            'description' => 'Super admin role',
            'is_system' => true,
        ]);

        Role::query()->create([
            'name' => 'Editor',
            'slug' => 'editor',
            'description' => 'Editor role',
            'is_system' => false,
        ]);

        $actor = User::factory()->create([
            'email' => 'admin@test.local',
        ]);

        $target = User::factory()->create([
            'name' => 'Target User',
            'email' => 'target@test.local',
        ]);

        app(RoleManagerInterface::class)->assignRoleToSubject(
            'super-admin',
            User::class,
            (int) $actor->getKey(),
        );

        $this->actingAs($actor)->put(route('users.update', $target), [
            'name' => $target->name,
            'email' => $target->email,
            'is_active' => 1,
            'role_slugs' => ['editor'],
        ])->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('audit_logs', [
            'event' => 'roles.assigned',
            'actor_type' => User::class,
            'actor_id' => (string) $actor->getKey(),
            'subject_type' => Role::class,
            'subject_id' => '2',
        ]);
    }

    public function test_only_super_admin_can_open_audit_index(): void
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

        $admin = User::factory()->create();
        $superAdmin = User::factory()->create();

        app(RoleManagerInterface::class)->assignRoleToSubject(
            'admin',
            User::class,
            (int) $admin->getKey(),
        );

        app(RoleManagerInterface::class)->assignRoleToSubject(
            'super-admin',
            User::class,
            (int) $superAdmin->getKey(),
        );

        $this->actingAs($admin)
            ->get(route('audit.index'))
            ->assertForbidden();

        $this->actingAs($superAdmin)
            ->get(route('audit.index'))
            ->assertOk();
    }
}