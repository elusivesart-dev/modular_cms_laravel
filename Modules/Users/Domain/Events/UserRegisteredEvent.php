<?php

declare(strict_types=1);

namespace Modules\Users\Domain\Events;

use App\Core\Events\Contracts\EventInterface;

final class UserRegisteredEvent implements EventInterface
{
    public function __construct(
        private readonly int $userId,
        private readonly string $email,
    ) {
    }

    public function name(): string
    {
        return 'users.registered';
    }

    public function payload(): array
    {
        return [
            'user_id' => $this->userId,
            'email' => $this->email,
        ];
    }
}