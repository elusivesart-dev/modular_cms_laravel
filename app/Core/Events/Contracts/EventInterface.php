<?php

declare(strict_types=1);

namespace App\Core\Events\Contracts;

interface EventInterface
{
    public function name(): string;

    public function payload(): array;
}