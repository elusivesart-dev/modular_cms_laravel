<?php

declare(strict_types=1);

namespace App\Core\Scheduler;

use Illuminate\Console\Scheduling\Schedule;

final class SchedulerManager
{
    public function __construct(
        private readonly Schedule $schedule
    ) {}

    public function register(): void
    {
    }
}