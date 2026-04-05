<?php

declare(strict_types=1);

namespace Modules\Users\Domain\DTOs;

final readonly class CreateUserData
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public bool $isActive = true,
    ) {
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'is_active' => $this->isActive,
        ];
    }
}