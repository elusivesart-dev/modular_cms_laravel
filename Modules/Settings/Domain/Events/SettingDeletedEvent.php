<?php

declare(strict_types=1);

namespace Modules\Settings\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class SettingDeletedEvent
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public int $settingId,
        public string $group,
        public string $key,
    ) {
    }
}