<?php

declare(strict_types=1);

namespace Modules\Roles\Application\Policies;

use Modules\Roles\Domain\Contracts\RoleEntityInterface;
use Modules\Roles\Domain\Services\RoleAssignmentService;

final class RolePolicy
{
    public function __construct(
        private readonly RoleAssignmentService $assignments,
    ) {
    }

    public function viewAny(object $user): bool
    {
        return $this->isSuperAdministrator($user);
    }

    public function view(object $user, RoleEntityInterface $role): bool
    {
        return $this->isSuperAdministrator($user);
    }

    public function create(object $user): bool
    {
        return $this->isSuperAdministrator($user);
    }

    public function update(object $user, RoleEntityInterface $role): bool
    {
        return $this->isSuperAdministrator($user);
    }

    public function delete(object $user, RoleEntityInterface $role): bool
    {
        if ($role->isSystem()) {
            return false;
        }

        return $this->isSuperAdministrator($user);
    }

    private function isSuperAdministrator(object $user): bool
    {
        return $this->matchesAnyRole($user, ['super-admin']);
    }

    private function matchesAnyRole(object $user, array $roleSlugs): bool
    {
        if (! method_exists($user, 'getAuthIdentifier')) {
            return false;
        }

        foreach ($roleSlugs as $roleSlug) {
            if ($this->assignments->hasRoleForSubject($roleSlug, $user::class, $user->getAuthIdentifier())) {
                return true;
            }
        }

        return false;
    }
}