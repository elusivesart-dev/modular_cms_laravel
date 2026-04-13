<?php

declare(strict_types=1);

namespace App\Core\Modules\Discovery;

use App\Core\Modules\Manifest\ModuleManifest;
use JsonException;
use RuntimeException;

final class ModuleDiscovery
{
    /**
     * @return array<int, ModuleManifest>
     */
    public function discover(): array
    {
        $modules = [];
        $path = base_path('Modules');

        if (! is_dir($path)) {
            return [];
        }

        $entries = scandir($path);

        if ($entries === false) {
            throw new RuntimeException(__('core-modules::modules.errors.modules_directory_scan_failed', [
                'path' => $path,
            ]));
        }

        $directories = array_values(array_filter(
            $entries,
            static fn (string $entry): bool => $entry !== '.' && $entry !== '..'
        ));

        sort($directories, SORT_NATURAL | SORT_FLAG_CASE);

        foreach ($directories as $module) {
            $modulePath = $path . DIRECTORY_SEPARATOR . $module;

            if (! is_dir($modulePath)) {
                continue;
            }

            $manifestPath = $modulePath . DIRECTORY_SEPARATOR . 'module.json';

            if (! is_file($manifestPath)) {
                continue;
            }

            $contents = file_get_contents($manifestPath);

            if ($contents === false) {
                throw new RuntimeException(__('core-modules::modules.errors.module_manifest_read_failed', [
                    'path' => $manifestPath,
                ]));
            }

            try {
                $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            } catch (JsonException $exception) {
                throw new RuntimeException(
                    __('core-modules::modules.errors.invalid_manifest_json', [
                        'path' => $manifestPath,
                    ]),
                    previous: $exception,
                );
            }

            if (! is_array($data)) {
                throw new RuntimeException(__('core-modules::modules.errors.invalid_manifest_root', [
                    'path' => $manifestPath,
                ]));
            }

            $modules[] = ModuleManifest::fromArray($data, $manifestPath);
        }

        return $modules;
    }
}