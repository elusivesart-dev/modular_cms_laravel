<?php

declare(strict_types=1);

namespace App\Core\Localization\Infrastructure\Filesystem;

final class LanguageDirectoryResolver
{
    public function resolveInstalledDirectory(string $code): string
    {
        return rtrim((string) config('localization.paths.packages_installed'), DIRECTORY_SEPARATOR)
            . DIRECTORY_SEPARATOR
            . $code;
    }
}