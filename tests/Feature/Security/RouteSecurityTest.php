<?php

declare(strict_types=1);

namespace Tests\Feature\Security;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Users\Infrastructure\Models\User;
use Tests\TestCase;

final class RouteSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_users_index(): void
    {
        $this->get(route('users.index'))
            ->assertRedirect(route('login'));
    }

    public function test_guest_is_redirected_from_roles_index(): void
    {
        $this->get(route('roles.index'))
            ->assertRedirect(route('login'));
    }

    public function test_guest_is_redirected_from_permissions_index(): void
    {
        $this->get(route('permissions.index'))
            ->assertRedirect(route('login'));
    }

    public function test_logout_requires_post_method(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/logout')
            ->assertOk();
    }

    public function test_logout_route_logs_user_out_via_post(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }
}