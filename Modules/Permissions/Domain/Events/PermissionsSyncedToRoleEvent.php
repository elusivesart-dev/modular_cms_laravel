<?php

declare(strict_types=1);

namespace Modules\Permissions\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Roles\Infrastructure\Models\Role;

final class PermissionsSyncedToRoleEvent
{
    use Dispatchable;
    use SerializesModels;

    /**
     * @param array<int, int> $permissionIds
     */
    public function __construct(
        public Role $role,
        public array $permissionIds,
    ) {
    }
}