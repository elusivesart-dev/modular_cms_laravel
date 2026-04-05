<?php

declare(strict_types=1);

namespace App\Core\Localization\Infrastructure\Persistence;

use App\Core\Localization\Contracts\LanguageRepositoryInterface;
use App\Core\Localization\DTO\LanguageManifestData;
use App\Core\Localization\Models\Language;
use Illuminate\Support\Collection;

final class LanguageRepository implements LanguageRepositoryInterface
{
    /**
     * @return Collection<int, Language>
     */
    public function getActive(): Collection
    {
        return Language::query()
            ->where('is_active', true)
            ->orderBy('native_name')
            ->get();
    }

    /**
     * @return Collection<int, Language>
     */
    public function getAll(): Collection
    {
        return Language::query()
            ->orderByDesc('is_system')
            ->orderBy('native_name')
            ->get();
    }

    public function findByCode(string $code): ?Language
    {
        return Language::query()
            ->where('code', $code)
            ->first();
    }

    public function findActiveByCode(string $code): ?Language
    {
        return Language::query()
            ->where('code', $code)
            ->where('is_active', true)
            ->first();
    }

    public function upsertFromManifest(
        LanguageManifestData $manifest,
        ?string $installedPath = null,
        bool $isSystem = false,
        bool $isActive = true,
    ): Language {
        $language = Language::query()->firstOrNew([
            'code' => $manifest->code,
        ]);

        $language->fill([
            'name' => $manifest->name,
            'native_name' => $manifest->nativeName,
            'direction' => $manifest->direction,
            'version' => $manifest->version,
            'installed_path' => $installedPath,
            'is_active' => $isActive,
            'is_system' => $isSystem,
        ]);

        $language->save();

        return $language->refresh();
    }
}