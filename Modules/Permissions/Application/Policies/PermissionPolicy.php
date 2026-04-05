<?php

declare(strict_types=1);

namespace Modules\Permissions\Application\Policies;

use App\Core\RBAC\Contracts\PermissionManagerInterface;
use Modules\Permissions\Infrastructure\Models\Permission;
use Modules\Users\Infrastructure\Models\User;

final class PermissionPolicy
{
    public function __construct(
        private readonly PermissionManagerInterface $permissions,
    ) {
    }

    public function viewAny(User $user): bool
    {
        return $this->permissions->hasPermissionForSubject(
            'permissions.view',
            $user::class,
            (int) $user->getKey(),
        );
    }

    public function view(User $user, Permission $permission): bool
    {
        return $this->permissions->hasPermissionForSubject(
            'permissions.view',
            $user::class,
            (int) $user->getKey(),
        );
    }

    public function create(User $user): bool
    {
        return $this->permissions->hasPermissionForSubject(
            'permissions.create',
            $user::class,
            (int) $user->getKey(),
        );
    }

    public function update(User $user, Permission $permission): bool
    {
        return $this->permissions->hasPermissionForSubject(
            'permissions.update',
            $user::class,
            (int) $user->getKey(),
        );
    }

    public function delete(User $user, Permission $permission): bool
    {
        return $this->permissions->hasPermissionForSubject(
            'permissions.delete',
            $user::class,
            (int) $user->getKey(),
        );
    }
}