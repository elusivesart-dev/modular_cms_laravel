<?php

declare(strict_types=1);

namespace App\Core\Localization\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class DefaultLocaleChangedEvent
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly ?string $oldLocale,
        public readonly string $newLocale,
    ) {
    }
}