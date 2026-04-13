<?php

declare(strict_types=1);

namespace App\Core\Modules\Manifest;

use RuntimeException;

final class ModuleManifest
{
    /**
     * @param array<int, string> $dependencies
     */
    public function __construct(
        public readonly string $name,
        public readonly string $version,
        public readonly array $dependencies,
        public readonly string $provider,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data, string $manifestPath): self
    {
        $name = $data['name'] ?? null;
        $version = $data['version'] ?? null;
        $provider = $data['provider'] ?? null;
        $dependencies = $data['dependencies'] ?? [];

        if (! is_string($name) || trim($name) === '') {
            throw new RuntimeException(__('core-modules::modules.errors.invalid_manifest_name', [
                'path' => $manifestPath,
            ]));
        }

        if (! is_string($version) || trim($version) === '') {
            throw new RuntimeException(__('core-modules::modules.errors.invalid_manifest_version', [
                'path' => $manifestPath,
            ]));
        }

        if (! is_string($provider) || trim($provider) === '') {
            throw new RuntimeException(__('core-modules::modules.errors.invalid_manifest_provider', [
                'path' => $manifestPath,
            ]));
        }

        if (! is_array($dependencies)) {
            throw new RuntimeException(__('core-modules::modules.errors.invalid_manifest_dependencies_array', [
                'path' => $manifestPath,
            ]));
        }

        $normalizedDependencies = [];

        foreach ($dependencies as $dependency) {
            if (! is_string($dependency) || trim($dependency) === '') {
                throw new RuntimeException(__('core-modules::modules.errors.invalid_manifest_dependencies_items', [
                    'path' => $manifestPath,
                ]));
            }

            $normalizedDependencies[] = trim($dependency);
        }

        return new self(
            name: trim($name),
            version: trim($version),
            dependencies: array_values(array_unique($normalizedDependencies)),
            provider: trim($provider),
        );
    }
}