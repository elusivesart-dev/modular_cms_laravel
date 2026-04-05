<?php

declare(strict_types=1);

namespace App\Core\Localization\Infrastructure\Filesystem;

use App\Core\Localization\Exceptions\InvalidLanguageManifestException;

final class LanguageFilesystemValidator
{
    public function validateInstallableDirectory(string $directory): void
    {
        if (!is_dir($directory)) {
            throw new InvalidLanguageManifestException('Language directory does not exist: ' . $directory);
        }

        $manifest = rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'language.json';

        if (!is_file($manifest)) {
            throw new InvalidLanguageManifestException('language.json is missing in directory: ' . $directory);
        }

        $uiFile = rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'ui.php';

        if (!is_file($uiFile)) {
            throw new InvalidLanguageManifestException('ui.php is missing in directory: ' . $directory);
        }
    }
}