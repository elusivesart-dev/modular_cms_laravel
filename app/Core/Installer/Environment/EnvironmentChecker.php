<?php

declare(strict_types=1);

namespace App\Core\Installer\Environment;

use RuntimeException;

final class EnvironmentChecker
{
    public function check(): void
    {
        $this->checkPhpVersion();
        $this->checkExtensions();
        $this->checkWritableDirectories();
    }

    private function checkPhpVersion(): void
    {
        if (version_compare(PHP_VERSION, '8.2.0', '<')) {
            throw new RuntimeException('PHP 8.2 or higher is required.');
        }
    }

    private function checkExtensions(): void
    {
        $required = [
            'bcmath',
            'ctype',
            'fileinfo',
            'json',
            'mbstring',
            'openssl',
            'pdo',
            'tokenizer',
            'xml',
        ];

        foreach ($required as $extension) {
            if (!extension_loaded($extension)) {
                throw new RuntimeException(sprintf('Missing PHP extension: %s', $extension));
            }
        }
    }

    private function checkWritableDirectories(): void
    {
        $paths = [
            app()->storagePath(),
            app()->bootstrapPath('cache'),
        ];

        foreach ($paths as $path) {
            if (!is_dir($path)) {
                throw new RuntimeException(sprintf('Missing required path: %s', $path));
            }

            if (!is_writable($path)) {
                throw new RuntimeException(sprintf('Path is not writable: %s', $path));
            }
        }
    }
}