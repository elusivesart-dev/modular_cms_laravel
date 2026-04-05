<?php

declare(strict_types=1);

namespace App\Core\Localization\Registry;

use App\Core\Localization\Contracts\LanguageRegistryInterface;
use App\Core\Localization\Contracts\LanguageRepositoryInterface;
use App\Core\Localization\DTO\LanguageData;
use App\Core\Localization\Exceptions\UnsupportedLocaleException;
use App\Core\Localization\Support\LocaleCodeNormalizer;
use App\Core\Localization\Support\LocaleLabelResolver;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

final readonly class LanguageRegistry implements LanguageRegistryInterface
{
    public function __construct(
        private LanguageRepositoryInterface $languages,
        private LocaleCodeNormalizer $normalizer,
        private LocaleLabelResolver $labelResolver,
    ) {
    }

    /**
     * @return Collection<int, LanguageData>
     */
    public function getAvailableLanguages(): Collection
    {
        $cacheEnabled = (bool) config('localization.cache.enabled', true);
        $cacheKey = (string) config('localization.cache.key', 'core.localization.available_languages');
        $ttl = (int) config('localization.cache.ttl', 3600);

        if (!$cacheEnabled) {
            return $this->resolveAvailableLanguages();
        }

        /** @var Collection<int, LanguageData> $languages */
        $languages = Cache::remember($cacheKey, $ttl, fn (): Collection => $this->resolveAvailableLanguages());

        return $languages;
    }

    /**
     * @return array<string, string>
     */
    public function getDropdownOptions(): array
    {
        $options = [];

        foreach ($this->getAvailableLanguages() as $language) {
            $options[$language->code] = $this->labelResolver->resolve($language);
        }

        return $options;
    }

    public function isSupported(?string $locale): bool
    {
        return $this->getByCode($locale) !== null;
    }

    public function normalize(?string $locale): ?string
    {
        $locale = $this->normalizer->normalize($locale);

        if ($locale === null) {
            return null;
        }

        return $this->isSupported($locale) ? $locale : null;
    }

    public function assertSupported(?string $locale): void
    {
        if ($this->normalize($locale) === null) {
            throw new UnsupportedLocaleException('Unsupported locale: ' . (string) $locale);
        }
    }

    public function getByCode(?string $locale): ?LanguageData
    {
        $normalized = $this->normalizer->normalize($locale);

        if ($normalized === null) {
            return null;
        }

        return $this->getAvailableLanguages()
            ->first(static fn (LanguageData $language): bool => $language->code === $normalized);
    }

    public function getFallbackLocale(): string
    {
        $fallback = $this->normalizer->normalize((string) config('localization.fallback_locale', 'en'));

        if ($fallback !== null && $this->isSupported($fallback)) {
            return $fallback;
        }

        $first = $this->getAvailableLanguages()->first();

        return $first?->code ?? 'en';
    }

    /**
     * @return Collection<int, LanguageData>
     */
    private function resolveAvailableLanguages(): Collection
    {
        $fromDatabase = $this->languages->getActive()
            ->map(fn ($language): LanguageData => new LanguageData(
                code: (string) $language->code,
                name: (string) $language->name,
                nativeName: (string) $language->native_name,
                direction: (string) $language->direction,
                version: $language->version !== null ? (string) $language->version : null,
                installedPath: $language->installed_path !== null ? (string) $language->installed_path : null,
                isActive: (bool) $language->is_active,
                isSystem: (bool) $language->is_system,
            ))
            ->values();

        if ($fromDatabase->isNotEmpty()) {
            return $fromDatabase;
        }

        /** @var array<string, array<string, mixed>> $fallback */
        $fallback = (array) config('localization.fallback_languages', []);

        return collect($fallback)
            ->map(function (array $language, string $code): LanguageData {
                $language['code'] = $code;

                return LanguageData::fromArray($language);
            })
            ->filter(static fn (LanguageData $language): bool => $language->isActive)
            ->sortBy(static fn (LanguageData $language): string => $language->nativeName)
            ->values();
    }
}