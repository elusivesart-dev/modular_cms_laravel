<?php

declare(strict_types=1);

namespace App\Core\Modules\Deactivator;

use App\Core\Modules\Registry\ModuleRegistry;
use RuntimeException;

final class ModuleDeactivator
{
    public function __construct(
        private readonly ModuleRegistry $registry
    ) {}

    public function deactivate(string $module): void
    {
        if (!$this->registry->get($module)) {
            throw new RuntimeException("Module not found: {$module}");
        }
    }
}