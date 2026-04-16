<?php

declare(strict_types=1);

namespace Modules\Roles\Domain\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Roles\Domain\DTOs\RoleData;

interface RoleRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function findById(int $id): ?RoleEntityInterface;

    public function findBySlug(string $slug): ?RoleEntityInterface;

    public function create(RoleData $data): RoleEntityInterface;

    public function update(RoleEntityInterface $role, RoleData $data): RoleEntityInterface;

    public function delete(RoleEntityInterface $role): void;
}