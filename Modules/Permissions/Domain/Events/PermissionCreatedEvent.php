<?php

declare(strict_types=1);

namespace Modules\Permissions\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Permissions\Infrastructure\Models\Permission;

final class PermissionCreatedEvent
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public Permission $permission)
    {
    }
}