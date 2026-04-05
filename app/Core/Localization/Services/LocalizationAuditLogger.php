<?php

declare(strict_types=1);

namespace App\Core\Localization\Services;

use App\Core\Audit\Services\AuditLogger;
use App\Core\Localization\Models\Language;

final readonly class LocalizationAuditLogger
{
    public function __construct(
        private AuditLogger $logger,
    ) {
    }

    public function logDefaultLocaleChanged(?string $oldLocale, string $newLocale): void
    {
        $this->logger->log(
            'settings.locale.changed',
            null,
            [
                'old_locale' => $oldLocale,
                'new_locale' => $newLocale,
            ],
        );
    }

    public function logLanguageInstalled(Language $language): void
    {
        $this->logger->log(
            'settings.language.installed',
            $language,
            [
                'code' => (string) $language->code,
                'name' => (string) $language->name,
                'native_name' => (string) $language->native_name,
                'version' => $language->version !== null ? (string) $language->version : null,
            ],
        );
    }

    public function logLanguageDeleted(string $code, ?Language $language = null): void
    {
        $this->logger->log(
            'settings.language.deleted',
            null,
            [
                'code' => $code,
                'name' => $language?->name !== null ? (string) $language->name : null,
                'native_name' => $language?->native_name !== null ? (string) $language->native_name : null,
            ],
        );
    }
}