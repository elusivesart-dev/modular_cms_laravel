<?php

declare(strict_types=1);

namespace App\Core\Localization\Infrastructure\Filesystem;

use App\Core\Localization\Exceptions\InvalidLanguageManifestException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

final class LanguagePackageRootResolver
{
    public function resolve(string $extractedDirectory): string
    {
        $extractedDirectory = rtrim($extractedDirectory, DIRECTORY_SEPARATOR);

        if ($this->isLanguageRoot($extractedDirectory)) {
            return $extractedDirectory;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($extractedDirectory, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            if (!$item instanceof SplFileInfo || !$item->isDir()) {
                continue;
            }

            $path = $item->getPathname();

            if ($this->isLanguageRoot($path)) {
                return $path;
            }
        }

        throw new InvalidLanguageManifestException(
            'Unable to locate a valid language package root inside: ' . $extractedDirectory
        );
    }

    private function isLanguageRoot(string $directory): bool
    {
        return is_file($directory . DIRECTORY_SEPARATOR . 'language.json')
            && is_file($directory . DIRECTORY_SEPARATOR . 'ui.php');
    }
}