<?php

declare(strict_types=1);

namespace Modules\Roles\Domain\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\Roles\Domain\Contracts\RoleAssignmentRepositoryInterface;
use Modules\Roles\Domain\Contracts\RoleEntityInterface;
use Modules\Roles\Domain\Contracts\RoleRepositoryInterface;
use Modules\Roles\Domain\Events\RoleAssignedEvent;
use Modules\Roles\Domain\Events\RoleRevokedEvent;
use Modules\Roles\Domain\Exceptions\RoleAssignmentException;

final class RoleAssignmentService
{
    public function __construct(
        private readonly RoleAssignmentRepositoryInterface $assignments,
        private readonly RoleRepositoryInterface $roles,
    ) {
    }

    public function assignRoleToSubject(string $roleSlug, string $subjectType, int|string $subjectId): void
    {
        $role = $this->roles->findBySlug($roleSlug);

        if ($role === null) {
            throw RoleAssignmentException::roleNotFound($roleSlug);
        }

        if ($this->assignments->hasRole($roleSlug, $subjectType, $subjectId)) {
            return;
        }

        $this->assignments->assign($role, $subjectType, $subjectId);

        $this->forgetRbacCache($subjectType, $subjectId);

        event(new RoleAssignedEvent($role, $subjectType, $subjectId));
    }

    public function revokeRoleFromSubject(string $roleSlug, string $subjectType, int|string $subjectId): void
    {
        $role = $this->roles->findBySlug($roleSlug);

        if ($role === null) {
            throw RoleAssignmentException::roleNotFound($roleSlug);
        }

        if (! $this->assignments->hasRole($roleSlug, $subjectType, $subjectId)) {
            return;
        }

        $this->assertNotRemovingLastSuperAdmin($role, $subjectType, $subjectId);

        $this->assignments->revoke($role, $subjectType, $subjectId);

        $this->forgetRbacCache($subjectType, $subjectId);

        event(new RoleRevokedEvent($role, $subjectType, $subjectId));
    }

    public function hasRoleForSubject(string $roleSlug, string $subjectType, int|string $subjectId): bool
    {
        return $this->assignments->hasRole($roleSlug, $subjectType, $subjectId);
    }

    /**
     * @param array<int, string> $roleSlugs
     */
    public function hasAnyRoleForSubject(array $roleSlugs, string $subjectType, int|string $subjectId): bool
    {
        foreach ($roleSlugs as $roleSlug) {
            if ($this->assignments->hasRole($roleSlug, $subjectType, $subjectId)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return Collection<int, RoleEntityInterface>
     */
    public function rolesForSubject(string $subjectType, int|string $subjectId): Collection
    {
        return $this->assignments->rolesForSubject($subjectType, $subjectId);
    }

    /**
     * @param array<int, string> $roleSlugs
     */
    public function syncRolesToSubject(array $roleSlugs, string $subjectType, int|string $subjectId): void
    {
        $normalizedSlugs = array_values(array_unique(array_filter(array_map(
            static fn (mixed $slug): string => is_string($slug) ? trim($slug) : '',
            $roleSlugs
        ))));

        $currentRoles = $this->rolesForSubject($subjectType, $subjectId);

        $currentSlugs = $currentRoles
            ->map(static fn (RoleEntityInterface $role): string => $role->getSlug())
            ->values()
            ->all();

        $rolesToAssign = array_values(array_diff($normalizedSlugs, $currentSlugs));
        $rolesToRevoke = array_values(array_diff($currentSlugs, $normalizedSlugs));

        DB::transaction(function () use ($rolesToAssign, $rolesToRevoke, $subjectType, $subjectId): void {
            foreach ($rolesToRevoke as $roleSlug) {
                $this->revokeRoleFromSubject($roleSlug, $subjectType, $subjectId);
            }

            foreach ($rolesToAssign as $roleSlug) {
                $this->assignRoleToSubject($roleSlug, $subjectType, $subjectId);
            }
        });

        $this->forgetRbacCache($subjectType, $subjectId);
    }

    private function assertNotRemovingLastSuperAdmin(RoleEntityInterface $role, string $subjectType, int|string $subjectId): void
    {
        if ($role->getSlug() !== 'super-admin') {
            return;
        }

        if (! $this->assignments->hasRole('super-admin', $subjectType, $subjectId)) {
            return;
        }

        if ($this->assignments->countSubjectsForRole($role) <= 1) {
            throw RoleAssignmentException::systemRoleCannotBeRevoked('super-admin');
        }
    }

    private function forgetRbacCache(string $subjectType, int|string $subjectId): void
    {
        $subjectKey = md5($subjectType . ':' . (string) $subjectId);

        Cache::forget('rbac.roles.' . $subjectKey);
        Cache::forget('rbac.permissions.' . $subjectKey);
    }
}