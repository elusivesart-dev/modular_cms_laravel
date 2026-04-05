<?php

declare(strict_types=1);

namespace App\Core\Localization\Contracts;

use App\Core\Localization\DTO\LanguageData;
use Illuminate\Support\Collection;

interface LanguageRegistryInterface
{
    /**
     * @return Collection<int, LanguageData>
     */
    public function getAvailableLanguages(): Collection;

    /**
     * @return array<string, string>
     */
    public function getDropdownOptions(): array;

    public function isSupported(?string $locale): bool;

    public function normalize(?string $locale): ?string;

    public function assertSupported(?string $locale): void;

    public function getByCode(?string $locale): ?LanguageData;

    public function getFallbackLocale(): string;
}