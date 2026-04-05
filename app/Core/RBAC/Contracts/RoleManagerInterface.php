<?php

declare(strict_types=1);

namespace App\Core\RBAC\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Modules\Roles\Infrastructure\Models\Role;

interface RoleManagerInterface
{
    public function assignRoleToSubject(string $roleSlug, string $subjectType, int|string $subjectId): void;

    public function revokeRoleFromSubject(string $roleSlug, string $subjectType, int|string $subjectId): void;

    public function hasRoleForSubject(string $roleSlug, string $subjectType, int|string $subjectId): bool;

    /**
     * @param array<int, string> $roleSlugs
     */
    public function hasAnyRoleForSubject(array $roleSlugs, string $subjectType, int|string $subjectId): bool;

    /**
     * @return Collection<int, Role>
     */
    public function rolesForSubject(string $subjectType, int|string $subjectId): Collection;

    /**
     * @param array<int, string> $roleSlugs
     */
    public function syncRolesToSubject(array $roleSlugs, string $subjectType, int|string $subjectId): void;
}