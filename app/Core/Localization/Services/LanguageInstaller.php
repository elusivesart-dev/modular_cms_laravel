<?php

declare(strict_types=1);

namespace App\Core\Localization\Services;

use App\Core\Localization\Contracts\LanguageInstallerInterface;
use App\Core\Localization\Contracts\LanguageManifestReaderInterface;
use App\Core\Localization\Contracts\LanguageRepositoryInterface;
use App\Core\Localization\Exceptions\LanguageAlreadyExistsException;
use App\Core\Localization\Infrastructure\Filesystem\LanguageDirectoryResolver;
use App\Core\Localization\Infrastructure\Filesystem\LanguageFilesystemValidator;
use App\Core\Localization\Models\Language;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

final readonly class LanguageInstaller implements LanguageInstallerInterface
{
    public function __construct(
        private LanguageFilesystemValidator $validator,
        private LanguageManifestReaderInterface $manifestReader,
        private LanguageDirectoryResolver $directoryResolver,
        private LanguageRepositoryInterface $languages,
    ) {
    }

    public function installFromDirectory(string $directory, bool $activate = true): Language
    {
        $this->validator->validateInstallableDirectory($directory);

        $manifest = $this->manifestReader->readFromDirectory($directory);
        $targetDirectory = $this->directoryResolver->resolveInstalledDirectory($manifest->code);

        if ($this->languages->findByCode($manifest->code) !== null || is_dir($targetDirectory)) {
            throw new LanguageAlreadyExistsException(
                'A language with code [' . $manifest->code . '] is already installed.'
            );
        }

        File::ensureDirectoryExists(dirname($targetDirectory));

        if (!File::copyDirectory($directory, $targetDirectory)) {
            throw new LanguageAlreadyExistsException('Unable to copy the language package to the install directory.');
        }

        $language = $this->languages->upsertFromManifest(
            manifest: $manifest,
            installedPath: $targetDirectory,
            isSystem: false,
            isActive: $activate,
        );

        Cache::forget((string) config('localization.cache.key', 'core.localization.available_languages'));

        return $language;
    }
}