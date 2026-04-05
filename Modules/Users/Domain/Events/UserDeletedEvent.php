<?php

declare(strict_types=1);

namespace Modules\Users\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class UserDeletedEvent
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public int $userId,
        public string $name,
        public string $email,
    ) {
    }
}