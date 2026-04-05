<?php

declare(strict_types=1);

namespace Modules\Users\Application\Services;

use App\Core\Events\Bus\EventBus;
use App\Core\RBAC\Contracts\RoleManagerInterface;
use DomainException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Modules\Roles\Domain\Contracts\RoleAssignmentRepositoryInterface;
use Modules\Roles\Domain\Contracts\RoleRepositoryInterface;
use Modules\Users\Application\Contracts\UserServiceInterface;
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
        private readonly RoleRepositoryInterface $roleRepository,
        private readonly RoleAssignmentRepositoryInterface $roleAssignments,
    ) {
    }

    public function create(CreateUserData $data): User
    {
        if ($this->users->findByEmail($data->email) !== null) {
            throw UserAlreadyExistsException::forEmail($data->email);
        }

        return DB::transaction(function () use ($data): User {
            $user = $this->users->create($data->toArray());

            $this->eventBus->emit(new UserRegisteredEvent(
                userId: (int) $user->getKey(),
                email: $user->email,
            ));

            event(new UserCreatedEvent($user));

            return $user;
        });
    }

    public function update(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data): User {
            $updated = $this->users->update($user, $data);

            event(new UserUpdatedEvent($updated));

            return $updated;
        });
    }

    public function delete(User $user): bool
    {
        $this->assertUserIsNotLastSuperAdmin($user);

        $userId = (int) $user->getKey();
        $name = (string) $user->name;
        $email = (string) $user->email;

        return DB::transaction(function () use ($user, $userId, $name, $email): bool {
            $deleted = $this->users->delete($user);

            if ($deleted) {
                event(new UserDeletedEvent($userId, $name, $email));
            }

            return $deleted;
        });
    }

    public function findById(int $id): ?User
    {
        return $this->users->findById($id);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->users->findByEmail($email);
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->users->paginate($perPage);
    }

    private function assertUserIsNotLastSuperAdmin(User $user): void
    {
        if (! $this->roles->hasRoleForSubject('super-admin', $user::class, (int) $user->getKey())) {
            return;
        }

        $superAdminRole = $this->roleRepository->findBySlug('super-admin');

        if ($superAdminRole === null) {
            return;
        }

        if ($this->roleAssignments->countSubjectsForRole($superAdminRole) <= 1) {
            throw new DomainException(__('users::users.exceptions.last_super_admin_cannot_be_deleted'));
        }
    }
}