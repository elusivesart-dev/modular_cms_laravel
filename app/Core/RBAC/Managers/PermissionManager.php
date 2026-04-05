<?php

declare(strict_types=1);

namespace App\Core\RBAC\Managers;

use App\Core\RBAC\Contracts\PermissionManagerInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\Permissions\Infrastructure\Models\Permission;
use Modules\Roles\Infrastructure\Models\RoleAssignment;

final class PermissionManager implements PermissionManagerInterface
{
    public function create(string $permissionSlug): void
    {
        Permission::query()->firstOrCreate(
            ['name' => $permissionSlug],
            [
                'label' => null,
                'description' => null,
            ]
        );
    }

    public function delete(string $permissionSlug): bool
    {
        $permission = Permission::query()
            ->where('name', $permissionSlug)
            ->first();

        if ($permission === null) {
            return false;
        }

        $roleIds = $permission->roles()
            ->pluck('roles.id')
            ->map(static fn (mixed $id): int => (int) $id)
            ->all();

        $deleted = (bool) DB::transaction(static function () use ($permission): bool {
            $permission->roles()->detach();

            return (bool) $permission->delete();
        });

        if ($deleted) {
            $this->forgetPermissionCachesForRoleIds($roleIds);
        }

        return $deleted;
    }

    public function exists(string $permissionSlug): bool
    {
        return Permission::query()
            ->where('name', $permissionSlug)
            ->exists();
    }

    public function hasPermissionForSubject(string $permissionSlug, string $subjectType, int|string $subjectId): bool
    {
        $permissions = $this->permissionsForSubject($subjectType, $subjectId);

        return $permissions->contains($permissionSlug);
    }

    public function hasAnyPermissionForSubject(array $permissionSlugs, string $subjectType, int|string $subjectId): bool
    {
        if ($permissionSlugs === []) {
            return false;
        }

        $permissions = $this->permissionsForSubject($subjectType, $subjectId);

        foreach ($permissionSlugs as $permissionSlug) {
            if ($permissions->contains($permissionSlug)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return Collection<int, string>
     */
    private function permissionsForSubject(string $subjectType, int|string $subjectId): Collection
    {
        return Cache::remember(
            $this->permissionsCacheKey($subjectType, $subjectId),
            now()->addMinutes(10),
            static function () use ($subjectType, $subjectId): Collection {
                return DB::table('role_assignments')
                    ->join('roles', 'role_assignments.role_id', '=', 'roles.id')
                    ->join('role_permissions', 'roles.id', '=', 'role_permissions.role_id')
                    ->join('permissions', 'role_permissions.permission_id', '=', 'permissions.id')
                    ->where('role_assignments.subject_type', $subjectType)
                    ->where('role_assignments.subject_id', (string) $subjectId)
                    ->select('permissions.name')
                    ->distinct()
                    ->pluck('permissions.name')
                    ->map(static fn (mixed $name): string => (string) $name)
                    ->values();
            }
        );
    }

    private function permissionsCacheKey(string $subjectType, int|string $subjectId): string
    {
        return 'rbac.permissions.' . md5($subjectType . ':' . (string) $subjectId);
    }

    /**
     * @param array<int, int> $roleIds
     */
    private function forgetPermissionCachesForRoleIds(array $roleIds): void
    {
        $normalizedRoleIds = array_values(array_unique(array_map('intval', $roleIds)));

        if ($normalizedRoleIds === []) {
            return;
        }

        $assignments = RoleAssignment::query()
            ->whereIn('role_id', $normalizedRoleIds)
            ->get(['subject_type', 'subject_id']);

        foreach ($assignments as $assignment) {
            Cache::forget(
                $this->permissionsCacheKey(
                    (string) $assignment->subject_type,
                    (string) $assignment->subject_id,
                )
            );
        }
    }
}