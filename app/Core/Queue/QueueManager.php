<?php

declare(strict_types=1);

namespace App\Core\Queue;

use Illuminate\Contracts\Queue\Factory;

final class QueueManager
{
    public function __construct(
        private readonly Factory $queue
    ) {}

    public function register(): void
    {
        $this->queue->connection();
    }
}