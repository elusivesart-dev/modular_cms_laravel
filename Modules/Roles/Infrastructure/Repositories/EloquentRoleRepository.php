<?php

declare(strict_types=1);

namespace Modules\Roles\Infrastructure\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Roles\Domain\Contracts\RoleRepositoryInterface;
use Modules\Roles\Domain\DTOs\RoleData;
use Modules\Roles\Infrastructure\Models\Role;

final class EloquentRoleRepository implements RoleRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Role::query()
            ->withCount('permissions')
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function findById(int $id): ?Role
    {
        return Role::query()->find($id);
    }

    public function findBySlug(string $slug): ?Role
    {
        return Role::query()->where('slug', $slug)->first();
    }

    public function create(RoleData $data): Role
    {
        return Role::query()->create([
            'name' => $data->name,
            'slug' => $data->slug,
            'description' => $data->description,
            'is_system' => $data->is_system,
        ]);
    }

    public function update(Role $role, RoleData $data): Role
    {
        $role->update([
            'name' => $data->name,
            'slug' => $data->slug,
            'description' => $data->description,
            'is_system' => $data->is_system,
        ]);

        return $role->refresh();
    }

    public function delete(Role $role): void
    {
        $role->delete();
    }
}