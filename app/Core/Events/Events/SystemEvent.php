<?php

declare(strict_types=1);

namespace App\Core\Events\Events;

use App\Core\Events\Contracts\EventInterface;

final class SystemEvent implements EventInterface
{
    public function __construct(
        private readonly string $name,
        private readonly array $payload = []
    ) {}

    public function name(): string
    {
        return $this->name;
    }

    public function payload(): array
    {
        return $this->payload;
    }
}