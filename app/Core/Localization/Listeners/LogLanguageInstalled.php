<?php

declare(strict_types=1);

namespace App\Core\Localization\Listeners;

use App\Core\Localization\Events\LanguageInstalledEvent;
use App\Core\Localization\Services\LocalizationAuditLogger;

final readonly class LogLanguageInstalled
{
    public function __construct(
        private LocalizationAuditLogger $audit,
    ) {
    }

    public function handle(LanguageInstalledEvent $event): void
    {
        $this->audit->logLanguageInstalled($event->language);
    }
}