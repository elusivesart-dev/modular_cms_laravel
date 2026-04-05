<?php

declare(strict_types=1);

namespace App\Core\Modules\Registry;

use App\Core\Modules\Manifest\ModuleManifest;

final class ModuleRegistry
{
    private array $modules = [];

    public function register(ModuleManifest $manifest): void
    {
        $this->modules[$manifest->name] = $manifest;
    }

    public function all(): array
    {
        return $this->modules;
    }

    public function get(string $name): ?ModuleManifest
    {
        return $this->modules[$name] ?? null;
    }
}