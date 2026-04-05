<?php

declare(strict_types=1);

namespace App\Core\Modules\Discovery;

use App\Core\Modules\Manifest\ModuleManifest;

final class ModuleDiscovery
{
    public function discover(): array
    {
        $modules = [];
        $path = base_path('Modules');

        foreach (scandir($path) as $module) {
            if ($module === '.' || $module === '..') {
                continue;
            }

            $manifestPath = $path.'/'.$module.'/module.json';

            if (!file_exists($manifestPath)) {
                continue;
            }

            $data = json_decode(file_get_contents($manifestPath), true);

            $modules[] = new ModuleManifest(
                $data['name'],
                $data['version'],
                $data['dependencies'] ?? [],
                $data['provider']
            );
        }

        return $modules;
    }
}