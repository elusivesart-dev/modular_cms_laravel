<?php

declare(strict_types=1);

namespace App\Core\RBAC\Contracts;

use App\Core\RBAC\DTO\RoleOptionData;

interface RoleCatalogInterface
{
    /**
     * @return array<int, RoleOptionData>
     */
    public function listForSelection(int $perPage = 1000): array;
}
