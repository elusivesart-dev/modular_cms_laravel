<?php

declare(strict_types=1);

namespace Modules\Settings\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Settings\Infrastructure\Models\Setting;

final class SettingUpdatedEvent
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public Setting $setting)
    {
    }
}