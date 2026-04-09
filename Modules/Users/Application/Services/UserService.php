<?php

declare(strict_types=1);

namespace Modules\Users\Application\Services;

use App\Core\Database\Contracts\TransactionManagerInterface;
use App\Core\Events\Bus\EventBus;
use App\Core\RBAC\Contracts\RoleManagerInterface;
use DomainException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Users\Application\Contracts\UserServiceInterface;
use Modules\Users\Domain\Contracts\UserEntityInterface;
use Modules\Users\Domain\Contracts\UserRepositoryInterface;
use Modules\Users\Domain\DTOs\CreateUserData;
use Modules\Users\Domain\Events\UserCreatedEvent;
use Modules\Users\Domain\Events\UserDeletedEvent;
use Modules\Users\Domain\Events\UserRegisteredEvent;
use Modules\Users\Domain\Events\UserUpdatedEvent;
use Modules\Users\Domain\Exceptions\UserAlreadyExistsException;
use Modules\Users\Infrastructure\Models\User;

final class UserService implements UserServiceInterface
{
    public function __construct(
        private readonly UserRepositoryInterface $users,
        private readonly EventBus $eventBus,
        private readonly RoleManagerInterface $roles,
        private readonly TransactionManagerInterface $transactions,
    ) {
    }

    public function create(CreateUserData $data): UserEntityInterface
    {
        if ($this->users->findByEmail($data->email) !== null) {
            throw UserAlreadyExistsException::forEmail($data->email);
        }

        return $this->transactions->transaction(function () use ($data): UserEntityInterface {
            $user = $this->users->create($data->toArray());

            $this->eventBus->emit(new UserRegisteredEvent(
                userId: (int) $user->getKey(),
                email: $user->getEmailForVerification(),
            ));

            event(new UserCreatedEvent($this->toModel($user)));

            return $user;
        });
    }

    public function update(UserEntityInterface $user, array $data): UserEntityInterface
    {
        return $this->transactions->transaction(function () use ($user, $data): UserEntityInterface {
            $updated = $this->users->update($user, $data);

            event(new UserUpdatedEvent($this->toModel($updated)));

            return $updated;
        });
    }

    public function delete(UserEntityInterface $user): bool
    {
        $this->assertUserIsNotLastSuperAdmin($user);

        $userId = (int) $user->getKey();
        $name = (string) $this->toModel($user)->name;
        $email = (string) $this->toModel($user)->email;

        return $this->transactions->transaction(function () use ($user, $userId, $name, $email): bool {
            $deleted = $this->users->delete($user);

            if ($deleted) {
                event(new UserDeletedEvent($userId, $name, $email));
            }

            return $deleted;
        });
    }

    public function findById(int $id): ?UserEntityInterface
    {
        return $this->users->findById($id);
    }

    public function findByEmail(string $email): ?UserEntityInterface
    {
        return $this->users->findByEmail($email);
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->users->paginate($perPage);
    }

    private function assertUserIsNotLastSuperAdmin(UserEntityInterface $user): void
    {
        if (! $this->roles->hasRoleForSubject('super-admin', $user::class, (int) $user->getKey())) {
            return;
        }

        $subjectsCount = $this->roles->countSubjectsForRole('super-admin');

        if ($subjectsCount === null) {
            return;
        }

        if ($subjectsCount <= 1) {
            throw new DomainException(__('users::users.exceptions.last_super_admin_cannot_be_deleted'));
        }
    }

    private function toModel(UserEntityInterface $user): User
    {
        if (! $user instanceof User) {
            throw new DomainException('Unsupported user entity implementation.');
        }

        return $user;
    }
}