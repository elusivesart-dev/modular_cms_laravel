<?php

declare(strict_types=1);

namespace Modules\Settings\Application\Listeners;

use App\Core\Audit\Services\AuditLogger;
use Modules\Settings\Domain\Events\LanguageDeletedFromSettingsEvent;

final class RecordLanguageDeletedAuditTrail
{
    public function __construct(
        private readonly AuditLogger $logger,
    ) {
    }

    public function handle(LanguageDeletedFromSettingsEvent $event): void
    {
        $this->logger->log(
            'settings.language.deleted',
            null,
            [
                'code' => $event->code,
                'name' => $event->name,
                'native_name' => $event->nativeName,
            ],
        );
    }
}