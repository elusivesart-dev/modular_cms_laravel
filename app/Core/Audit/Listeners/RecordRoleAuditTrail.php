<?php

declare(strict_types=1);

namespace App\Core\Audit\Listeners;

use App\Core\Audit\Services\AuditLogger;
use Modules\Roles\Domain\Events\RoleAssignedEvent;
use Modules\Roles\Domain\Events\RoleCreatedEvent;
use Modules\Roles\Domain\Events\RoleDeletedEvent;
use Modules\Roles\Domain\Events\RoleRevokedEvent;
use Modules\Roles\Domain\Events\RoleUpdatedEvent;

final class RecordRoleAuditTrail
{
    public function __construct(
        private readonly AuditLogger $logger,
    ) {
    }

    public function handleRoleAssigned(RoleAssignedEvent $event): void
    {
        $this->logger->log(
            'roles.assigned',
            $event->role,
            [
                'role_id' => (int) $event->role->getKey(),
                'role_slug' => $event->role->slug,
                'subject_type' => $event->subjectType,
                'subject_id' => (string) $event->subjectId,
            ],
            'Role assigned to subject',
        );
    }

    public function handleRoleRevoked(RoleRevokedEvent $event): void
    {
        $this->logger->log(
            'roles.revoked',
            $event->role,
            [
                'role_id' => (int) $event->role->getKey(),
                'role_slug' => $event->role->slug,
                'subject_type' => $event->subjectType,
                'subject_id' => (string) $event->subjectId,
            ],
            'Role revoked from subject',
        );
    }

    public function handleRoleCreated(RoleCreatedEvent $event): void
    {
        $this->logger->log(
            'roles.created',
            $event->role,
            [
                'role_id' => (int) $event->role->getKey(),
                'role_slug' => $event->role->slug,
                'is_system' => (bool) $event->role->is_system,
            ],
            'Role created',
        );
    }

    public function handleRoleUpdated(RoleUpdatedEvent $event): void
    {
        $this->logger->log(
            'roles.updated',
            $event->role,
            [
                'role_id' => (int) $event->role->getKey(),
                'role_slug' => $event->role->slug,
                'is_system' => (bool) $event->role->is_system,
            ],
            'Role updated',
        );
    }

    public function handleRoleDeleted(RoleDeletedEvent $event): void
    {
        $this->logger->log(
            'roles.deleted',
            $event->role,
            [
                'role_id' => (int) $event->role->getKey(),
                'role_slug' => $event->role->slug,
                'is_system' => (bool) $event->role->is_system,
            ],
            'Role deleted',
        );
    }
}