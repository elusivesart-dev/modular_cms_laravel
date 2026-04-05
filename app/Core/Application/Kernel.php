<?php

declare(strict_types=1);

namespace App\Core\Application;

use App\Core\Boot\BootManager;
use App\Core\Cache\CacheManager;
use App\Core\Config\ConfigManager;
use App\Core\Container\ContainerManager;
use App\Core\Encryption\EncryptionManager;
use App\Core\Environment\EnvironmentLoader;
use App\Core\Filesystem\FilesystemManager;
use App\Core\Logging\LoggingManager;
use App\Core\Queue\QueueManager;
use App\Core\Scheduler\SchedulerManager;
use Illuminate\Contracts\Foundation\Application;

final class Kernel
{
    private bool $booted = false;

    public function __construct(
        private readonly Application $app,
        private readonly EnvironmentLoader $environmentLoader,
        private readonly ConfigManager $configManager,
        private readonly ContainerManager $containerManager,
        private readonly BootManager $bootManager,
        private readonly LoggingManager $loggingManager,
        private readonly CacheManager $cacheManager,
        private readonly QueueManager $queueManager,
        private readonly SchedulerManager $schedulerManager,
        private readonly FilesystemManager $filesystemManager,
        private readonly EncryptionManager $encryptionManager
    ) {
    }

    public function boot(): void
    {
        if ($this->booted) {
            return;
        }

        $this->environmentLoader->load();
        $this->configManager->load();
        $this->containerManager->register();
        $this->loggingManager->register();
        $this->cacheManager->register();
        $this->queueManager->register();
        $this->schedulerManager->register();
        $this->filesystemManager->register();
        $this->encryptionManager->register();
        $this->bootManager->boot();

        $this->booted = true;
    }
}