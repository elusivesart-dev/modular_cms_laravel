<?php

declare(strict_types=1);

namespace Modules\Users\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Users\Infrastructure\Models\User;

final class UserCreatedEvent
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public User $user)
    {
    }
}