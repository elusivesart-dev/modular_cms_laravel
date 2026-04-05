<?php

declare(strict_types=1);

namespace App\Core\RBAC\Resolver;

use App\Core\RBAC\Contracts\PermissionManagerInterface;
use App\Core\RBAC\Contracts\RBACResolverInterface;
use App\Core\RBAC\Contracts\RoleManagerInterface;

final class RBACResolver implements RBACResolverInterface
{
    public function __construct(
        private readonly RoleManagerInterface $roles,
        private readonly PermissionManagerInterface $permissions,
    ) {
    }

    public function can(object $user, string $permissionSlug): bool
    {
        if (!method_exists($user, 'getAuthIdentifier')) {
            return false;
        }

        return $this->permissions->hasPermissionForSubject(
            $permissionSlug,
            $user::class,
            $user->getAuthIdentifier(),
        );
    }

    public function hasRole(object $user, string $roleSlug): bool
    {
        if (!method_exists($user, 'getAuthIdentifier')) {
            return false;
        }

        return $this->roles->hasRoleForSubject(
            $roleSlug,
            $user::class,
            $user->getAuthIdentifier(),
        );
    }

    public function hasAnyRole(object $user, array $roleSlugs): bool
    {
        if (!method_exists($user, 'getAuthIdentifier')) {
            return false;
        }

        return $this->roles->hasAnyRoleForSubject(
            $roleSlugs,
            $user::class,
            $user->getAuthIdentifier(),
        );
    }
}