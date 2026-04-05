<?php

declare(strict_types=1);

namespace App\Core\Localization\Listeners;

use App\Core\Localization\Events\DefaultLocaleChangedEvent;
use App\Core\Localization\Services\LocalizationAuditLogger;

final readonly class LogDefaultLocaleChanged
{
    public function __construct(
        private LocalizationAuditLogger $audit,
    ) {
    }

    public function handle(DefaultLocaleChangedEvent $event): void
    {
        $this->audit->logDefaultLocaleChanged(
            $event->oldLocale,
            $event->newLocale,
        );
    }
}