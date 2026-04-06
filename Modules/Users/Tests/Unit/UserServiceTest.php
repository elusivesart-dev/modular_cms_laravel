<?php

declare(strict_types=1);

namespace Modules\Users\Tests\Unit;

use App\Core\Database\Contracts\TransactionManagerInterface;
use App\Core\Events\Bus\EventBus;
use App\Core\RBAC\Contracts\RoleManagerInterface;
use Mockery;
use Modules\Users\Application\Services\UserService;
use Modules\Users\Domain\Contracts\UserRepositoryInterface;
use Modules\Users\Domain\DTOs\CreateUserData;
use Modules\Users\Infrastructure\Models\User;
use Orchestra\Testbench\TestCase;

final class UserServiceTest extends TestCase
{
    public function test_user_can_be_created(): void
    {
        $repository = Mockery::mock(UserRepositoryInterface::class);
        $eventBus = Mockery::mock(EventBus::class);
        $transactions = Mockery::mock(TransactionManagerInterface::class);
        $roles = Mockery::mock(RoleManagerInterface::class);

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

        $transactions->shouldReceive('transaction')
            ->once()
            ->andReturnUsing(static fn (callable $callback): mixed => $callback());

        $service = new UserService($repository, $eventBus, $transactions, $roles);

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