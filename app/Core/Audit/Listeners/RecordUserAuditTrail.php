<?php

declare(strict_types=1);

namespace App\Core\Audit\Listeners;

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
                'user_id' => (int) $event->user->getKey(),
                'email' => $event->user->email,
                'is_active' => (bool) $event->user->is_active,
            ],
            'User created',
        );
    }

    public function handleUserUpdated(UserUpdatedEvent $event): void
    {
        $this->logger->log(
            'users.updated',
            $event->user,
            [
                'user_id' => (int) $event->user->getKey(),
                'email' => $event->user->email,
                'is_active' => (bool) $event->user->is_active,
            ],
            'User updated',
        );
    }

    public function handleUserDeleted(UserDeletedEvent $event): void
    {
        $this->logger->log(
            'users.deleted',
            null,
            [
                'user_id' => $event->userId,
                'email' => $event->email,
                'name' => $event->name,
            ],
            'User deleted',
        );
    }
}