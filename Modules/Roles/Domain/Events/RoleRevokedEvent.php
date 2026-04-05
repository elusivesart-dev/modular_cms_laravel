<?php

declare(strict_types=1);

namespace Modules\Roles\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Roles\Infrastructure\Models\Role;

final class RoleRevokedEvent
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public Role $role,
        public string $subjectType,
        public int|string $subjectId,
    ) {
    }
}