<?php

declare(strict_types=1);

namespace Modules\Users\Tests\Unit;

use App\Core\Events\Bus\EventBus;
use Mockery;
use Modules\Users\Application\Services\UserService;
use Modules\Users\Domain\DTOs\CreateUserData;
use Modules\Users\Infrastructure\Models\User;
use Modules\Users\Infrastructure\Repositories\UserRepository;
use Orchestra\Testbench\TestCase;

final class UserServiceTest extends TestCase
{
    public function test_user_can_be_created(): void
    {
        $repository = Mockery::mock(UserRepository::class);
        $eventBus = Mockery::mock(EventBus::class);

        $repository->shouldReceive('findByEmail')
            ->once()
            ->with('john@example.com')
            ->andReturn(null);

        $repository->shouldReceive('create')
            ->once()
            ->andReturn(new User([
                'id' => 1,
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => 'password123',
                'is_active' => true,
            ]));

        $eventBus->shouldReceive('emit')->once();

        $service = new UserService($repository, $eventBus);

        $user = $service->create(new CreateUserData(
            name: 'John Doe',
            email: 'john@example.com',
            password: 'password123',
            isActive: true,
        ));

        $this->assertSame('john@example.com', $user->email);
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}