<?php

declare(strict_types=1);

namespace App\Core\RBAC\Managers;

use App\Core\RBAC\Contracts\PermissionManagerInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

final class PermissionManager implements PermissionManagerInterface
{
    public function create(string $permissionSlug): void
    {
        if (! $this->permissionsTableExists()) {
            return;
        }

        DB::table('permissions')->updateOrInsert(
            ['name' => $permissionSlug],
            [
                'label' => null,
                'description' => null,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }

    public function delete(string $permissionSlug): bool
    {
        if (! $this->permissionsTableExists()) {
            return false;
        }

        $permission = DB::table('permissions')
            ->where('name', $permissionSlug)
            ->first(['id']);

        if ($permission === null) {
            return false;
        }

        $permissionId = (int) $permission->id;

        $roleIds = $this->rolePermissionsTableExists()
            ? DB::table('role_permissions')
                ->where('permission_id', $permissionId)
                ->pluck('role_id')
                ->map(static fn (mixed $id): int => (int) $id)
                ->all()
            : [];

        $deleted = (bool) DB::transaction(function () use ($permissionId): bool {
            if (Schema::hasTable('role_permissions')) {
                DB::table('role_permissions')
                    ->where('permission_id', $permissionId)
                    ->delete();
            }

            return DB::table('permissions')
                ->where('id', $permissionId)
                ->delete() > 0;
        });

        if ($deleted) {
            $this->forgetPermissionCachesForRoleIds($roleIds);
        }

        return $deleted;
    }

    public function exists(string $permissionSlug): bool
    {
        if (! $this->permissionsTableExists()) {
            return false;
        }

        return DB::table('permissions')
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
        if (
            ! $this->roleAssignmentsTableExists()
            || ! $this->rolesTableExists()
            || ! $this->rolePermissionsTableExists()
            || ! $this->permissionsTableExists()
        ) {
            return collect();
        }

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
        if (! $this->roleAssignmentsTableExists()) {
            return;
        }

        $normalizedRoleIds = array_values(array_unique(array_map('intval', $roleIds)));

        if ($normalizedRoleIds === []) {
            return;
        }

        $assignments = DB::table('role_assignments')
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

    private function permissionsTableExists(): bool
    {
        return Schema::hasTable('permissions');
    }

    private function rolePermissionsTableExists(): bool
    {
        return Schema::hasTable('role_permissions');
    }

    private function roleAssignmentsTableExists(): bool
    {
        return Schema::hasTable('role_assignments');
    }

    private function rolesTableExists(): bool
    {
        return Schema::hasTable('roles');
    }
}