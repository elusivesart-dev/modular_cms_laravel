<?php

declare(strict_types=1);

namespace App\Core\Logging;

use Illuminate\Log\LogManager;

final class LoggingManager
{
    public function __construct(
        private readonly LogManager $log
    ) {}

    public function register(): void
    {
        $this->log->info('Kernel logging initialized');
    }
}