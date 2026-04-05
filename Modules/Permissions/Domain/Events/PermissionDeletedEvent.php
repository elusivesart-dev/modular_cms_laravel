<?php

declare(strict_types=1);

namespace Modules\Permissions\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class PermissionDeletedEvent
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public int $permissionId,
        public string $permissionName,
    ) {
    }
}