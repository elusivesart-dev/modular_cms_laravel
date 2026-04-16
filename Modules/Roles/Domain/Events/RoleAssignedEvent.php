<?php

declare(strict_types=1);

namespace Modules\Roles\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Roles\Domain\Contracts\RoleEntityInterface;

final class RoleAssignedEvent
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public RoleEntityInterface $role,
        public string $subjectType,
        public int|string $subjectId,
    ) {
    }
}