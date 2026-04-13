<?php

declare(strict_types=1);

namespace Modules\Roles\Application\Contracts;

use Modules\Roles\Domain\DTOs\RoleData;
use Modules\Roles\Infrastructure\Models\Role;

interface RoleAdministrationWorkflowInterface
{
    /**
     * @return array<int, mixed>
     */
    public function availablePermissions(): array;

    /**
     * @return array<int, int>
     */
    public function selectedPermissionIds(Role $role): array;

    /**
     * @param array<int, int> $permissionIds
     */
    public function store(RoleData $data, array $permissionIds = []): Role;

    /**
     * @param array<int, int> $permissionIds
     */
    public function update(Role $role, RoleData $data, array $permissionIds = []): Role;

    public function delete(Role $role): void;
}