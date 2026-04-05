<?php

declare(strict_types=1);

namespace Modules\Roles\Domain\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Roles\Domain\DTOs\RoleData;
use Modules\Roles\Infrastructure\Models\Role;

interface RoleRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function findById(int $id): ?Role;

    public function findBySlug(string $slug): ?Role;

    public function create(RoleData $data): Role;

    public function update(Role $role, RoleData $data): Role;

    public function delete(Role $role): void;
}