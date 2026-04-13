<?php

declare(strict_types=1);

namespace Modules\Roles\Application\Services;

use Modules\Permissions\Domain\Contracts\PermissionRepositoryInterface;
use Modules\Permissions\Domain\Services\PermissionAssignmentService;
use Modules\Roles\Application\Contracts\RoleAdministrationWorkflowInterface;
use Modules\Roles\Domain\Contracts\RoleRepositoryInterface;
use Modules\Roles\Domain\DTOs\RoleData;
use Modules\Roles\Domain\Events\RoleCreatedEvent;
use Modules\Roles\Domain\Events\RoleDeletedEvent;
use Modules\Roles\Domain\Events\RoleUpdatedEvent;
use Modules\Roles\Infrastructure\Models\Role;

final class RoleAdministrationWorkflowService implements RoleAdministrationWorkflowInterface
{
    public function __construct(
        private readonly RoleRepositoryInterface $roles,
        private readonly PermissionRepositoryInterface $permissions,
        private readonly PermissionAssignmentService $permissionAssignments,
    ) {
    }

    public function availablePermissions(): array
    {
        return $this->permissions->paginate(1000)->items();
    }

    public function selectedPermissionIds(Role $role): array
    {
        return $role->permissions()
            ->pluck('permissions.id')
            ->map(static fn (mixed $id): int => (int) $id)
            ->all();
    }

    public function store(RoleData $data, array $permissionIds = []): Role
    {
        $role = $this->roles->create($data);

        $this->permissionAssignments->syncPermissionsToRole(
            $role,
            $permissionIds,
        );

        /** @var Role $freshRole */
        $freshRole = $role->fresh('permissions');

        event(new RoleCreatedEvent($freshRole));

        return $freshRole;
    }

    public function update(Role $role, RoleData $data, array $permissionIds = []): Role
    {
        $updated = $this->roles->update($role, $data);

        $this->permissionAssignments->syncPermissionsToRole(
            $updated,
            $permissionIds,
        );

        /** @var Role $freshRole */
        $freshRole = $updated->fresh('permissions');

        event(new RoleUpdatedEvent($freshRole));

        return $freshRole;
    }

    public function delete(Role $role): void
    {
        $this->roles->delete($role);

        event(new RoleDeletedEvent($role));
    }
}