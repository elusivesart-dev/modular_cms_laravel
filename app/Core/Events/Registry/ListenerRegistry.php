<?php

declare(strict_types=1);

namespace App\Core\Events\Registry;

use App\Core\Events\Contracts\ListenerInterface;

final class ListenerRegistry
{
    private array $listeners = [];

    public function register(string $event, ListenerInterface $listener): void
    {
        $this->listeners[$event][] = $listener;
    }

    public function listeners(string $event): array
    {
        return $this->listeners[$event] ?? [];
    }
}