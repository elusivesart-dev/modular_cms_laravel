<?php

declare(strict_types=1);

namespace App\Core\Localization\Services;

use App\Core\Localization\Contracts\LanguageManifestReaderInterface;
use App\Core\Localization\Contracts\LanguageRepositoryInterface;
use App\Core\Localization\Contracts\LanguageSynchronizerInterface;

final readonly class LanguageSynchronizer implements LanguageSynchronizerInterface
{
    public function __construct(
        private LanguageManifestReaderInterface $manifestReader,
        private LanguageRepositoryInterface $languages,
    ) {
    }

    public function syncFallbackLanguages(): void
    {
        $fallbackLanguages = (array) config('localization.fallback_languages', []);

        foreach ($fallbackLanguages as $code => $metadata) {
            $directory = (string) ($metadata['installed_path'] ?? '');

            if ($directory === '' || !is_dir($directory)) {
                continue;
            }

            $manifest = $this->manifestReader->readFromDirectory($directory);

            $this->languages->upsertFromManifest(
                manifest: $manifest,
                installedPath: $directory,
                isSystem: (bool) ($metadata['is_system'] ?? true),
                isActive: (bool) ($metadata['is_active'] ?? true),
            );
        }
    }
}