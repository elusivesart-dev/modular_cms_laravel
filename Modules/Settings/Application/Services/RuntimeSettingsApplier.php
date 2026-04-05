<?php

declare(strict_types=1);

namespace Modules\Settings\Application\Services;

use App\Core\Localization\Contracts\LanguageRegistryInterface;
use Illuminate\Contracts\Foundation\Application;
use Modules\Settings\Domain\Contracts\SettingRepositoryInterface;

final readonly class RuntimeSettingsApplier
{
    public function __construct(
        private SettingRepositoryInterface $settings,
        private Application $app,
        private LanguageRegistryInterface $languages,
    ) {
    }

    public function apply(?string $sessionLocale = null): void
    {
        $this->applySystem();
        $this->applyLocale($sessionLocale);
    }

    public function applySystem(): void
    {
        $runtime = $this->settings->getRuntimeSettings();

        $siteName = $this->stringValue($runtime->get('general.site_name'));
        $timezone = $this->normalizeTimezone($this->stringValue($runtime->get('system.timezone')));
        $mailFromName = $this->stringValue($runtime->get('mail.from_name'));
        $mailFromAddress = $this->stringValue($runtime->get('mail.from_address'));

        if ($siteName !== null) {
            config(['app.name' => $siteName]);
        }

        if ($timezone !== null) {
            config(['app.timezone' => $timezone]);
            date_default_timezone_set($timezone);
        }

        if ($mailFromAddress !== null) {
            config(['mail.from.address' => $mailFromAddress]);
        }

        if ($mailFromName !== null) {
            config(['mail.from.name' => $mailFromName]);
        }
    }

    public function applyLocale(?string $sessionLocale = null): void
    {
        $runtime = $this->settings->getRuntimeSettings();

        $normalizedSessionLocale = $this->normalizeLocale($this->stringValue($sessionLocale));
        $defaultLocale = $this->normalizeLocale(
            $this->stringValue($runtime->get('system.default_locale'))
        );

        $locale = $normalizedSessionLocale
            ?? $defaultLocale
            ?? $this->languages->getFallbackLocale();

        config(['app.locale' => $locale]);
        $this->app->setLocale($locale);
    }

    private function stringValue(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $normalized = trim((string) $value);

        return $normalized !== '' ? $normalized : null;
    }

    private function normalizeLocale(?string $locale): ?string
    {
        return $this->languages->normalize($locale);
    }

    private function normalizeTimezone(?string $timezone): ?string
    {
        if ($timezone === null) {
            return null;
        }

        return in_array($timezone, timezone_identifiers_list(), true) ? $timezone : null;
    }
}