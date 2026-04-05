<?php

declare(strict_types=1);

namespace App\Core\Installer\Modules;

use App\Core\Modules\Activator\ModuleActivator;
use App\Core\Modules\Dependency\ModuleDependencyResolver;
use App\Core\Modules\Discovery\ModuleDiscovery;
use App\Core\Modules\Manifest\ModuleManifest;
use App\Core\Modules\Registry\ModuleRegistry;

final class ModuleInitializer
{
    public function __construct(
        private readonly ModuleDiscovery $discovery,
        private readonly ModuleRegistry $registry,
        private readonly ModuleDependencyResolver $resolver,
        private readonly ModuleActivator $activator,
    ) {
    }

    public function initialize(): void
    {
        $discoveredModules = $this->discovery->discover();
        $orderedModules = $this->resolver->resolveLoadOrder($discoveredModules);

        foreach ($orderedModules as $manifest) {
            if (!$manifest instanceof ModuleManifest) {
                continue;
            }

            $this->registry->register($manifest);
        }

        foreach ($orderedModules as $manifest) {
            $this->activator->activate($manifest->name);
        }
    }
}