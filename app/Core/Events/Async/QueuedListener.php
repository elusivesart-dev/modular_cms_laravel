<?php

declare(strict_types=1);

namespace App\Core\Events\Async;

use App\Core\Events\Contracts\EventInterface;
use App\Core\Events\Contracts\ListenerInterface;
use Illuminate\Contracts\Queue\ShouldQueue;

abstract class QueuedListener implements ListenerInterface, ShouldQueue
{
    abstract public function handle(EventInterface $event): void;
}