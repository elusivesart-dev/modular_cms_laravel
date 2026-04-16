<?php

declare(strict_types=1);

namespace Modules\Permissions\Infrastructure\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Permissions\Domain\Contracts\PermissionRepositoryInterface;
use Modules\Permissions\Domain\DTOs\PermissionData;
use Modules\Permissions\Infrastructure\Models\Permission;
use Modules\Roles\Infrastructure\Models\Role;
use Modules\Roles\Infrastructure\Models\RoleAssignment;

final class PermissionRepository implements PermissionRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        $query = Permission::query()
            ->withCount('roles')
            ->orderBy('name');

        if ($this->supportsTranslations()) {
            $query->with(['translations']);
        }

        return $query->paginate($perPage);
    }

    public function findById(int $id): ?Permission
    {
        $query = Permission::query();

        if ($this->supportsTranslations()) {
            $query->with(['translations']);
        }

        return $query->find($id);
    }

    public function findByName(string $name): ?Permission
    {
        $query = Permission::query();

        if ($this->supportsTranslations()) {
            $query->with(['translations']);
        }

        return $query->where('name', $name)->first();
    }

    public function create(PermissionData $data): Permission
    {
        $permission = DB::transaction(function () use ($data): Permission {
            $permission = Permission::query()->create([
                'name' => $data->name,
                'label' => $data->label,
                'description' => $data->description,
            ]);

            $this->syncTranslations($permission, $data);
            $this->syncRoles($permission, $data->roleIds);

            return $this->supportsTranslations()
                ? $permission->fresh(['translations'])
                : $permission->refresh();
        });

        $this->forgetPermissionCachesForRoleIds($data->roleIds);

        return $permission;
    }

    public function update(Permission $permission, PermissionData $data): Permission
    {
        $currentRoleIds = $permission->roles()
            ->pluck('roles.id')
            ->map(static fn (mixed $id): int => (int) $id)
            ->all();

        $updatedPermission = DB::transaction(function () use ($permission, $data): Permission {
            $permission->update([
                'name' => $data->name,
                'label' => $data->label,
                'description' => $data->description,
            ]);

            $this->syncTranslations($permission, $data);
            $this->syncRoles($permission, $data->roleIds);

            return $this->supportsTranslations()
                ? $permission->fresh(['translations'])
                : $permission->refresh();
        });

        $affectedRoleIds = array_values(array_unique([
            ...array_map('intval', $currentRoleIds),
            ...array_map('intval', $data->roleIds),
        ]));

        $this->forgetPermissionCachesForRoleIds($affectedRoleIds);

        return $updatedPermission;
    }

    public function delete(Permission $permission): void
    {
        $roleIds = $permission->roles()
            ->pluck('roles.id')
            ->map(static fn (mixed $id): int => (int) $id)
            ->all();

        DB::transaction(function () use ($permission): void {
            $permission->roles()->detach();
            $permission->delete();
        });

        $this->forgetPermissionCachesForRoleIds($roleIds);
    }

    public function syncRoles(Permission $permission, array $roleIds): void
    {
        $validatedRoleIds = Role::query()
            ->whereIn('id', $roleIds)
            ->pluck('id')
            ->map(static fn (mixed $id): int => (int) $id)
            ->all();

        $permission->roles()->sync($validatedRoleIds);
    }

    public function getAssignedRoleIds(Permission $permission): array
    {
        return $permission->roles()
            ->pluck('roles.id')
            ->map(static fn (mixed $id): int => (int) $id)
            ->all();
    }

    public function getAllRoleOptions(): Collection
    {
        return Role::query()
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);
    }

    private function syncTranslations(Permission $permission, PermissionData $data): void
    {
        if (! $this->supportsTranslations()) {
            return;
        }

        foreach ($data->getTranslations() as $locale => $translation) {
            $label = $translation['label'] ?? null;
            $description = $translation['description'] ?? null;

            if ($label === null && $description === null) {
                $permission->translations()
                    ->where('locale', $locale)
                    ->delete();

                continue;
            }

            $permission->translations()->updateOrCreate(
                ['locale' => $locale],
                [
                    'label' => $label ?? ($data->label ?? $permission->name),
                    'description' => $description,
                ],
            );
        }
    }

    /**
     * @param array<int, int|string> $roleIds
     */
    private function forgetPermissionCachesForRoleIds(array $roleIds): void
    {
        $normalizedRoleIds = array_values(array_unique(array_map('intval', $roleIds)));

        if ($normalizedRoleIds === []) {
            return;
        }

        $assignments = RoleAssignment::query()
            ->whereIn('role_id', $normalizedRoleIds)
            ->get(['subject_type', 'subject_id']);

        foreach ($assignments as $assignment) {
            Cache::forget(
                'rbac.permissions.' . md5($assignment->subject_type . ':' . (string) $assignment->subject_id)
            );
        }
    }

    private function supportsTranslations(): bool
    {
        return Schema::hasTable('permission_translations');
    }
}