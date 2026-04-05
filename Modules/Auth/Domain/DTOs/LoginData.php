<?php

declare(strict_types=1);

namespace Modules\Auth\Domain\DTOs;

final readonly class LoginData
{
    public function __construct(
        public string $email,
        public string $password,
        public bool $remember = false,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            email: (string) $data['email'],
            password: (string) $data['password'],
            remember: (bool) ($data['remember'] ?? false),
        );
    }
}