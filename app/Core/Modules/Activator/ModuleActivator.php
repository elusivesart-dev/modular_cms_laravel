<?php

declare(strict_types=1);

namespace App\Core\Modules\Activator;

use App\Core\Modules\Registry\ModuleRegistry;
use App\Core\Modules\Migration\ModuleMigrationRunner;
use App\Core\Modules\Seeder\ModuleSeederRunner;
use App\Core\Modules\Dependency\ModuleDependencyResolver;
use RuntimeException;

final class ModuleActivator
{
    public function __construct(
        private readonly ModuleRegistry $registry,
        private readonly ModuleDependencyResolver $resolver,
        private readonly ModuleMigrationRunner $migrations,
        private readonly ModuleSeederRunner $seeders
    ) {}

    public function activate(string $module): void
    {
        $manifest = $this->registry->get($module);

        if (!$manifest) {
            throw new RuntimeException("Module not found: {$module}");
        }

        $this->resolver->validate($module);

        $path = base_path("Modules/{$module}");

        $migrationPath = "{$path}/Infrastructure/Database/Migrations";

        if (is_dir($migrationPath)) {
            $this->migrations->run($migrationPath);
        }

        $seederClass = "Modules\\{$module}\\Database\\Seeders\\{$module}Seeder";

        if (class_exists($seederClass)) {
            $this->seeders->run($seederClass);
        }
    }
}