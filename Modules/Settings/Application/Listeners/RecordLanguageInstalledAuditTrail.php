<?php

declare(strict_types=1);

namespace Modules\Settings\Application\Listeners;

use App\Core\Audit\Services\AuditLogger;
use Modules\Settings\Domain\Events\LanguageInstalledFromSettingsEvent;

final class RecordLanguageInstalledAuditTrail
{
    public function __construct(
        private readonly AuditLogger $logger,
    ) {
    }

    public function handle(LanguageInstalledFromSettingsEvent $event): void
    {
        $this->logger->log(
            'settings.language.installed',
            null,
            [
                'code' => $event->code,
                'name' => $event->name,
                'native_name' => $event->nativeName,
                'version' => $event->version,
            ],
        );
    }
}