<?php

declare(strict_types=1);

namespace Modules\Roles\Infrastructure\Repositories;

use DomainException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Roles\Domain\Contracts\RoleEntityInterface;
use Modules\Roles\Domain\Contracts\RoleRepositoryInterface;
use Modules\Roles\Domain\DTOs\RoleData;
use Modules\Roles\Infrastructure\Models\Role;

final class EloquentRoleRepository implements RoleRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        $query = Role::query()
            ->with(['translations'])
            ->orderBy('name');

        if (Schema::hasTable('permissions') && Schema::hasTable('role_permissions')) {
            $query->withCount('permissions');
        }

        $paginator = $query->paginate($perPage);

        if (! Schema::hasTable('permissions') || ! Schema::hasTable('role_permissions')) {
            $collection = $paginator->getCollection()->map(static function (Role $role): Role {
                $role->setAttribute('permissions_count', 0);

                return $role;
            });

            $paginator->setCollection($collection);
        }

        return $paginator;
    }

    public function findById(int $id): ?RoleEntityInterface
    {
        return Role::query()
            ->with(['translations'])
            ->find($id);
    }

    public function findBySlug(string $slug): ?RoleEntityInterface
    {
        return Role::query()
            ->with(['translations'])
            ->where('slug', $slug)
            ->first();
    }

    public function create(RoleData $data): RoleEntityInterface
    {
        /** @var Role $role */
        $role = DB::transaction(function () use ($data): Role {
            /** @var Role $role */
            $role = Role::query()->create([
                'name' => $data->resolveLegacyName(
                    (string) app()->getLocale(),
                    (string) config('app.fallback_locale', 'en'),
                ),
                'slug' => $data->slug,
                'description' => $data->resolveLegacyDescription(
                    (string) app()->getLocale(),
                    (string) config('app.fallback_locale', 'en'),
                ),
                'is_system' => $data->is_system,
            ]);

            $this->syncTranslations($role, $data);

            return $role->fresh(['translations']);
        });

        return $role;
    }

    public function update(RoleEntityInterface $role, RoleData $data): RoleEntityInterface
    {
        $model = $this->toModel($role);

        /** @var Role $updated */
        $updated = DB::transaction(function () use ($model, $data): Role {
            $model->update([
                'name' => $data->resolveLegacyName(
                    (string) app()->getLocale(),
                    (string) config('app.fallback_locale', 'en'),
                ),
                'slug' => $data->slug,
                'description' => $data->resolveLegacyDescription(
                    (string) app()->getLocale(),
                    (string) config('app.fallback_locale', 'en'),
                ),
                'is_system' => $data->is_system,
            ]);

            $this->syncTranslations($model, $data);

            return $model->fresh(['translations']);
        });

        return $updated;
    }

    public function delete(RoleEntityInterface $role): void
    {
        $this->toModel($role)->delete();
    }

    private function syncTranslations(Role $role, RoleData $data): void
    {
        foreach ($data->getTranslations() as $locale => $translation) {
            $name = $translation['name'] ?? null;
            $description = $translation['description'] ?? null;

            if ($name === null && $description === null) {
                $role->translations()
                    ->where('locale', $locale)
                    ->delete();

                continue;
            }

            $role->translations()->updateOrCreate(
                ['locale' => $locale],
                [
                    'name' => $name ?? $data->name,
                    'description' => $description,
                ],
            );
        }
    }

    private function toModel(RoleEntityInterface $role): Role
    {
        if (! $role instanceof Role) {
            throw new DomainException('Unsupported role entity implementation.');
        }

        return $role;
    }
}