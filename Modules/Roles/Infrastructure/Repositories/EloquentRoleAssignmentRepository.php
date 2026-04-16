<?php

declare(strict_types=1);

namespace Modules\Roles\Infrastructure\Repositories;

use DomainException;
use Illuminate\Database\Eloquent\Collection;
use Modules\Roles\Domain\Contracts\RoleAssignmentRepositoryInterface;
use Modules\Roles\Domain\Contracts\RoleEntityInterface;
use Modules\Roles\Infrastructure\Models\Role;
use Modules\Roles\Infrastructure\Models\RoleAssignment;

final class EloquentRoleAssignmentRepository implements RoleAssignmentRepositoryInterface
{
    public function assign(RoleEntityInterface $role, string $subjectType, int|string $subjectId): RoleAssignment
    {
        return RoleAssignment::query()->firstOrCreate([
            'role_id' => $this->toModel($role)->getKey(),
            'subject_type' => $subjectType,
            'subject_id' => (string) $subjectId,
        ]);
    }

    public function revoke(RoleEntityInterface $role, string $subjectType, int|string $subjectId): void
    {
        RoleAssignment::query()
            ->where('role_id', $this->toModel($role)->getKey())
            ->where('subject_type', $subjectType)
            ->where('subject_id', (string) $subjectId)
            ->delete();
    }

    public function hasRole(string $roleSlug, string $subjectType, int|string $subjectId): bool
    {
        return RoleAssignment::query()
            ->where('subject_type', $subjectType)
            ->where('subject_id', (string) $subjectId)
            ->whereHas('role', static function ($query) use ($roleSlug): void {
                $query->where('slug', $roleSlug);
            })
            ->exists();
    }

    public function rolesForSubject(string $subjectType, int|string $subjectId): Collection
    {
        return Role::query()
            ->whereHas('assignments', static function ($query) use ($subjectType, $subjectId): void {
                $query->where('subject_type', $subjectType)
                    ->where('subject_id', (string) $subjectId);
            })
            ->orderBy('name')
            ->get();
    }

    public function countSubjectsForRole(RoleEntityInterface $role): int
    {
        return RoleAssignment::query()
            ->where('role_id', $this->toModel($role)->getKey())
            ->count();
    }

    private function toModel(RoleEntityInterface $role): Role
    {
        if (! $role instanceof Role) {
            throw new DomainException('Unsupported role entity implementation.');
        }

        return $role;
    }
}