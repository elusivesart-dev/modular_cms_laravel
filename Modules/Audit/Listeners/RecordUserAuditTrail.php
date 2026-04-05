<?php

declare(strict_types=1);

namespace Modules\Audit\Listeners;

use App\Core\Audit\Services\AuditLogger;
use Modules\Users\Domain\Events\UserCreatedEvent;
use Modules\Users\Domain\Events\UserDeletedEvent;
use Modules\Users\Domain\Events\UserUpdatedEvent;

final class RecordUserAuditTrail
{
    public function __construct(
        private readonly AuditLogger $logger,
    ) {
    }

    public function handleUserCreated(UserCreatedEvent $event): void
    {
        $this->logger->log(
            'users.created',
            $event->user,
            [
                'user' => (string) $event->user->name,
                'email' => (string) $event->user->email,
                'is_active' => (bool) $event->user->is_active,
            ],
        );
    }

    public function handleUserUpdated(UserUpdatedEvent $event): void
    {
        $this->logger->log(
            'users.updated',
            $event->user,
            [
                'user' => (string) $event->user->name,
                'email' => (string) $event->user->email,
                'is_active' => (bool) $event->user->is_active,
            ],
        );
    }

    public function handleUserDeleted(UserDeletedEvent $event): void
    {
        $this->logger->log(
            'users.deleted',
            null,
            [
                'user' => (string) $event->name,
                'email' => (string) $event->email,
            ],
        );
    }
}