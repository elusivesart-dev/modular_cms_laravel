<?php

declare(strict_types=1);

namespace Modules\Permissions\Domain\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Permissions\Domain\DTOs\PermissionData;
use Modules\Permissions\Infrastructure\Models\Permission;

interface PermissionRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function findById(int $id): ?Permission;

    public function findByName(string $name): ?Permission;

    public function create(PermissionData $data): Permission;

    public function update(Permission $permission, PermissionData $data): Permission;

    public function delete(Permission $permission): void;

    public function syncRoles(Permission $permission, array $roleIds): void;

    public function getAssignedRoleIds(Permission $permission): array;

    public function getAllRoleOptions(): Collection;
}