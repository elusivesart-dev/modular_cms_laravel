<?php

declare(strict_types=1);

namespace App\Core\Localization\Support;

final class LocaleFlagResolver
{
    public function resolveAssetPath(string $locale): string
    {
        $normalized = strtolower(trim($locale));

        $map = [
            'en' => 'gb',
        ];

        $flagCode = $map[$normalized] ?? $normalized;
        $candidate = public_path('assets/flags/' . $flagCode . '.svg');

        if (is_file($candidate)) {
            return asset('assets/flags/' . $flagCode . '.svg');
        }

        return asset('assets/flags/default.svg');
    }
}