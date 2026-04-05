<?php

declare(strict_types=1);

namespace App\Core\Events\Bus;

use App\Core\Events\Contracts\EventInterface;
use App\Core\Events\Dispatcher\EventDispatcher;

final class EventBus
{
    public function __construct(
        private readonly EventDispatcher $dispatcher
    ) {}

    public function emit(EventInterface $event): void
    {
        $this->dispatcher->dispatch($event);
    }
}