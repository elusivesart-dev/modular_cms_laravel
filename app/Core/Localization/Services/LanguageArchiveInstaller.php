<?php

declare(strict_types=1);

namespace App\Core\Localization\Services;

use App\Core\Localization\Contracts\LanguageArchiveInstallerInterface;
use App\Core\Localization\Contracts\LanguageInstallerInterface;
use App\Core\Localization\Infrastructure\Filesystem\LanguagePackageExtractor;
use App\Core\Localization\Infrastructure\Filesystem\LanguagePackageRootResolver;
use App\Core\Localization\Models\Language;
use Illuminate\Support\Facades\File;

final readonly class LanguageArchiveInstaller implements LanguageArchiveInstallerInterface
{
    public function __construct(
        private LanguagePackageExtractor $extractor,
        private LanguagePackageRootResolver $rootResolver,
        private LanguageInstallerInterface $installer,
    ) {
    }

    public function installFromArchive(string $archivePath): Language
    {
        $temporaryRoot = rtrim((string) config('localization.paths.packages_tmp'), DIRECTORY_SEPARATOR);
        $extractionDirectory = $temporaryRoot . DIRECTORY_SEPARATOR . 'extract_' . bin2hex(random_bytes(16));

        File::ensureDirectoryExists($temporaryRoot);

        try {
            $this->extractor->extract($archivePath, $extractionDirectory);
            $languageRoot = $this->rootResolver->resolve($extractionDirectory);

            return $this->installer->installFromDirectory($languageRoot, true);
        } finally {
            if (is_dir($extractionDirectory)) {
                File::deleteDirectory($extractionDirectory);
            }
        }
    }
}