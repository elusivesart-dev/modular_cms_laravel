<?php

declare(strict_types=1);

namespace Modules\Settings\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class SettingsGroupUpdatedEvent
{
    use Dispatchable;
    use SerializesModels;

    /**
     * @param array<string, mixed> $values
     */
    public function __construct(
        public string $group,
        public array $values,
    ) {
    }
}