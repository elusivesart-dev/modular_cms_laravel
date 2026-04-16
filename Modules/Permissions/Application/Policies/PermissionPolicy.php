<?php

declare(strict_types=1);

namespace Modules\Permissions\Application\Policies;

use App\Core\RBAC\Contracts\PermissionManagerInterface;
use Illuminate\Contracts\Auth\Authenticatable;
use Modules\Permissions\Infrastructure\Models\Permission;

final class PermissionPolicy
{
    public function __construct(
        private readonly PermissionManagerInterface $permissions,
    ) {
    }

    public function viewAny(Authenticatable $user): bool
    {
        return $this->permissions->hasPermissionForSubject(
            'permissions.view',
            $user::class,
            (int) $user->getAuthIdentifier(),
        );
    }

    public function view(Authenticatable $user, Permission $permission): bool
    {
        return $this->permissions->hasPermissionForSubject(
            'permissions.view',
            $user::class,
            (int) $user->getAuthIdentifier(),
        );
    }

    public function create(Authenticatable $user): bool
    {
        return $this->permissions->hasPermissionForSubject(
            'permissions.create',
            $user::class,
            (int) $user->getAuthIdentifier(),
        );
    }

    public function update(Authenticatable $user, Permission $permission): bool
    {
        return $this->permissions->hasPermissionForSubject(
            'permissions.update',
            $user::class,
            (int) $user->getAuthIdentifier(),
        );
    }

    public function delete(Authenticatable $user, Permission $permission): bool
    {
        return $this->permissions->hasPermissionForSubject(
            'permissions.delete',
            $user::class,
            (int) $user->getAuthIdentifier(),
        );
    }
}