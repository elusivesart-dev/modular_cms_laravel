<?php

declare(strict_types=1);

namespace App\Core\Modules\Loader;

use App\Core\Modules\Dependency\ModuleDependencyResolver;
use App\Core\Modules\Discovery\ModuleDiscovery;
use App\Core\Modules\Registry\ModuleRegistry;
use Illuminate\Contracts\Foundation\Application;

final class ModuleLoader
{
    public function __construct(
        private readonly ModuleDiscovery $discovery,
        private readonly ModuleRegistry $registry,
        private readonly ModuleDependencyResolver $resolver,
        private readonly Application $app,
    ) {
    }

    public function load(): void
    {
        $discoveredModules = $this->discovery->discover();
        $orderedModules = $this->resolver->resolveLoadOrder($discoveredModules);

        foreach ($orderedModules as $manifest) {
            $this->registry->register($manifest);
        }

        foreach ($orderedModules as $module) {
            $this->resolver->validate($module->name);
            $this->app->register($module->provider);
        }
    }
}