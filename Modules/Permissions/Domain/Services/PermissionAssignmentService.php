<?php

declare(strict_types=1);

namespace Modules\Permissions\Domain\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\Permissions\Domain\Events\PermissionsSyncedToRoleEvent;
use Modules\Permissions\Infrastructure\Models\Permission;
use Modules\Roles\Infrastructure\Models\Role;
use Modules\Roles\Infrastructure\Models\RoleAssignment;

final class PermissionAssignmentService
{
    public function syncPermissionsToRole(Role $role, array $permissionIds): void
    {
        $ids = Permission::query()
            ->whereIn('id', array_values(array_unique(array_map('intval', $permissionIds))))
            ->pluck('id')
            ->map(static fn (mixed $id): int => (int) $id)
            ->all();

        DB::transaction(function () use ($role, $ids): void {
            $role->permissions()->sync($ids);
        });

        $this->forgetPermissionCachesForRoleSubjects((int) $role->getKey());

        event(new PermissionsSyncedToRoleEvent($role->fresh('permissions'), $ids));
    }

    private function forgetPermissionCachesForRoleSubjects(int $roleId): void
    {
        $assignments = RoleAssignment::query()
            ->where('role_id', $roleId)
            ->get(['subject_type', 'subject_id']);

        foreach ($assignments as $assignment) {
            Cache::forget(
                'rbac.permissions.' . md5($assignment->subject_type . ':' . (string) $assignment->subject_id)
            );
        }
    }
}