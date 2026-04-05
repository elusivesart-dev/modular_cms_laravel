<?php

declare(strict_types=1);

namespace Modules\Roles\Application\Policies;

use Modules\Roles\Domain\Services\RoleAssignmentService;
use Modules\Roles\Infrastructure\Models\Role;

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

    public function view(object $user, Role $role): bool
    {
        return $this->isSuperAdministrator($user);
    }

    public function create(object $user): bool
    {
        return $this->isSuperAdministrator($user);
    }

    public function update(object $user, Role $role): bool
    {
        return $this->isSuperAdministrator($user);
    }

    public function delete(object $user, Role $role): bool
    {
        if ($role->is_system) {
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