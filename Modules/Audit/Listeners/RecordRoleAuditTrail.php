<?php

declare(strict_types=1);

namespace Modules\Audit\Listeners;

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
                'user_id' => (string) $event->subjectId,
                'role' => (string) $event->role->name,
                'role_slug' => (string) $event->role->slug,
                'subject_type' => (string) $event->subjectType,
            ],
        );
    }

    public function handleRoleRevoked(RoleRevokedEvent $event): void
    {
        $this->logger->log(
            'roles.revoked',
            $event->role,
            [
                'user_id' => (string) $event->subjectId,
                'role' => (string) $event->role->name,
                'role_slug' => (string) $event->role->slug,
                'subject_type' => (string) $event->subjectType,
            ],
        );
    }

    public function handleRoleCreated(RoleCreatedEvent $event): void
    {
        $this->logger->log(
            'roles.created',
            $event->role,
            [
                'role' => (string) $event->role->name,
                'role_slug' => (string) $event->role->slug,
                'is_system' => (bool) $event->role->is_system,
            ],
        );
    }

    public function handleRoleUpdated(RoleUpdatedEvent $event): void
    {
        $this->logger->log(
            'roles.updated',
            $event->role,
            [
                'role' => (string) $event->role->name,
                'role_slug' => (string) $event->role->slug,
                'is_system' => (bool) $event->role->is_system,
            ],
        );
    }

    public function handleRoleDeleted(RoleDeletedEvent $event): void
    {
        $this->logger->log(
            'roles.deleted',
            null,
            [
                'role' => (string) $event->role->name,
                'role_slug' => (string) $event->role->slug,
                'is_system' => (bool) $event->role->is_system,
            ],
        );
    }
}