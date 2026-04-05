<?php

declare(strict_types=1);

namespace App\Core\Localization\Contracts;

use App\Core\Localization\DTO\LanguageManifestData;
use App\Core\Localization\Models\Language;
use Illuminate\Support\Collection;

interface LanguageRepositoryInterface
{
    /**
     * @return Collection<int, Language>
     */
    public function getActive(): Collection;

    /**
     * @return Collection<int, Language>
     */
    public function getAll(): Collection;

    public function findByCode(string $code): ?Language;

    public function findActiveByCode(string $code): ?Language;

    public function upsertFromManifest(
        LanguageManifestData $manifest,
        ?string $installedPath = null,
        bool $isSystem = false,
        bool $isActive = true,
    ): Language;
}