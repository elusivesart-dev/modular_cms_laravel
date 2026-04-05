<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Events;

abstract class DomainEvent
{
    protected \DateTimeImmutable $occurredAt;

    public function __construct()
    {
        $this->occurredAt = new \DateTimeImmutable();
    }

    public function occurredAt(): \DateTimeImmutable
    {
        return $this->occurredAt;
    }
}