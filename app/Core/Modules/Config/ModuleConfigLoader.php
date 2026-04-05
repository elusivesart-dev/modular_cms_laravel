<?php

declare(strict_types=1);

namespace App\Core\Modules\Config;

use Illuminate\Contracts\Config\Repository;
use RuntimeException;

final class ModuleConfigLoader
{
    public function __construct(
        private readonly Repository $config,
    ) {
    }

    public function load(string $modulePath): void
    {
        $configPath = rtrim($modulePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'Config';

        if (!is_dir($configPath)) {
            return;
        }

        $moduleName = basename(rtrim($modulePath, DIRECTORY_SEPARATOR));

        foreach (glob($configPath . DIRECTORY_SEPARATOR . '*.php') ?: [] as $file) {
            $configKey = pathinfo($file, PATHINFO_FILENAME);
            $loadedConfig = require $file;

            if (!is_array($loadedConfig)) {
                throw new RuntimeException(sprintf(
                    'Module config file [%s] must return an array.',
                    $file
                ));
            }

            $namespacedKey = sprintf('modules.%s.%s', strtolower($moduleName), $configKey);
            $existingConfig = $this->config->get($namespacedKey, []);

            $this->config->set(
                $namespacedKey,
                $this->mergeConfig($existingConfig, $loadedConfig),
            );
        }
    }

    /**
     * @param array<string, mixed> $existing
     * @param array<string, mixed> $incoming
     * @return array<string, mixed>
     */
    private function mergeConfig(array $existing, array $incoming): array
    {
        foreach ($incoming as $key => $value) {
            if (is_array($value) && isset($existing[$key]) && is_array($existing[$key])) {
                $existing[$key] = $this->mergeConfig($existing[$key], $value);
                continue;
            }

            $existing[$key] = $value;
        }

        return $existing;
    }
}