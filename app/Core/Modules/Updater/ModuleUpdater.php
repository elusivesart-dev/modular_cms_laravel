<?php

declare(strict_types=1);

namespace App\Core\Modules\Updater;

use App\Core\Modules\Registry\ModuleRegistry;
use App\Core\Modules\Versioning\ModuleVersionManager;
use App\Core\Modules\Migration\ModuleMigrationRunner;
use RuntimeException;

final class ModuleUpdater
{
    public function __construct(
        private readonly ModuleRegistry $registry,
        private readonly ModuleVersionManager $versionManager,
        private readonly ModuleMigrationRunner $migrations
    ) {}

    public function update(string $module): void
    {
        $manifest = $this->registry->get($module);

        if (!$manifest) {
            throw new RuntimeException("Module not found: {$module}");
        }

        if (!$this->versionManager->validate($manifest->version)) {
            throw new RuntimeException("Invalid module version");
        }

        $path = base_path("Modules/{$module}/Infrastructure/Database/Migrations");

        if (is_dir($path)) {
            $this->migrations->run($path);
        }
    }
}