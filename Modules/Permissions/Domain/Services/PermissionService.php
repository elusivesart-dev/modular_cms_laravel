<?php

declare(strict_types=1);

namespace Modules\Permissions\Domain\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Permissions\Domain\Contracts\PermissionRepositoryInterface;
use Modules\Permissions\Domain\DTOs\PermissionData;
use Modules\Permissions\Domain\Events\PermissionCreatedEvent;
use Modules\Permissions\Domain\Events\PermissionDeletedEvent;
use Modules\Permissions\Domain\Events\PermissionUpdatedEvent;
use Modules\Permissions\Domain\ValueObjects\PermissionName;
use Modules\Permissions\Infrastructure\Models\Permission;

final readonly class PermissionService
{
    public function __construct(
        private PermissionRepositoryInterface $permissions,
    ) {
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->permissions->paginate($perPage);
    }

    public function findOrFail(int $id): Permission
    {
        $permission = $this->permissions->findById($id);

        if ($permission === null) {
            abort(404);
        }

        return $permission;
    }

    public function create(array $payload): Permission
    {
        $data = PermissionData::fromArray($payload);

        new PermissionName($data->name);

        $permission = $this->permissions->create($data);

        event(new PermissionCreatedEvent($permission));

        return $permission;
    }

    public function update(Permission $permission, array $payload): Permission
    {
        $data = PermissionData::fromArray($payload);

        new PermissionName($data->name);

        $updated = $this->permissions->update($permission, $data);

        event(new PermissionUpdatedEvent($updated));

        return $updated;
    }

    public function delete(Permission $permission): void
    {
        $permissionId = (int) $permission->getKey();
        $permissionName = (string) $permission->name;

        $this->permissions->delete($permission);

        event(new PermissionDeletedEvent($permissionId, $permissionName));
    }

    public function getAllRoleOptions(): Collection
    {
        return $this->permissions->getAllRoleOptions();
    }

    public function getAssignedRoleIds(Permission $permission): array
    {
        return $this->permissions->getAssignedRoleIds($permission);
    }
}