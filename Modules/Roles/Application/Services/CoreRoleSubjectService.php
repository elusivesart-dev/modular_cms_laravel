<?php

declare(strict_types=1);

namespace Modules\Roles\Application\Services;

use App\Core\RBAC\Contracts\RoleSubjectServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Modules\Roles\Domain\Contracts\RoleAssignmentRepositoryInterface;
use Modules\Roles\Domain\Contracts\RoleRepositoryInterface;
use Modules\Roles\Domain\Services\RoleAssignmentService;

final class CoreRoleSubjectService implements RoleSubjectServiceInterface
{
    public function __construct(
        private readonly RoleAssignmentService $assignments,
        private readonly RoleRepositoryInterface $roles,
        private readonly RoleAssignmentRepositoryInterface $roleAssignments,
    ) {
    }

    public function assignRoleToSubject(string $roleSlug, string $subjectType, int|string $subjectId): void
    {
        $this->assignments->assignRoleToSubject($roleSlug, $subjectType, $subjectId);
    }

    public function revokeRoleFromSubject(string $roleSlug, string $subjectType, int|string $subjectId): void
    {
        $this->assignments->revokeRoleFromSubject($roleSlug, $subjectType, $subjectId);
    }

    public function hasRoleForSubject(string $roleSlug, string $subjectType, int|string $subjectId): bool
    {
        return $this->assignments->hasRoleForSubject($roleSlug, $subjectType, $subjectId);
    }

    public function hasAnyRoleForSubject(array $roleSlugs, string $subjectType, int|string $subjectId): bool
    {
        return $this->assignments->hasAnyRoleForSubject($roleSlugs, $subjectType, $subjectId);
    }

    public function rolesForSubject(string $subjectType, int|string $subjectId): Collection
    {
        return $this->assignments->rolesForSubject($subjectType, $subjectId);
    }

    public function syncRolesToSubject(array $roleSlugs, string $subjectType, int|string $subjectId): void
    {
        $this->assignments->syncRolesToSubject($roleSlugs, $subjectType, $subjectId);
    }

    public function countSubjectsForRole(string $roleSlug): ?int
    {
        $role = $this->roles->findBySlug($roleSlug);

        if ($role === null) {
            return null;
        }

        return $this->roleAssignments->countSubjectsForRole($role);
    }
}