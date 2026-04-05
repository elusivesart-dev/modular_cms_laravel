<?php

declare(strict_types=1);

namespace Modules\Roles\Application\Services;

use App\Core\RBAC\Contracts\RoleCatalogInterface;
use App\Core\RBAC\DTO\RoleOptionData;
use Modules\Roles\Domain\Contracts\RoleRepositoryInterface;

final class CoreRoleCatalogService implements RoleCatalogInterface
{
    public function __construct(
        private readonly RoleRepositoryInterface $roles,
    ) {
    }

    /**
     * @return array<int, RoleOptionData>
     */
    public function listForSelection(int $perPage = 1000): array
    {
        $items = $this->roles->paginate($perPage)->items();
        $result = [];

        foreach ($items as $role) {
            $result[] = new RoleOptionData(
                id: (int) $role->getKey(),
                name: (string) $role->name,
                slug: (string) $role->slug,
                description: $role->description !== null ? (string) $role->description : null,
                isSystem: (bool) $role->is_system,
            );
        }

        return $result;
    }
}
