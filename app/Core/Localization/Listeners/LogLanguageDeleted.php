<?php

declare(strict_types=1);

namespace App\Core\Localization\Listeners;

use App\Core\Localization\Events\LanguageDeletedEvent;
use App\Core\Localization\Services\LocalizationAuditLogger;

final readonly class LogLanguageDeleted
{
    public function __construct(
        private LocalizationAuditLogger $audit,
    ) {
    }

    public function handle(LanguageDeletedEvent $event): void
    {
        $this->audit->logLanguageDeleted(
            $event->code,
            $event->language,
        );
    }
}