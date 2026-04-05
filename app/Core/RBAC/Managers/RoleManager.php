<?php

declare(strict_types=1);

namespace App\Core\RBAC\Managers;

use App\Core\RBAC\Contracts\RoleManagerInterface;
use App\Core\RBAC\Contracts\RoleSubjectServiceInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

final class RoleManager implements RoleManagerInterface
{
    public function __construct(
        private readonly RoleSubjectServiceInterface $subjects,
    ) {
    }

    public function assignRoleToSubject(string $roleSlug, string $subjectType, int|string $subjectId): void
    {
        $this->subjects->assignRoleToSubject($roleSlug, $subjectType, $subjectId);
    }

    public function revokeRoleFromSubject(string $roleSlug, string $subjectType, int|string $subjectId): void
    {
        $this->subjects->revokeRoleFromSubject($roleSlug, $subjectType, $subjectId);
    }

    public function hasRoleForSubject(string $roleSlug, string $subjectType, int|string $subjectId): bool
    {
        return $this->subjects->hasRoleForSubject($roleSlug, $subjectType, $subjectId);
    }

    public function hasAnyRoleForSubject(array $roleSlugs, string $subjectType, int|string $subjectId): bool
    {
        return $this->subjects->hasAnyRoleForSubject($roleSlugs, $subjectType, $subjectId);
    }

    /**
     * @return Collection<int, mixed>
     */
    public function rolesForSubject(string $subjectType, int|string $subjectId): Collection
    {
        return Cache::remember(
            $this->rolesCacheKey($subjectType, $subjectId),
            now()->addMinutes(10),
            fn (): Collection => $this->subjects->rolesForSubject($subjectType, $subjectId),
        );
    }

    public function syncRolesToSubject(array $roleSlugs, string $subjectType, int|string $subjectId): void
    {
        $this->subjects->syncRolesToSubject($roleSlugs, $subjectType, $subjectId);
    }

    public function countSubjectsForRole(string $roleSlug): ?int
    {
        return $this->subjects->countSubjectsForRole($roleSlug);
    }

    private function rolesCacheKey(string $subjectType, int|string $subjectId): string
    {
        return 'rbac.roles.' . md5($subjectType . ':' . (string) $subjectId);
    }
}
