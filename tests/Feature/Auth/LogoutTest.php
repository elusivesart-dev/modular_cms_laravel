<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Users\Infrastructure\Models\User;
use Tests\TestCase;

final class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_logout_via_post(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_authenticated_user_can_access_logout_fallback_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('logout.fallback'))
            ->assertOk()
            ->assertSee('id="logout-form"', false)
            ->assertSee('action="' . route('logout') . '"', false);

        $this->assertAuthenticated();
    }

    public function test_guest_cannot_access_logout_post_route(): void
    {
        $this->post(route('logout'))
            ->assertRedirect(route('login'));
    }
}