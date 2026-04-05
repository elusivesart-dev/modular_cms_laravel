<?php

declare(strict_types=1);

namespace App\Core\Events\Dispatcher;

use App\Core\Events\Contracts\EventInterface;
use App\Core\Events\Registry\ListenerRegistry;

final class EventDispatcher
{
    public function __construct(
        private readonly ListenerRegistry $registry
    ) {}

    public function dispatch(EventInterface $event): void
    {
        foreach ($this->registry->listeners($event->name()) as $listener) {
            $listener->handle($event);
        }
    }
}