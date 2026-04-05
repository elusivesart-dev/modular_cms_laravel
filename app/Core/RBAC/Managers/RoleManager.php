<?php

declare(strict_types=1);

namespace App\Core\RBAC\Managers;

use App\Core\RBAC\Contracts\RoleManagerInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Modules\Roles\Domain\Services\RoleAssignmentService;
use Modules\Roles\Infrastructure\Models\Role;

final class RoleManager implements RoleManagerInterface
{
    public function __construct(
        private readonly RoleAssignmentService $assignments,
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

    /**
     * @return Collection<int, Role>
     */
    public function rolesForSubject(string $subjectType, int|string $subjectId): Collection
    {
        return Cache::remember(
            $this->rolesCacheKey($subjectType, $subjectId),
            now()->addMinutes(10),
            fn (): Collection => $this->assignments->rolesForSubject($subjectType, $subjectId),
        );
    }

    public function syncRolesToSubject(array $roleSlugs, string $subjectType, int|string $subjectId): void
    {
        $this->assignments->syncRolesToSubject($roleSlugs, $subjectType, $subjectId);
    }

    private function rolesCacheKey(string $subjectType, int|string $subjectId): string
    {
        return 'rbac.roles.' . md5($subjectType . ':' . (string) $subjectId);
    }
}