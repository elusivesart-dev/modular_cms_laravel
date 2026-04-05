<?php

declare(strict_types=1);

namespace Modules\Audit\Listeners;

use App\Core\Audit\Services\AuditLogger;
use Modules\Permissions\Domain\Events\PermissionCreatedEvent;
use Modules\Permissions\Domain\Events\PermissionDeletedEvent;
use Modules\Permissions\Domain\Events\PermissionsSyncedToRoleEvent;
use Modules\Permissions\Domain\Events\PermissionUpdatedEvent;

final class RecordPermissionAuditTrail
{
    public function __construct(
        private readonly AuditLogger $logger,
    ) {
    }

    public function handlePermissionCreated(PermissionCreatedEvent $event): void
    {
        $this->logger->log(
            'permissions.created',
            $event->permission,
            [
                'permission' => (string) $event->permission->name,
            ],
        );
    }

    public function handlePermissionUpdated(PermissionUpdatedEvent $event): void
    {
        $this->logger->log(
            'permissions.updated',
            $event->permission,
            [
                'permission' => (string) $event->permission->name,
            ],
        );
    }

    public function handlePermissionDeleted(PermissionDeletedEvent $event): void
    {
        $this->logger->log(
            'permissions.deleted',
            null,
            [
                'permission' => (string) $event->permissionName,
            ],
        );
    }

    public function handlePermissionsSyncedToRole(PermissionsSyncedToRoleEvent $event): void
    {
        $this->logger->log(
            'permissions.synced_to_role',
            $event->role,
            [
                'role' => (string) $event->role->name,
                'role_slug' => (string) $event->role->slug,
                'permission_ids' => $event->permissionIds,
            ],
        );
    }
}