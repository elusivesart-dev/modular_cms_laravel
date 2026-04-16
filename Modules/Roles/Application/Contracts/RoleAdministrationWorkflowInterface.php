<?php

declare(strict_types=1);

namespace Modules\Roles\Application\Contracts;

use Modules\Roles\Domain\Contracts\RoleEntityInterface;
use Modules\Roles\Domain\DTOs\RoleData;

interface RoleAdministrationWorkflowInterface
{
    /**
     * @return array<int, mixed>
     */
    public function availablePermissions(): array;

    /**
     * @return array<int, int>
     */
    public function selectedPermissionIds(RoleEntityInterface $role): array;

    /**
     * @param array<int, int> $permissionIds
     */
    public function store(RoleData $data, array $permissionIds = []): RoleEntityInterface;

    /**
     * @param array<int, int> $permissionIds
     */
    public function update(RoleEntityInterface $role, RoleData $data, array $permissionIds = []): RoleEntityInterface;

    public function delete(RoleEntityInterface $role): void;
}