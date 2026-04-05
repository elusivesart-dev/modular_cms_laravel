<?php

declare(strict_types=1);

namespace App\Console;

use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Core\Installer\Console\InstallCommand;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        InstallCommand::class,
    ];

    protected function schedule($schedule): void
    {
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}