<?php

declare(strict_types=1);

namespace App\Core\Localization\Events;

use App\Core\Localization\Models\Language;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class LanguageDeletedEvent
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly string $code,
        public readonly ?Language $language = null,
    ) {
    }
}