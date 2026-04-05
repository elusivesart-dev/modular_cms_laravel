<?php

declare(strict_types=1);

namespace App\Core\Localization\Services;

use App\Core\Localization\Contracts\LanguageDeleterInterface;
use App\Core\Localization\Contracts\LanguageRegistryInterface;
use App\Core\Localization\Contracts\LanguageRepositoryInterface;
use App\Core\Localization\Exceptions\LanguageDeletionException;
use App\Core\Localization\Exceptions\LanguageNotFoundException;
use App\Core\Localization\Support\LocaleCodeNormalizer;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

final readonly class LanguageDeleter implements LanguageDeleterInterface
{
    public function __construct(
        private LanguageRepositoryInterface $languages,
        private LanguageRegistryInterface $registry,
        private LocaleCodeNormalizer $normalizer,
    ) {
    }

    public function deleteByCode(string $code, ?string $currentDefaultLocale = null): void
    {
        $normalizedCode = $this->normalizer->normalize($code);

        if ($normalizedCode === null) {
            throw new LanguageNotFoundException('Language not found.');
        }

        $language = $this->languages->findByCode($normalizedCode);

        if ($language === null) {
            throw new LanguageNotFoundException('Language not found.');
        }

        if ((bool) $language->is_system) {
            throw new LanguageDeletionException('System languages cannot be deleted.');
        }

        $normalizedDefaultLocale = $this->normalizer->normalize($currentDefaultLocale);
        if ($normalizedDefaultLocale !== null && $normalizedDefaultLocale === $normalizedCode) {
            throw new LanguageDeletionException('The current default site language cannot be deleted.');
        }

        $fallbackLocale = $this->normalizer->normalize($this->registry->getFallbackLocale());
        if ($fallbackLocale !== null && $fallbackLocale === $normalizedCode) {
            throw new LanguageDeletionException('The fallback language cannot be deleted.');
        }

        if ($this->languages->getAll()->count() <= 1) {
            throw new LanguageDeletionException('The last installed language cannot be deleted.');
        }

        DB::transaction(function () use ($language): void {
            $installedPath = $language->installed_path !== null ? (string) $language->installed_path : null;

            $language->delete();

            if ($installedPath !== null && $installedPath !== '') {
                $this->deleteInstalledDirectory($installedPath);
            }
        });

        Cache::forget((string) config('localization.cache.key', 'core.localization.available_languages'));
    }

    private function deleteInstalledDirectory(string $installedPath): void
    {
        $baseInstalledPath = realpath((string) config('localization.paths.packages_installed'));

        if ($baseInstalledPath === false) {
            return;
        }

        $resolvedInstalledPath = realpath($installedPath);

        if ($resolvedInstalledPath === false) {
            return;
        }

        $basePrefix = rtrim($baseInstalledPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        if (!str_starts_with($resolvedInstalledPath, $basePrefix)) {
            throw new LanguageDeletionException('Refusing to delete a path outside the languages directory.');
        }

        if (!is_dir($resolvedInstalledPath)) {
            return;
        }

        File::deleteDirectory($resolvedInstalledPath);
    }
}