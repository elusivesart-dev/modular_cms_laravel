<?php

declare(strict_types=1);

namespace Modules\Users\Application\Policies;

use App\Core\RBAC\Contracts\RoleManagerInterface;
use Modules\Users\Infrastructure\Models\User;

final class UserPolicy
{
    public function __construct(
        private readonly RoleManagerInterface $roles,
    ) {
    }

    public function viewAny(User $user): bool
    {
        return $this->isAdministrator($user);
    }

    public function view(User $user, User $model): bool
    {
        return $this->isAdministrator($user);
    }

    public function create(User $user): bool
    {
        return $this->isAdministrator($user);
    }

    public function update(User $user, User $model): bool
    {
        if ((int) $user->getKey() === (int) $model->getKey()) {
            return true;
        }

        return $this->isAdministrator($user);
    }

    public function delete(User $user, User $model): bool
    {
        if ((int) $user->getKey() === (int) $model->getKey()) {
            return false;
        }

        return $this->isSuperAdministrator($user);
    }

    private function isAdministrator(User $user): bool
    {
        return $this->roles->hasAnyRoleForSubject(
            ['super-admin', 'admin'],
            $user::class,
            (int) $user->getKey(),
        );
    }

    private function isSuperAdministrator(User $user): bool
    {
        return $this->roles->hasRoleForSubject(
            'super-admin',
            $user::class,
            (int) $user->getKey(),
        );
    }
}