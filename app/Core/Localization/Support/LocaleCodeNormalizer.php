<?php

declare(strict_types=1);

namespace App\Core\Localization\Support;

final class LocaleCodeNormalizer
{
    public function normalize(?string $locale): ?string
    {
        if ($locale === null) {
            return null;
        }

        $locale = trim($locale);

        if ($locale === '') {
            return null;
        }

        $locale = str_replace('_', '-', $locale);
        $parts = array_values(array_filter(explode('-', $locale), static fn (string $part): bool => $part !== ''));

        if ($parts === []) {
            return null;
        }

        $language = strtolower($parts[0]);

        if (!preg_match('/^[a-z]{2,8}$/', $language)) {
            return null;
        }

        if (!isset($parts[1])) {
            return $language;
        }

        $region = strtoupper($parts[1]);

        if (!preg_match('/^[A-Z0-9]{2,8}$/', $region)) {
            return null;
        }

        return $language . '-' . $region;
    }
}