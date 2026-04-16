<?php

declare(strict_types=1);

namespace Modules\Roles\Application\Services;

use Modules\Permissions\Domain\Contracts\PermissionRepositoryInterface;
use Modules\Permissions\Domain\Services\PermissionAssignmentService;
use Modules\Roles\Application\Contracts\RoleAdministrationWorkflowInterface;
use Modules\Roles\Domain\Contracts\RoleEntityInterface;
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

    public function selectedPermissionIds(RoleEntityInterface $role): array
    {
        return $role->getSelectedPermissionIds();
    }

    public function store(RoleData $data, array $permissionIds = []): RoleEntityInterface
    {
        $role = $this->roles->create($data);

        $this->permissionAssignments->syncPermissionsToRole(
            $this->toModel($role),
            $permissionIds,
        );

        /** @var Role $freshRole */
        $freshRole = $this->toModel($role)->fresh('permissions');

        event(new RoleCreatedEvent($freshRole));

        return $freshRole;
    }

    public function update(RoleEntityInterface $role, RoleData $data, array $permissionIds = []): RoleEntityInterface
    {
        $updated = $this->roles->update($role, $data);

        $this->permissionAssignments->syncPermissionsToRole(
            $this->toModel($updated),
            $permissionIds,
        );

        /** @var Role $freshRole */
        $freshRole = $this->toModel($updated)->fresh('permissions');

        event(new RoleUpdatedEvent($freshRole));

        return $freshRole;
    }

    public function delete(RoleEntityInterface $role): void
    {
        event(new RoleDeletedEvent($role));

        $this->roles->delete($role);
    }

    private function toModel(RoleEntityInterface $role): Role
    {
        if (! $role instanceof Role) {
            throw new \DomainException('Unsupported role entity implementation.');
        }

        return $role;
    }
}