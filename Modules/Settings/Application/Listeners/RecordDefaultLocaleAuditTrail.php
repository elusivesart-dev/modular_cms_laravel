<?php

declare(strict_types=1);

namespace Modules\Settings\Application\Listeners;

use App\Core\Audit\Services\AuditLogger;
use Modules\Settings\Domain\Events\DefaultLocaleChangedEvent;

final class RecordDefaultLocaleAuditTrail
{
    public function __construct(
        private readonly AuditLogger $logger,
    ) {
    }

    public function handle(DefaultLocaleChangedEvent $event): void
    {
        $this->logger->log(
            'settings.locale.changed',
            null,
            [
                'old_locale' => $event->oldLocale,
                'new_locale' => $event->newLocale,
            ],
        );
    }
}