<?php

declare(strict_types=1);

namespace Modules\Roles\Application\Services;

use App\Core\RBAC\Contracts\RoleCatalogInterface;
use App\Core\RBAC\DTO\RoleOptionData;
use Modules\Roles\Domain\Contracts\RoleEntityInterface;
use Modules\Roles\Domain\Contracts\RoleRepositoryInterface;

final class CoreRoleCatalogService implements RoleCatalogInterface
{
    public function __construct(
        private readonly RoleRepositoryInterface $roles,
        private readonly RoleTranslationService $translations,
    ) {
    }

    public function all(): array
    {
        return array_map(
            fn (RoleEntityInterface $role): RoleOptionData => new RoleOptionData(
                id: (int) $role->getKey(),
                name: $this->translations->displayName($role),
                slug: $role->getSlug(),
                description: $this->translations->displayDescription($role),
                isSystem: $role->isSystem(),
            ),
            $this->roles->paginate(1000)->items(),
        );
    }
}