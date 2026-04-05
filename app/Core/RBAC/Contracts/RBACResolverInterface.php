<?php

declare(strict_types=1);

namespace App\Core\RBAC\Contracts;

interface RBACResolverInterface
{
    public function can(object $user, string $permissionSlug): bool;

    public function hasRole(object $user, string $roleSlug): bool;

    /**
     * @param array<int, string> $roleSlugs
     */
    public function hasAnyRole(object $user, array $roleSlugs): bool;
}