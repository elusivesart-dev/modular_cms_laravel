<?php

declare(strict_types=1);

namespace App\Core\Modules\Dependency;

use App\Core\Modules\Manifest\ModuleManifest;
use App\Core\Modules\Registry\ModuleRegistry;
use RuntimeException;

final class ModuleDependencyResolver
{
    public function __construct(
        private readonly ModuleRegistry $registry,
    ) {
    }

    public function validate(string $module): void
    {
        $manifest = $this->registry->get($module);

        if ($manifest === null) {
            throw new RuntimeException("Module manifest not found: {$module}");
        }

        foreach ($manifest->dependencies as $dependency) {
            if ($this->registry->get($dependency) === null) {
                throw new RuntimeException("Missing module dependency [{$dependency}] required by [{$module}]");
            }
        }
    }

    /**
     * @param array<int, ModuleManifest> $manifests
     * @return array<int, ModuleManifest>
     */
    public function resolveLoadOrder(array $manifests): array
    {
        $manifestMap = [];

        foreach ($manifests as $manifest) {
            $manifestMap[$manifest->name] = $manifest;
        }

        $ordered = [];
        $visiting = [];
        $visited = [];

        foreach ($manifestMap as $moduleName => $manifest) {
            $this->visit(
                moduleName: $moduleName,
                manifestMap: $manifestMap,
                ordered: $ordered,
                visiting: $visiting,
                visited: $visited,
            );
        }

        return array_values($ordered);
    }

    /**
     * @param array<string, ModuleManifest> $manifestMap
     * @param array<string, ModuleManifest> $ordered
     * @param array<string, bool> $visiting
     * @param array<string, bool> $visited
     */
    private function visit(
        string $moduleName,
        array $manifestMap,
        array &$ordered,
        array &$visiting,
        array &$visited,
    ): void {
        if (isset($visited[$moduleName])) {
            return;
        }

        if (isset($visiting[$moduleName])) {
            throw new RuntimeException("Circular module dependency detected involving [{$moduleName}]");
        }

        $manifest = $manifestMap[$moduleName] ?? null;

        if ($manifest === null) {
            throw new RuntimeException("Module manifest not found: {$moduleName}");
        }

        $visiting[$moduleName] = true;

        foreach ($manifest->dependencies as $dependency) {
            if (!isset($manifestMap[$dependency])) {
                throw new RuntimeException("Missing module dependency [{$dependency}] required by [{$moduleName}]");
            }

            $this->visit(
                moduleName: $dependency,
                manifestMap: $manifestMap,
                ordered: $ordered,
                visiting: $visiting,
                visited: $visited,
            );
        }

        unset($visiting[$moduleName]);
        $visited[$moduleName] = true;
        $ordered[$moduleName] = $manifest;
    }
}