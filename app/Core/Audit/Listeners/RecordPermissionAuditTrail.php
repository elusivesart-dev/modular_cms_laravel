<?php

declare(strict_types=1);

namespace App\Core\Audit\Listeners;

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
                'permission_id' => (int) $event->permission->getKey(),
                'permission_name' => $event->permission->name,
            ],
            'Permission created',
        );
    }

    public function handlePermissionUpdated(PermissionUpdatedEvent $event): void
    {
        $this->logger->log(
            'permissions.updated',
            $event->permission,
            [
                'permission_id' => (int) $event->permission->getKey(),
                'permission_name' => $event->permission->name,
            ],
            'Permission updated',
        );
    }

    public function handlePermissionDeleted(PermissionDeletedEvent $event): void
    {
        $this->logger->log(
            'permissions.deleted',
            null,
            [
                'permission_id' => $event->permissionId,
                'permission_name' => $event->permissionName,
            ],
            'Permission deleted',
        );
    }

    public function handlePermissionsSyncedToRole(PermissionsSyncedToRoleEvent $event): void
    {
        $this->logger->log(
            'permissions.synced_to_role',
            $event->role,
            [
                'role_id' => (int) $event->role->getKey(),
                'role_slug' => $event->role->slug,
                'permission_ids' => $event->permissionIds,
            ],
            'Permissions synced to role',
        );
    }
}