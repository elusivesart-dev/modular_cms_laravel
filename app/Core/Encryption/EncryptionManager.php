<?php

declare(strict_types=1);

namespace App\Core\Encryption;

use Illuminate\Contracts\Encryption\Encrypter;

final class EncryptionManager
{
    public function __construct(
        private readonly Encrypter $encrypter
    ) {}

    public function register(): void
    {
        $this->encrypter->getKey();
    }
}