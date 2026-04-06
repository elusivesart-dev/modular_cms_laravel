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

    public function all(): array
    {
        return array_map(
            static fn (object $role): RoleOptionData => new RoleOptionData(
                name: (string) $role->name,
                slug: (string) $role->slug,
                description: $role->description !== null ? (string) $role->description : null,
                isSystem: (bool) $role->is_system,
            ),
            $this->roles->paginate(1000)->items(),
        );
    }
}