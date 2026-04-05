<?php

declare(strict_types=1);

namespace Modules\Roles\Domain\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Modules\Roles\Infrastructure\Models\Role;
use Modules\Roles\Infrastructure\Models\RoleAssignment;

interface RoleAssignmentRepositoryInterface
{
    public function assign(Role $role, string $subjectType, int|string $subjectId): RoleAssignment;

    public function revoke(Role $role, string $subjectType, int|string $subjectId): void;

    public function hasRole(string $roleSlug, string $subjectType, int|string $subjectId): bool;

    /**
     * @return Collection<int, Role>
     */
    public function rolesForSubject(string $subjectType, int|string $subjectId): Collection;

    public function countSubjectsForRole(Role $role): int;
}