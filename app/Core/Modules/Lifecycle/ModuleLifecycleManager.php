<?php

declare(strict_types=1);

namespace App\Core\Modules\Lifecycle;

use App\Core\Modules\Loader\ModuleLoader;

final class ModuleLifecycleManager
{
    public function __construct(
        private readonly ModuleLoader $loader
    ) {}

    public function boot(): void
    {
        $this->loader->load();
    }
}